<?php

namespace App\Models;

use App\Entities\Branch;

/**
 * Model สำหรับจัดการข้อมูลตาราง branches (สืบทอด BaseTenantModel เพื่อ Auto Tenant Scoping)
 */
class BranchModel extends BaseTenantModel
{
    protected $table          = 'branches';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = Branch::class;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'tenant_id',
        'name',
        'code',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'tenant_id' => 'required|is_natural_no_zero',
        'name'      => 'required|max_length[100]',
        'code'      => 'required|max_length[50]',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
