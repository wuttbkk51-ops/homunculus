<?php

namespace App\Models;

use App\Entities\Branch;
use CodeIgniter\Model;

/**
 * Model สำหรับจัดการข้อมูลตาราง branches
 */
class BranchModel extends Model
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

    /**
     * Scope สำหรับกรองข้อมูลเฉพาะ Tenant ที่ระบุ
     */
    public function forTenant(int $tenantId): self
    {
        return $this->where('tenant_id', $tenantId);
    }
}
