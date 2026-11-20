<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Model จัดการข้อมูลตาราง people (Human Info แบบ Decoupled)
 *
 * สืบทอดจาก TenantModel และเปิด includeGlobal เพื่อให้เข้าถึงข้อมูลบุคคลระดับ Global (tenant_id = 0) ได้
 */
class PersonModel extends TenantModel
{
    protected $table            = 'people';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'metadata',
    ];

    /**
     * อนุญาตให้มองเห็นและอ้างอิงข้อมูลบุคคลระดับ Global (tenant_id = 0)
     */
    protected bool $includeGlobal = true;

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|min_length[1]|max_length[100]',
        'last_name'  => 'required|min_length[1]|max_length[100]',
        'phone'      => 'permit_empty|max_length[30]',
        'email'      => 'permit_empty|valid_email|max_length[100]',
    ];
}
