<?php

namespace App\Models;

use App\Entities\Tenant;
use CodeIgniter\Model;

/**
 * Model สำหรับจัดการข้อมูลตาราง tenants
 */
class TenantModel extends Model
{
    protected $table          = 'tenants';
    protected $primaryKey     = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = Tenant::class;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'name',
        'code',
        'status',
        'expire_date',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'        => 'required|max_length[100]',
        'code'        => 'required|max_length[50]|is_unique[tenants.code,id,{id}]',
        'status'      => 'required|in_list[active,suspended,expired]',
        'expire_date' => 'permit_empty|valid_date',
    ];

    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
