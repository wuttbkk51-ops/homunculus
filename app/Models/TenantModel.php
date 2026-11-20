<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

/**
 * Base Tenant Model
 *
 * ผูก auto where tenant_id ให้อัตโนมัติทุก action (find, insert, update, delete)
 * และรองรับการดึงข้อมูล Global (tenant_id = 0) ผ่าน Property $includeGlobal
 */
abstract class TenantModel extends Model
{
    /**
     * กำหนดว่าตารางนี้อนุญาตให้เข้าถึงข้อมูล Global (tenant_id = 0) ด้วยหรือไม่
     */
    protected bool $includeGlobal = false;

    /**
     * Active Tenant ID ประจำ Request ปัจจุบัน
     */
    protected static ?int $currentTenantId = null;

    /**
     * Flag สำหรับข้ามการเช็ค Tenant (เช่น งานเบื้องหลังระดับ Platform หรือ System Admin)
     */
    protected static bool $bypassTenant = false;

    protected $beforeFind   = ['applyTenantScope'];
    protected $beforeInsert = ['applyTenantInsert'];
    protected $beforeUpdate = ['applyTenantScope'];
    protected $beforeDelete = ['applyTenantScope'];

    /**
     * ตั้งค่า Active Tenant ID
     */
    public static function setCurrentTenantId(?int $tenantId): void
    {
        static::$currentTenantId = $tenantId;
    }

    /**
     * ดึงค่า Active Tenant ID ปัจจุบัน
     */
    public static function getCurrentTenantId(): ?int
    {
        return static::$currentTenantId;
    }

    /**
     * รันโค้ดโดยข้ามการเช็ค tenant_id ชั่วคราว
     */
    public static function withoutTenant(callable $callback): mixed
    {
        $previousState        = static::$bypassTenant;
        static::$bypassTenant = true;

        try {
            return $callback();
        } finally {
            static::$bypassTenant = $previousState;
        }
    }

    /**
     * รันโค้ดภายใต้ Tenant Context ที่กำหนด
     */
    public static function forTenant(int $tenantId, callable $callback): mixed
    {
        $previousTenant = static::$currentTenantId;
        static::setCurrentTenantId($tenantId);

        try {
            return $callback();
        } finally {
            static::setCurrentTenantId($previousTenant);
        }
    }

    /**
     * Callback อัตโนมัติสำหรับดักเงื่อนไข tenant_id ในการ Query
     */
    protected function applyTenantScope(array $data): array
    {
        if (static::$bypassTenant) {
            return $data;
        }

        $tenantId = static::getCurrentTenantId();

        // หากไม่มี tenant context ถูกกำหนด
        if ($tenantId === null) {
            if ($this->includeGlobal) {
                $this->where($this->table . '.tenant_id', 0);
            } else {
                // ป้องกัน data leak กรณีไม่มี context ให้จำกัดไม่ให้เห็นข้อมูลใด
                $this->where($this->table . '.tenant_id', 0);
            }

            return $data;
        }

        if ($this->includeGlobal) {
            $this->whereIn($this->table . '.tenant_id', [$tenantId, 0]);
        } else {
            $this->where($this->table . '.tenant_id', $tenantId);
        }

        return $data;
    }

    /**
     * Callback อัตโนมัติสำหรับใส่ tenant_id ตอน Insert ข้อมูลใหม่
     */
    protected function applyTenantInsert(array $data): array
    {
        if (! isset($data['data']['tenant_id'])) {
            $tenantId = static::getCurrentTenantId();

            if ($tenantId !== null) {
                $data['data']['tenant_id'] = $tenantId;
            } elseif (! $this->includeGlobal) {
                throw new RuntimeException('ไม่สามารถบันทึกข้อมูลได้เนื่องจากไม่มี Active Tenant ID');
            }
        }

        return $data;
    }
}
