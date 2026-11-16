<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model สำหรับจัดการข้อมูลตาราง user_branches (Pivot table)
 */
class UserBranchModel extends Model
{
    protected $table          = 'user_branches';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'branch_id',
        'branch_role',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'     => 'required|is_natural_no_zero',
        'branch_id'   => 'required|is_natural_no_zero',
        'branch_role' => 'required|max_length[50]',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * ดึงรายการสาขาทั้งหมดพร้อมข้อมูลสาขาที่ผู้ใช้สังกัด
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUserBranchesWithDetails(int $userId): array
    {
        return $this->select('user_branches.*, branches.name as branch_name, branches.code as branch_code, branches.tenant_id')
            ->join('branches', 'branches.id = user_branches.branch_id')
            ->where('user_branches.user_id', $userId)
            ->where('branches.deleted_at', null)
            ->findAll();
    }

    /**
     * ดึงข้อมูลการผูกสาขาเฉพาะรายการของ user และ branch
     */
    public function getAssignment(int $userId, int $branchId): ?array
    {
        return $this->where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->first();
    }
}
