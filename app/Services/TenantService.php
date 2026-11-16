<?php

namespace App\Services;

use App\Entities\Tenant;
use App\Models\TenantModel;

/**
 * Service สำหรับจัดการ Tenant Context และการแคชประสิทธิภาพสูง (High-Performance Caching)
 * สอดคล้องกับข้อกำหนด Zero DB Query ต่อ Request
 */
class TenantService
{
    /**
     * ดึงข้อมูล Tenant ผ่านระบบแคช (CI4 Cache)
     */
    public function getTenant(int $tenantId): ?Tenant
    {
        $cacheKey = "tenant_{$tenantId}";

        return cache()->remember($cacheKey, 3600, static function () use ($tenantId): ?Tenant {
            /** @var TenantModel $model */
            $model = model(TenantModel::class);

            return $model->find($tenantId);
        });
    }

    /**
     * ตรวจสอบว่า Tenant ยังมีสถานะเปิดใช้งานและยังไม่หมดอายุหรือไม่
     */
    public function isTenantActive(int $tenantId): bool
    {
        $tenant = $this->getTenant($tenantId);

        if (! $tenant) {
            return false;
        }

        return $tenant->isActive();
    }

    /**
     * ล้างแคชของ Tenant ทันทีเมื่อสถานะหรือข้อมูลเปลี่ยนแปลง (Instant Invalidation)
     */
    public function clearCache(int $tenantId): void
    {
        cache()->delete("tenant_{$tenantId}");
    }

    /**
     * อัปเดตสถานะของ Tenant พร้อมล้างแคชทันที
     */
    public function updateStatus(int $tenantId, string $status, ?string $expireDate = null): bool
    {
        /** @var TenantModel $model */
        $model = model(TenantModel::class);

        $data = ['status' => $status];
        if ($expireDate !== null) {
            $data['expire_date'] = $expireDate;
        }

        $result = $model->update($tenantId, $data);

        if ($result) {
            $this->clearCache($tenantId);
        }

        return (bool) $result;
    }
}
