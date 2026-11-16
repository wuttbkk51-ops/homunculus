<?php

namespace App\Entities;

use CodeIgniter\Shield\Entities\User as ShieldUser;

/**
 * Entity สำหรับตัวแทนข้อมูล User ที่ขยายความสามารถจาก Shield User
 */
class User extends ShieldUser
{
    protected $casts = [
        'id'        => 'integer',
        'tenant_id' => '?integer',
        'active'    => 'boolean',
    ];

    /**
     * ตรวจสอบว่าผู้ใช้คนนี้เป็น SuperAdmin หรือไม่ (tenant_id = null)
     */
    public function isSuperAdmin(): bool
    {
        return ! isset($this->attributes['tenant_id']) || $this->attributes['tenant_id'] === null;
    }

    /**
     * ดึง Tenant ID ของผู้ใช้
     */
    public function getTenantId(): ?int
    {
        return isset($this->attributes['tenant_id']) && $this->attributes['tenant_id'] !== null
            ? (int) $this->attributes['tenant_id']
            : null;
    }
}
