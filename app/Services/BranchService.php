<?php

namespace App\Services;

use App\Models\BranchModel;
use App\Models\UserBranchModel;

/**
 * Service สำหรับจัดการสาขา (Branches) และ Active Branch Context ใน Session
 */
class BranchService
{
    /**
     * ดึงรายการสาขาทั้งหมดที่ผู้ใช้ได้รับมอบหมายอย่างชัดเจน (Explicit Many-to-Many)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUserBranches(int $userId): array
    {
        /** @var UserBranchModel $userBranchModel */
        $userBranchModel = model(UserBranchModel::class);

        return $userBranchModel->getUserBranchesWithDetails($userId);
    }

    /**
     * ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงสาขานี้หรือไม่
     */
    public function hasBranchAccess(int $userId, int $branchId): bool
    {
        /** @var UserBranchModel $userBranchModel */
        $userBranchModel = model(UserBranchModel::class);

        $assignment = $userBranchModel->getAssignment($userId, $branchId);

        return $assignment !== null;
    }

    /**
     * ดึงบทบาทของผู้ใช้ในสาขานั้นๆ (branch_role เช่น manager, cashier)
     */
    public function getUserBranchRole(int $userId, int $branchId): ?string
    {
        /** @var UserBranchModel $userBranchModel */
        $userBranchModel = model(UserBranchModel::class);

        $assignment = $userBranchModel->getAssignment($userId, $branchId);

        return $assignment['branch_role'] ?? null;
    }

    /**
     * กำหนดสาขาที่กำลังทำงานอยู่ (Active Branch) ลงใน Session
     */
    public function setActiveBranch(int $userId, int $branchId): bool
    {
        if (! $this->hasBranchAccess($userId, $branchId)) {
            return false;
        }

        /** @var BranchModel $branchModel */
        $branchModel = model(BranchModel::class);
        $branch = $branchModel->find($branchId);

        if (! $branch) {
            return false;
        }

        $branchRole = $this->getUserBranchRole($userId, $branchId);

        session()->set('active_branch', [
            'id'          => (int) $branch->id,
            'name'        => $branch->name,
            'code'        => $branch->code,
            'tenant_id'   => (int) $branch->tenant_id,
            'branch_role' => $branchRole,
        ]);

        return true;
    }

    /**
     * อ่าน ID ของสาขาที่กำลัง Active อยู่ใน Session
     */
    public function getActiveBranchId(): ?int
    {
        $activeBranch = session()->get('active_branch');

        return isset($activeBranch['id']) ? (int) $activeBranch['id'] : null;
    }

    /**
     * อ่านข้อมูลสาขาที่กำลัง Active อยู่ใน Session
     *
     * @return array<string, mixed>|null
     */
    public function getActiveBranch(): ?array
    {
        return session()->get('active_branch');
    }

    /**
     * ล้างข้อมูลสาขา Active ออกจาก Session
     */
    public function clearActiveBranch(): void
    {
        session()->remove('active_branch');
    }
}
