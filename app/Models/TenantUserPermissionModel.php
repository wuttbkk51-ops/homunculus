<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Model จัดการสิทธิ์เฉพาะรายบุคคลระดับ granular (module.action.scope) ประจำ Tenant
 */
class TenantUserPermissionModel extends TenantModel
{
    protected $table            = 'tenant_user_permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'user_id',
        'permission',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // ไม่มี updated_at

    // Validation
    protected $validationRules = [
        'tenant_id'  => 'required|is_natural_no_zero',
        'user_id'    => 'required|is_natural_no_zero',
        'permission' => 'required|min_length[3]|max_length[100]',
    ];
}
