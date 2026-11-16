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
     * ตรวจสอบว่าผู้ใช้คนนี้เป็น SuperAdmin หรือไม่
     * เงื่อนไข: ID = 1 หรือ (ไม่มี tenant_id และอยู่ในกลุ่ม admin)
     */
    public function isSuperAdmin(): bool
    {
        return (int) $this->id === 1
            || (($this->attributes['tenant_id'] ?? null) === null && method_exists($this, 'inGroup') && $this->inGroup('admin'));
    }

    /**
     * ตรวจสอบว่าเป็น Admin หรือไม่ (ครอบคลุมทั้ง SuperAdmin ID=1 และ Tenant Admin คนอื่นๆ)
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin()
            || (method_exists($this, 'inGroup') && $this->inGroup('admin'));
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
