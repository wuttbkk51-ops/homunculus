<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TenantModel;
use App\Models\TenantUserPermissionModel;
use Config\AuthTenantRoles;

/**
 * Service สำหรับจัดการสิทธิ์และ Auth Cache Flag ประจำ Tenant
 */
class AuthService
{
    protected TenantUserPermissionModel $permissionModel;
    protected AuthTenantRoles $roleConfig;

    public function __construct(
        ?TenantUserPermissionModel $permissionModel = null,
        ?AuthTenantRoles $roleConfig = null
    ) {
        $this->permissionModel = $permissionModel ?? new TenantUserPermissionModel();
        $this->roleConfig      = $roleConfig ?? config(AuthTenantRoles::class);
    }

    /**
     * ดึงสิทธิ์ที่ประมวลผลแล้ว (Resolved Permissions) ของผู้ใช้ใน Tenant
     * รวมสิทธิ์จาก Role Presets (Config) และ User Overrides (DB)
     *
     * @return list<string>
     */
    public function resolveUserPermissions(int $tenantId, int $userId, string $role): array
    {
        // 1. สิทธิ์เริ่มต้นจาก Role Preset ใน Config
        $rolePermissions = $this->roleConfig->roles[$role] ?? [];

        // 2. สิทธิ์เฉพาะบุคคลจาก Database
        $directRecords = TenantModel::withoutTenant(fn () =>
            $this->permissionModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->findAll()
        );

        $directPermissions = array_column($directRecords, 'permission');

        // 3. รวมสิทธิ์และตัดตัวซ้ำ
        return array_values(array_unique(array_merge($rolePermissions, $directPermissions)));
    }

    /**
     * ตรวจสอบว่าชุดสิทธิ์ของผู้ใช้ครอบคลุมสิทธิ์ที่ต้องการหรือไม่
     *
     * @param list<string> $userPermissions
     */
    public function hasPermission(array $userPermissions, string $requiredPermission): bool
    {
        foreach ($userPermissions as $pattern) {
            if ($this->matchesPermission($pattern, $requiredPermission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ตรวจสอบ Wildcard Permission Engine สำหรับรูปแบบ module.action.scope
     *
     * รองรับ '*', '*.*.*', 'orders.create.*', 'orders.*.own' ฯลฯ
     */
    public function matchesPermission(string $pattern, string $required): bool
    {
        // กรณีตรงกันเป๊ะ หรือ Pattern คือ * ตัวเดียว หรือ *.*.*
        if ($pattern === $required || $pattern === '*' || $pattern === '*.*.*') {
            return true;
        }

        $patternParts  = explode('.', $pattern);
        $requiredParts = explode('.', $required);

        // หากจำนวน Segment ไม่เท่ากัน และ Pattern ไม่ได้ลงท้ายด้วย *
        if (count($patternParts) !== count($requiredParts)) {
            // อนุญาตถ้า pattern สั้นกว่าและตัวสุดท้ายเป็น *
            $lastIndex = count($patternParts) - 1;
            if ($patternParts[$lastIndex] === '*') {
                for ($i = 0; $i < $lastIndex; $i++) {
                    if ($patternParts[$i] !== $requiredParts[$i] && $patternParts[$i] !== '*') {
                        return false;
                    }
                }
                return true;
            }

            return false;
        }

        // เทียบทีละตำแหน่ง
        foreach ($patternParts as $index => $part) {
            if ($part === '*') {
                continue;
            }

            if ($part !== $requiredParts[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * ดึง Auth Cache Flag ของผู้ใช้ประจำ Tenant
     */
    public function getUserAuthCache(int $tenantId, int $userId): ?array
    {
        $cacheKey = "user_auth_{$tenantId}_{$userId}";
        return cache($cacheKey);
    }

    /**
     * บันทึก Auth Cache Flag ของผู้ใช้ประจำ Tenant
     */
    public function setUserAuthCache(int $tenantId, int $userId, array $data, int $ttl = 3600): void
    {
        $cacheKey = "user_auth_{$tenantId}_{$userId}";
        cache()->save($cacheKey, $data, $ttl);
    }

    /**
     * ลบ Auth Cache Flag ของผู้ใช้ประจำ Tenant เพื่อส่งสัญญาณว่าข้อมูลเปลี่ยน
     */
    public function invalidateUserAuthCache(int $tenantId, int $userId): void
    {
        $cacheKey = "user_auth_{$tenantId}_{$userId}";
        cache()->delete($cacheKey);
    }

    /**
     * เพิ่มสิทธิ์เฉพาะบุคคลลงใน Database และล้าง Cache
     */
    public function grantPermission(int $tenantId, int $userId, string $permission): void
    {
        TenantModel::withoutTenant(function () use ($tenantId, $userId, $permission) {
            $existing = $this->permissionModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->where('permission', $permission)
                ->first();

            if (! $existing) {
                $this->permissionModel->insert([
                    'tenant_id'  => $tenantId,
                    'user_id'    => $userId,
                    'permission' => $permission,
                ]);
            }
        });

        $this->invalidateUserAuthCache($tenantId, $userId);
    }

    /**
     * ถอดสิทธิ์เฉพาะบุคคลออกจาก Database และล้าง Cache
     */
    public function revokePermission(int $tenantId, int $userId, string $permission): void
    {
        TenantModel::withoutTenant(function () use ($tenantId, $userId, $permission) {
            $this->permissionModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->where('permission', $permission)
                ->delete();
        });

        $this->invalidateUserAuthCache($tenantId, $userId);
    }
}
