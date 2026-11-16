<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * Entity สำหรับตัวแทนข้อมูล Branch
 */
class Branch extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'        => 'integer',
        'tenant_id' => 'integer',
    ];
}
