<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (! function_exists('tenant_id')) {
    /**
     * ดึงรหัส Tenant ID ของผู้ใช้ปัจจุบัน
     * เข้าถึงจาก User Entity ในหน่วยความจำโดยตรง หรือดึงจาก Session Active Branch
     */
    function tenant_id(): ?int
    {
        $user = auth()->user();

        if ($user !== null) {
            if (method_exists($user, 'getTenantId')) {
                return $user->getTenantId();
            }

            if (isset($user->tenant_id) && $user->tenant_id !== null) {
                return (int) $user->tenant_id;
            }
        }

        $activeBranch = session()->get('active_branch');

        return isset($activeBranch['tenant_id']) ? (int) $activeBranch['tenant_id'] : null;
    }
}

if (! function_exists('branch_id')) {
    /**
     * ดึงรหัส Active Branch ID ปัจจุบันจาก Session ในหน่วยความจำ
     */
    function branch_id(): ?int
    {
        $activeBranch = session()->get('active_branch');

        return isset($activeBranch['id']) ? (int) $activeBranch['id'] : null;
    }
}

if (! function_exists('active_branch')) {
    /**
     * ดึงข้อมูลรายละเอียดของ Active Branch ปัจจุบัน
     *
     * @return array<string, mixed>|null ข้อมูลสาขา เช่น ['id' => 1, 'name' => 'สำนักงานใหญ่', 'code' => 'HQ', ...]
     */
    function active_branch(): ?array
    {
        return session()->get('active_branch');
    }
}

if (! function_exists('active_branch_role')) {
    /**
     * ดึงบทบาทของผู้ใช้ในสาขาปัจจุบัน (branch_role เช่น manager, cashier, staff)
     */
    function active_branch_role(): ?string
    {
        $activeBranch = session()->get('active_branch');

        return $activeBranch['branch_role'] ?? null;
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

        $tenantId = isset($user->tenant_id) ? $user->tenant_id : null;

        return (int) $user->id === 1
            || ($tenantId === null && method_exists($user, 'inGroup') && $user->inGroup('admin'));
    }
}

if (! function_exists('can')) {
    /**
     * ตรวจสอบสิทธิ์แบบ Contextual RBAC (Permission-First)
     * รูปแบบคีย์สิทธิ์: {module}.{action}.{scope} เช่น sale.view.own, sale.create.branch, sale.view.all
     *
     * @param string   $permission รหัสสิทธิ์ที่ต้องการตรวจสอบ
     * @param int|null $branchId   รหัสสาขาที่ต้องการตรวจสอบ (หากไม่ระบุจะใช้ Active Branch)
     */
    function can(string $permission, ?int $branchId = null): bool
    {
        /** @var \App\Services\AuthService $authService */
        $authService = service('auth_service');

        return $authService->can($permission, null, $branchId);
    }
}

if (! function_exists('user_branches')) {
    /**
     * ดึงรายการสาขาทั้งหมดที่ผู้ใช้ปัจจุบันได้รับมอบหมาย (สำหรับแสดงใน Dropdown เลือกสาขา)
     *
     * @return array<int, array<string, mixed>>
     */
    function user_branches(): array
    {
        $user = auth()->user();

        if ($user === null) {
            return [];
        }

        /** @var \App\Services\BranchService $branchService */
        $branchService = service('branch_service');

        return $branchService->getUserBranches((int) $user->id);
    }
}
