<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Model จัดการข้อมูลตาราง people (Human Info แบบ Decoupled ประจำ Tenant)
 *
 * สืบทอดจาก TenantModel เพื่อผูก auto-where tenant_id อัตโนมัติทุก action
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
        'tenant_id'  => 'permit_empty|is_natural_no_zero',
        'first_name' => 'required|min_length[1]|max_length[100]',
        'last_name'  => 'required|min_length[1]|max_length[100]',
        'phone'      => 'permit_empty|max_length[30]',
        'email'      => 'permit_empty|valid_email|max_length[100]',
    ];
}
