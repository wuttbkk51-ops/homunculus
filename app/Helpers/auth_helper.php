<?php

declare(strict_types=1);

/**
 * Auth Helper สำหรับระบบยืนยันตัวตนและการตรวจสอบสิทธิ์แบบ Contextual RBAC (Permission-First)
 */

use App\Entities\User;
use App\Services\AuthService;
use App\Services\BranchService;

if (! function_exists('auth_can')) {
    /**
     * ตรวจสอบสิทธิ์แบบ Contextual RBAC (Permission-First)
     *
     * @param string    $permission รหัสสิทธิ์ เช่น sale.view.branch, sale.*
     * @param User|null $user       Entity ผู้ใช้ (หากไม่ระบุจะใช้ผู้ใช้ปัจจุบัน)
     * @param int|null  $branchId   รหัสสาขา (หากไม่ระบุจะใช้ Active Branch ปัจจุบัน)
     */
    function auth_can(string $permission, ?User $user = null, ?int $branchId = null): bool
    {
        /** @var AuthService $authService */
        $authService = service('auth_service');

        return $authService->can($permission, $user, $branchId);
    }
}

if (! function_exists('active_branch_id')) {
    /**
     * ดึง ID ของสาขาที่ Active อยู่ในปัจจุบัน (Alias ของ branch_id())
     */
    function active_branch_id(): ?int
    {
        return branch_id();
    }
}

if (! function_exists('active_branch')) {
    /**
     * ดึงข้อมูลสาขาที่ Active อยู่ในปัจจุบัน
     *
     * @return array<string, mixed>|null
     */
    function active_branch(): ?array
    {
        return session()->get('active_branch');
    }
}

if (! function_exists('user_branches')) {
    /**
     * ดึงรายการสาขาทั้งหมดที่ผู้ใช้ปัจจุบันได้รับมอบหมาย (สำหรับแสดงใน Dropdown บน Navbar)
     *
     * @return array<int, array<string, mixed>>
     */
    function user_branches(): array
    {
        $user = auth()->user();
        if ($user === null) {
            return [];
        }

        /** @var BranchService $branchService */
        $branchService = service('branch_service');

        return $branchService->getUserBranches((int) $user->id);
    }
}

if (! function_exists('current_tenant_id')) {
    /**
     * ดึง ID ของ Tenant ประจำตัวผู้ใช้ที่ล็อกอินอยู่ (Alias ของ tenant_id())
     */
    function current_tenant_id(): ?int
    {
        return tenant_id();
    }
}

if (! function_exists('is_superadmin')) {
    /**
     * ตรวจสอบว่าผู้ใช้ปัจจุบันเป็น SuperAdmin หรือไม่
     */
    function is_superadmin(): bool
    {
        $user = auth()->user();
        if ($user === null) {
            return false;
        }

        if (method_exists($user, 'isSuperAdmin')) {
            return $user->isSuperAdmin();
        }

        $tenantId = $user->tenant_id ?? null;

        return (int) $user->id === 1
            || ($tenantId === null && method_exists($user, 'inGroup') && $user->inGroup('admin'));
    }
}
