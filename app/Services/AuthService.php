<?php

namespace App\Services;

use App\Entities\User;
use Config\AuthGroups;

/**
 * Service สำหรับระบบยืนยันตัวตนและการตรวจสอบสิทธิ์แบบบริบท (Contextual RBAC & Permission-First)
 */
class AuthService
{
    protected TenantService $tenantService;
    protected BranchService $branchService;
    protected AuthGroups $authGroups;

    public function __construct(
        ?TenantService $tenantService = null,
        ?BranchService $branchService = null,
        ?AuthGroups $authGroups = null
    ) {
        $this->tenantService = $tenantService ?? new TenantService();
        $this->branchService = $branchService ?? new BranchService();
        $this->authGroups    = $authGroups ?? config('AuthGroups');
    }

    /**
     * ประมวลผลหลังการล็อกอินสำเร็จ เพื่อจัดเตรียม Tenant และ Branch Context
     *
     * @return array{status: string, redirect: string, message?: string}
     */
    public function handlePostLogin(?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user) {
            return [
                'status'   => 'unauthenticated',
                'redirect' => 'login',
            ];
        }

        // 1. กรณี SuperAdmin
        if ($user->isSuperAdmin()) {
            return [
                'status'   => 'success',
                'redirect' => 'admin',
            ];
        }

        $tenantId = $user->getTenantId();
        if ($tenantId === null) {
            auth()->logout();

            return [
                'status'   => 'error',
                'redirect' => 'login',
                'message'  => 'บัญชีผู้ใช้นี้ยังไม่ได้ถูกผูกกับองค์กร (Tenant)',
            ];
        }

        // 2. ตรวจสอบสถานะ Tenant จาก Cache
        if (! $this->tenantService->isTenantActive($tenantId)) {
            auth()->logout();

            return [
                'status'   => 'tenant_inactive',
                'redirect' => 'auth/tenant-suspended',
                'message'  => 'บัญชีองค์กรของท่านถูกระงับหรือหมดอายุการใช้งาน',
            ];
        }

        // 3. ตรวจสอบสาขาที่ได้รับมอบหมาย
        $branches = $this->branchService->getUserBranches((int) $user->id);

        if (empty($branches)) {
            auth()->logout();

            return [
                'status'   => 'no_branch',
                'redirect' => 'login',
                'message'  => 'บัญชีผู้ใช้นี้ยังไม่ได้รับมอบหมายสาขาในการทำงาน กรุณาติดต่อผู้ดูแลระบบ',
            ];
        }

        // ถ้ามีสาขาเดียว ตั้งค่า Active Branch อัตโนมัติ
        if (count($branches) === 1) {
            $this->branchService->setActiveBranch((int) $user->id, (int) $branches[0]['branch_id']);

            return [
                'status'   => 'success',
                'redirect' => 'admin',
            ];
        }

        // ถ้ามีหลายสาขา ให้ไปที่หน้าเลือกสาขา
        return [
            'status'   => 'select_branch',
            'redirect' => 'auth/select-branch',
        ];
    }

    /**
     * ตรวจสอบสิทธิ์แบบ Permission-First ตามหลัก Contextual RBAC
     * รูปแบบคีย์สิทธิ์: {module}.{action}.{scope} เช่น sale.view.own, sale.create.branch, sale.view.all
     */
    public function can(string $requestedPermission, ?User $user = null, ?int $branchId = null): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            return false;
        }

        // SuperAdmin ได้รับสิทธิ์ทั้งหมด
        if ($user->isSuperAdmin()) {
            return true;
        }

        $tenantId = $user->getTenantId();
        if ($tenantId === null || ! $this->tenantService->isTenantActive($tenantId)) {
            return false;
        }

        // รวบรวมสิทธิ์ที่ผู้ใช้ได้รับ (Granted Permissions)
        $grantedPermissions = $this->getUserPermissions($user, $branchId);

        // ตรวจสอบกับ requested permission
        foreach ($grantedPermissions as $granted) {
            if ($this->matchPermission($granted, $requestedPermission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * รวบรวมสิทธิ์ทั้งหมดของผู้ใช้ ทั้งจาก Branch Role ในสาขาที่ Active และ Tenant Groups
     *
     * @return list<string>
     */
    public function getUserPermissions(User $user, ?int $branchId = null): array
    {
        $branchId ??= $this->branchService->getActiveBranchId();
        $permissions = [];

        // 1. สิทธิ์จาก Contextual Branch Role
        if ($branchId !== null) {
            $branchRole = $this->branchService->getUserBranchRole((int) $user->id, $branchId);
            if ($branchRole && isset($this->authGroups->matrix[$branchRole])) {
                $permissions = array_merge($permissions, $this->authGroups->matrix[$branchRole]);
            }
        }

        // 2. สิทธิ์จาก Groups ทั่วไปของ Shield
        if (method_exists($user, 'getGroups')) {
            try {
                $groups = $user->getGroups();
                foreach ($groups as $group) {
                    if (isset($this->authGroups->matrix[$group])) {
                        $permissions = array_merge($permissions, $this->authGroups->matrix[$group]);
                    }
                }
            } catch (\Throwable) {
                // ข้ามหากยังไม่มีตาราง auth_groups_users ในฐานข้อมูล
            }
        }

        // 3. สิทธิ์ที่กำหนดให้ผู้ใช้โดยตรง (Direct permissions)
        if (method_exists($user, 'getPermissions')) {
            try {
                $permissions = array_merge($permissions, $user->getPermissions());
            } catch (\Throwable) {
                // ข้ามหากยังไม่มีตาราง auth_permissions_users ในฐานข้อมูล
            }
        }

        return array_values(array_unique($permissions));
    }

    /**
     * ตรวจสอบว่าสิทธิ์ที่ได้รับ (Granted) ครอบคลุมสิทธิ์ที่ร้องขอ (Requested) หรือไม่
     * รองรับ Wildcard (*) และ Scope Hierarchy (.all > .branch > .own)
     */
    public function matchPermission(string $granted, string $requested): bool
    {
        // 1. กรณี Granted เป็น Wildcard สูงสุด
        if ($granted === '*' || $granted === $requested) {
            return true;
        }

        $grantedParts   = explode('.', $granted);
        $requestedParts = explode('.', $requested);

        // จัดรูปแบบให้อยู่ในรูป [module, action, scope]
        $gMod   = $grantedParts[0] ?? '*';
        $gAct   = $grantedParts[1] ?? '*';
        $gScope = $grantedParts[2] ?? '*';

        $rMod   = $requestedParts[0] ?? '*';
        $rAct   = $requestedParts[1] ?? '*';
        $rScope = $requestedParts[2] ?? '*';

        // ตรวจสอบ Module
        if ($gMod !== '*' && $gMod !== $rMod) {
            return false;
        }

        // ตรวจสอบ Action
        if ($gAct !== '*' && $gAct !== $rAct) {
            return false;
        }

        // ตรวจสอบ Scope ตามลำดับชั้น (Hierarchy: all > branch > own)
        return $this->matchScope($gScope, $rScope);
    }

    /**
     * เปรียบเทียบ Scope ตามลำดับชั้นความคุ้มครอง
     */
    protected function matchScope(string $grantedScope, string $requestedScope): bool
    {
        if ($grantedScope === '*' || $grantedScope === 'all') {
            return true;
        }

        if ($grantedScope === 'branch') {
            return in_array($requestedScope, ['branch', 'own', '*'], true);
        }

        if ($grantedScope === 'own') {
            return in_array($requestedScope, ['own', '*'], true);
        }

        return $grantedScope === $requestedScope;
    }
}
