<?php

use App\Entities\User;
use App\Services\AuthService;
use App\Services\BranchService;

if (! function_exists('auth_can')) {
    /**
     * ตรวจสอบสิทธิ์แบบ Contextual RBAC (Permission-First)
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
     * ดึง ID ของสาขาที่ Active อยู่ในปัจจุบัน
     */
    function active_branch_id(): ?int
    {
        /** @var BranchService $branchService */
        $branchService = service('branch_service');

        return $branchService->getActiveBranchId();
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
        /** @var BranchService $branchService */
        $branchService = service('branch_service');

        return $branchService->getActiveBranch();
    }
}

if (! function_exists('current_tenant_id')) {
    /**
     * ดึง ID ของ Tenant ประจำตัวผู้ใช้ที่ล็อกอินอยู่
     */
    function current_tenant_id(): ?int
    {
        $user = auth()->user();

        return $user ? $user->getTenantId() : null;
    }
}

if (! function_exists('is_superadmin')) {
    /**
     * ตรวจสอบว่าผู้ใช้ปัจจุบันเป็น SuperAdmin หรือไม่
     */
    function is_superadmin(): bool
    {
        $user = auth()->user();

        return $user ? $user->isSuperAdmin() : false;
    }
}
