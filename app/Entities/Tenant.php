<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * Entity สำหรับตัวแทนข้อมูล Tenant
 */
class Tenant extends Entity
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'expire_date'];
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * ตรวจสอบว่า Tenant ยังคงมีสถานะ Active และยังไม่หมดอายุหรือไม่
     */
    public function isActive(): bool
    {
        if ($this->attributes['status'] !== 'active') {
            return false;
        }

        if (! empty($this->attributes['expire_date'])) {
            $now = date('Y-m-d H:i:s');
            if ($this->attributes['expire_date'] < $now) {
                return false;
            }
        }

        return true;
    }
}
