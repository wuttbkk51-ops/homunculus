<?php

namespace App\Services;

use App\Entities\User;
use CodeIgniter\Shield\Entities\User as ShieldUser;
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
     * @param User|ShieldUser|null $user
     * @return array{status: string, redirect: string, message?: string}
     */
    public function handlePostLogin($user = null): array
    {
        $user ??= auth()->user();

        if (! $user) {
            return [
                'status'   => 'unauthenticated',
                'redirect' => 'login',
            ];
        }

        // 1. กรณี SuperAdmin
        $isSuperAdmin = method_exists($user, 'isSuperAdmin')
            ? $user->isSuperAdmin()
            : ((int) $user->id === 1 || ($user->tenant_id === null && method_exists($user, 'inGroup') && $user->inGroup('admin')));

        if ($isSuperAdmin) {
            return [
                'status'   => 'success',
                'redirect' => 'dashboard',
            ];
        }

        $tenantId = method_exists($user, 'getTenantId') ? $user->getTenantId() : ($user->tenant_id ?? null);
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

        // 3. กำหนดสาขาเริ่มต้นให้อัตโนมัติ (Seamless UX - ไม่ต้องมีหน้าเลือกสาขาคั่น)
        $defaultBranch = $this->branchService->autoSelectDefaultBranch((int) $user->id);

        if ($defaultBranch === null) {
            auth()->logout();

            return [
                'status'   => 'no_branch',
                'redirect' => 'login',
                'message'  => 'บัญชีผู้ใช้นี้ยังไม่ได้รับมอบหมายสาขาในการทำงาน กรุณาติดต่อผู้ดูแลระบบ',
            ];
        }

        return [
            'status'   => 'success',
            'redirect' => 'dashboard',
        ];
    }

    /**
     * ตรวจสอบสิทธิ์แบบ Permission-First ตามหลัก Contextual RBAC
     * รูปแบบคีย์สิทธิ์: {module}.{action}.{scope} เช่น sale.view.own, sale.create.branch, sale.view.all
     *
     * @param User|ShieldUser|null $user
     */
    public function can(string $requestedPermission, $user = null, ?int $branchId = null): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            return false;
        }

        // SuperAdmin ได้รับสิทธิ์ทั้งหมด
        $isSuperAdmin = method_exists($user, 'isSuperAdmin')
            ? $user->isSuperAdmin()
            : ((int) $user->id === 1 || ($user->tenant_id === null && method_exists($user, 'inGroup') && $user->inGroup('admin')));

        if ($isSuperAdmin) {
            return true;
        }

        $tenantId = method_exists($user, 'getTenantId') ? $user->getTenantId() : ($user->tenant_id ?? null);
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
     * ดำเนินการผ่าน CI4 Cache เพื่อให้ Zero DB Query per Request
     *
     * @param User|ShieldUser $user
     * @return list<string>
     */
    public function getUserPermissions($user, ?int $branchId = null): array
    {
        $branchId ??= $this->branchService->getActiveBranchId();
        $cacheKey = "user_perms_{$user->id}_" . ($branchId ?? 'global');

        return cache()->remember($cacheKey, 3600, function () use ($user, $branchId): array {
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
        });
    }

    /**
     * ล้างแคชสิทธิ์ของผู้ใช้ (เรียกใช้เมื่อมีการแก้ไข Role/Permissions หรือสลับสาขา)
     */
    public function clearUserPermissionsCache(int $userId, ?int $branchId = null): void
    {
        if ($branchId !== null) {
            cache()->delete("user_perms_{$userId}_{$branchId}");
        } else {
            cache()->delete("user_perms_{$userId}_global");
        }
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
        if ($gMod !== '*' && $rMod !== '*' && $gMod !== $rMod) {
            return false;
        }

        // ตรวจสอบ Action
        if ($gAct !== '*' && $rAct !== '*' && $gAct !== $rAct) {
            return false;
        }

        // หาก Scope ฝั่งใดฝั่งหนึ่งเป็น Wildcard ถือว่าเข้าเงื่อนไข
        if ($gScope === '*' || $rScope === '*') {
            return true;
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
