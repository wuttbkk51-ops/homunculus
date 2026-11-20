<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TenantModel;
use App\Models\TenantModelBase;
use App\Models\TenantUserModel;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service สำหรับจัดการ Tenant และความเป็นสมาชิก
 */
class TenantService
{
    protected TenantModelBase $tenantModel;
    protected TenantUserModel $tenantUserModel;

    public function __construct(?TenantModelBase $tenantModel = null, ?TenantUserModel $tenantUserModel = null)
    {
        $this->tenantModel     = $tenantModel ?? new TenantModelBase();
        $this->tenantUserModel = $tenantUserModel ?? new TenantUserModel();
    }

    /**
     * สร้าง Tenant ใหม่ พร้อมสร้าง UUID อัตโนมัติหากไม่ได้ระบุ
     */
    public function createTenant(array $data): array
    {
        if (empty($data['uuid'])) {
            $data['uuid'] = uuid_v7();
        }

        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        $id = $this->tenantModel->insert($data, true);
        if (! $id) {
            $errors = implode(', ', $this->tenantModel->errors());
            throw new RuntimeException("สร้าง Tenant ไม่สำเร็จ: {$errors}");
        }

        $tenant = $this->tenantModel->find($id);
        $this->cacheTenant($tenant);

        return $tenant;
    }

    /**
     * ค้นหา Tenant ด้วย UUID (ใช้ Cache เพื่อลด Query)
     */
    public function findByUuid(string $uuid): ?array
    {
        $cacheKey = "tenant_uuid_{$uuid}";
        $cached   = cache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $tenant = $this->tenantModel->where('uuid', $uuid)->first();
        if ($tenant) {
            $this->cacheTenant($tenant);
        }

        return $tenant;
    }

    /**
     * ค้นหา Tenant ด้วย Integer ID (ใช้ Cache เพื่อลด Query)
     */
    public function findById(int $id): ?array
    {
        $cacheKey = "tenant_id_{$id}";
        $cached   = cache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $tenant = $this->tenantModel->find($id);
        if ($tenant) {
            $this->cacheTenant($tenant);
        }

        return $tenant;
    }

    /**
     * ค้นหา Tenant ด้วย Slug
     */
    public function findBySlug(string $slug): ?array
    {
        $cacheKey = "tenant_slug_{$slug}";
        $cached   = cache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $tenant = $this->tenantModel->where('slug', $slug)->first();
        if ($tenant) {
            $this->cacheTenant($tenant);
        }

        return $tenant;
    }

    /**
     * ตั้งค่า Active Tenant ID ให้กับ Context
     */
    public function setActiveTenantId(?int $tenantId): void
    {
        TenantModel::setCurrentTenantId($tenantId);
    }

    /**
     * ดึงค่า Active Tenant ID ปัจจุบัน
     */
    public function getActiveTenantId(): ?int
    {
        return TenantModel::getCurrentTenantId();
    }

    /**
     * ตรวจสอบข้อมูลสมาชิกใน Tenant
     */
    public function getMember(int $tenantId, int $userId): ?array
    {
        return TenantModel::withoutTenant(fn () =>
            $this->tenantUserModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->first()
        );
    }

    /**
     * เพิ่มสมาชิกเข้า Tenant
     */
    public function addMember(int $tenantId, int $userId, string $role = 'member', ?int $personId = null): int
    {
        return TenantModel::withoutTenant(function () use ($tenantId, $userId, $role, $personId) {
            $data = [
                'tenant_id' => $tenantId,
                'user_id'   => $userId,
                'role'      => $role,
                'status'    => 'active',
                'person_id' => $personId,
            ];

            $insertId = $this->tenantUserModel->insert($data, true);
            if (! $insertId) {
                $errors = implode(', ', $this->tenantUserModel->errors());
                throw new RuntimeException("เพิ่มสมาชิกเข้า Tenant ไม่สำเร็จ: {$errors}");
            }

            return (int) $insertId;
        });
    }

    /**
     * ปรับสถานะสมาชิกใน Tenant และสั่งลบ Auth Cache
     */
    public function updateMemberStatus(int $tenantId, int $userId, string $status): bool
    {
        return TenantModel::withoutTenant(function () use ($tenantId, $userId, $status) {
            $member = $this->getMember($tenantId, $userId);
            if (! $member) {
                throw new InvalidArgumentException('ไม่พบสมาชิกใน Tenant นี้');
            }

            $updated = $this->tenantUserModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->set(['status' => $status])
                ->update();

            // ลบ Cache Flag เพื่อบังคับให้ Request ถัดไปตรวจสอบสถานะใหม่
            cache()->delete("user_auth_{$tenantId}_{$userId}");

            return $updated;
        });
    }

    /**
     * ลบสมาชิกออกจาก Tenant
     */
    public function removeMember(int $tenantId, int $userId): bool
    {
        return TenantModel::withoutTenant(function () use ($tenantId, $userId) {
            $deleted = (bool) $this->tenantUserModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->delete();

            cache()->delete("user_auth_{$tenantId}_{$userId}");

            return $deleted;
        });
    }

    /**
     * บันทึกข้อมูล Tenant ลง Cache
     */
    protected function cacheTenant(array $tenant): void
    {
        $ttl = 3600; // 1 ชั่วโมง
        cache()->save("tenant_id_{$tenant['id']}", $tenant, $ttl);
        cache()->save("tenant_uuid_{$tenant['uuid']}", $tenant, $ttl);
        cache()->save("tenant_slug_{$tenant['slug']}", $tenant, $ttl);
    }
}
