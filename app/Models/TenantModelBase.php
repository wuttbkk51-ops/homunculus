<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model จัดการข้อมูลตาราง tenants (ระดับระบบ)
 */
class TenantModelBase extends Model
{
    protected $table            = 'tenants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'name',
        'slug',
        'status',
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
        'uuid'   => 'required|max_length[36]|is_unique[tenants.uuid,id,{id}]',
        'name'   => 'required|min_length[2]|max_length[100]',
        'slug'   => 'required|min_length[2]|max_length[50]|alpha_dash|is_unique[tenants.slug,id,{id}]',
        'status' => 'permit_empty|in_list[active,suspended,inactive]',
    ];

    protected $validationMessages = [
        'slug' => [
            'is_unique' => 'Slug ของ Tenant นี้ถูกใช้งานแล้ว',
        ],
        'uuid' => [
            'is_unique' => 'UUID นี้ถูกใช้งานแล้ว',
        ],
    ];
}
