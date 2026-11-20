<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Model จัดการความสัมพันธ์ระหว่าง User กับ Tenant
 */
class TenantUserModel extends TenantModel
{
    protected $table            = 'tenant_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'user_id',
        'person_id',
        'role',
        'status',
        'last_active_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tenant_id' => 'required|is_natural_no_zero',
        'user_id'   => 'required|is_natural_no_zero',
        'person_id' => 'permit_empty|is_natural_no_zero',
        'role'      => 'required|max_length[50]',
        'status'    => 'permit_empty|in_list[active,suspended,inactive]',
    ];
}
