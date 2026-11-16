<?php

namespace App\Models\Traits;

/**
 * Trait สำหรับจัดการ Multi-Tenant Query Scoping และ Auto-Injection อัตโนมัติ
 * ป้องกันปัญหา Developer ลืมใส่ WHERE tenant_id หรือลืมใส่ tenant_id ในการ INSERT
 */
trait TenantScopedTrait
{
    /**
     * กำหนดว่า Query นี้จะถูก Scope ตาม Tenant หรือไม่
     */
    protected bool $scopedToTenant = true;

    /**
     * ปิดการกรอง Tenant ชั่วคราว (สำหรับ SuperAdmin หรือ Background Worker)
     */
    public function withoutTenant(): self
    {
        $this->scopedToTenant = false;

        return $this;
    }

    /**
     * กำหนดให้อ่านข้อมูลเฉพาะ Tenant ที่ระบุ
     */
    public function forTenant(int $tenantId): self
    {
        return $this->where($this->table . '.tenant_id', $tenantId);
    }

    /**
     * Auto-Inject tenant_id ก่อนทำการ Insert เสมอ (ป้องกันการปลอมแปลง tenant_id จาก POST)
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function injectTenantId(array $data): array
    {
        $tenantId = tenant_id();

        if ($tenantId !== null) {
            $data['data']['tenant_id'] = $tenantId;
        }

        return $data;
    }

    /**
     * Auto-Scope SELECT, UPDATE และ DELETE ให้ติด WHERE tenant_id เสมอ
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function scopeTenant(array $data): array
    {
        if (! $this->scopedToTenant) {
            // รีเซ็ตสถานะกลับเป็น true สำหรับ query ครั้งถัดไป
            $this->scopedToTenant = true;

            return $data;
        }

        // หากเป็น SuperAdmin ยกเว้นการ Auto Scope
        if (is_superadmin()) {
            return $data;
        }

        $tenantId = tenant_id();

        if ($tenantId !== null) {
            $this->where($this->table . '.tenant_id', $tenantId);
        }

        return $data;
    }
}
