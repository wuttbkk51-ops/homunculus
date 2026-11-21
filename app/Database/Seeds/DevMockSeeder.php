<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Models\PersonModel;
use App\Models\TenantModel;
use App\Models\TenantModelBase;
use App\Models\TenantUserModel;
use App\Models\TenantUserPermissionModel;
use App\Services\TenantService;
use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Seeder สำหรับ Mockup ข้อมูลตัวอย่างสำหรับช่วง Development
 *
 * ประกอบด้วย:
 * 1. Admin Group: superAdmin, juniorDev (ลูกน้อง admin)
 * 2. User Group: เจ้าของบริษัท 2 เจ้า แต่ละเจ้ามีลูกน้อง 1 คน (รวม 2 Tenants, 4 Users, 4 People)
 * 3. ตัวอย่าง Auth Groups และ Permissions
 */
class DevMockSeeder extends Seeder
{
    public function run(): void
    {
        // ป้องกันไม่ให้ Mockup data ปะปนเข้าสู่ระบบ production
        if (ENVIRONMENT === 'production') {
            return;
        }

        $userModel                 = model(UserModel::class);
        $tenantModel               = new TenantModelBase();
        $tenantUserModel           = new TenantUserModel();
        $tenantUserPermissionModel = new TenantUserPermissionModel();
        $personModel               = new PersonModel();

        // --------------------------------------------------------------------
        // 1. สร้าง Platform Admins (Shield Group: admin)
        // --------------------------------------------------------------------

        // 1.1 superAdmin
        $superAdmin = $this->createOrGetUser($userModel, [
            'username' => 'superAdmin',
            'email'    => 'superadmin@homunculus.local',
            'password' => 'DevPassword123!',
        ]);
        $superAdmin->syncGroups('admin');
        $superAdmin->addPermission('admin.access', 'admin.settings', 'users.manage-admins');

        // 1.2 juniorDev (ลูกน้อง admin)
        $juniorDev = $this->createOrGetUser($userModel, [
            'username' => 'juniorDev',
            'email'    => 'juniordev@homunculus.local',
            'password' => 'DevPassword123!',
        ]);
        $juniorDev->syncGroups('admin');
        $juniorDev->addPermission('admin.access', 'users.create', 'users.edit');

        // --------------------------------------------------------------------
        // 2. ข้อมูลบริษัทที่ 1: Siam Future Corp (Tenant 1)
        // --------------------------------------------------------------------
        $tenant1 = $this->createOrGetTenant($tenantModel, [
            'name' => 'บริษัท สยาม ฟิวเจอร์ จำกัด',
            'slug' => 'siam-future',
        ]);
        $tenant1Id = (int) $tenant1['id'];

        // 2.1 เจ้าของบริษัทที่ 1 (Owner)
        $siamOwner = $this->createOrGetUser($userModel, [
            'username' => 'siamOwner',
            'email'    => 'owner@siamfuture.local',
            'password' => 'DevPassword123!',
        ]);
        $siamOwner->syncGroups('user');
        $siamOwner->addPermission('beta.access');

        $siamOwnerPerson = $this->createOrGetPerson($personModel, $tenant1Id, [
            'first_name' => 'สมบัติ',
            'last_name'  => 'อนาคตไกล',
            'email'      => 'owner@siamfuture.local',
            'phone'      => '081-111-2222',
        ]);

        $this->attachTenantUser(
            $tenantUserModel,
            $tenant1Id,
            (int) $siamOwner->id,
            'admin',
            (int) $siamOwnerPerson['id']
        );

        $this->grantTenantPermission($tenantUserPermissionModel, $tenant1Id, (int) $siamOwner->id, 'reports.export.*');

        // 2.2 ลูกน้องบริษัทที่ 1 (Staff)
        $siamStaff = $this->createOrGetUser($userModel, [
            'username' => 'siamStaff',
            'email'    => 'staff@siamfuture.local',
            'password' => 'DevPassword123!',
        ]);
        $siamStaff->syncGroups('user');

        $siamStaffPerson = $this->createOrGetPerson($personModel, $tenant1Id, [
            'first_name' => 'สมศรี',
            'last_name'  => 'ขยันยิ่ง',
            'email'      => 'staff@siamfuture.local',
            'phone'      => '081-333-4444',
        ]);

        $this->attachTenantUser(
            $tenantUserModel,
            $tenant1Id,
            (int) $siamStaff->id,
            'staff',
            (int) $siamStaffPerson['id']
        );

        $this->grantTenantPermission($tenantUserPermissionModel, $tenant1Id, (int) $siamStaff->id, 'orders.create.own');

        // --------------------------------------------------------------------
        // 3. ข้อมูลบริษัทที่ 2: Bangkok Logistic Ltd (Tenant 2)
        // --------------------------------------------------------------------
        $tenant2 = $this->createOrGetTenant($tenantModel, [
            'name' => 'บริษัท บางกอก โลจิสติกส์ จำกัด',
            'slug' => 'bangkok-logistic',
        ]);
        $tenant2Id = (int) $tenant2['id'];

        // 3.1 เจ้าของบริษัทที่ 2 (Owner)
        $bkkOwner = $this->createOrGetUser($userModel, [
            'username' => 'bkkOwner',
            'email'    => 'owner@bangkoklogistic.local',
            'password' => 'DevPassword123!',
        ]);
        $bkkOwner->syncGroups('user');
        $bkkOwner->addPermission('beta.access');

        $bkkOwnerPerson = $this->createOrGetPerson($personModel, $tenant2Id, [
            'first_name' => 'ธนากร',
            'last_name'  => 'ส่งของเร็ว',
            'email'      => 'owner@bangkoklogistic.local',
            'phone'      => '082-555-6666',
        ]);

        $this->attachTenantUser(
            $tenantUserModel,
            $tenant2Id,
            (int) $bkkOwner->id,
            'admin',
            (int) $bkkOwnerPerson['id']
        );

        $this->grantTenantPermission($tenantUserPermissionModel, $tenant2Id, (int) $bkkOwner->id, 'logistics.manage.*');

        // 3.2 ลูกน้องบริษัทที่ 2 (Staff)
        $bkkStaff = $this->createOrGetUser($userModel, [
            'username' => 'bkkStaff',
            'email'    => 'staff@bangkoklogistic.local',
            'password' => 'DevPassword123!',
        ]);
        $bkkStaff->syncGroups('user');

        $bkkStaffPerson = $this->createOrGetPerson($personModel, $tenant2Id, [
            'first_name' => 'พิมพ์ใจ',
            'last_name'  => 'ขับขี่ปลอดภัย',
            'email'      => 'staff@bangkoklogistic.local',
            'phone'      => '082-777-8888',
        ]);

        $this->attachTenantUser(
            $tenantUserModel,
            $tenant2Id,
            (int) $bkkStaff->id,
            'staff',
            (int) $bkkStaffPerson['id']
        );

        $this->grantTenantPermission($tenantUserPermissionModel, $tenant2Id, (int) $bkkStaff->id, 'orders.view.own');
    }

    /**
     * ดึง User ที่มีอยู่แล้ว หรือสร้างขึ้นใหม่ (idempotent)
     */
    protected function createOrGetUser(UserModel $userModel, array $userData): User
    {
        $existing = $userModel->findByCredentials(['email' => $userData['email']]);
        if ($existing instanceof User) {
            return $existing;
        }

        $user = new User([
            'username' => $userData['username'],
            'email'    => $userData['email'],
            'password' => $userData['password'],
            'active'   => 1,
        ]);

        $userId = $userModel->insert($user, true);

        return $userModel->find($userId);
    }

    /**
     * ดึง Tenant ที่มีอยู่แล้ว หรือสร้างขึ้นใหม่ (idempotent)
     */
    protected function createOrGetTenant(TenantModelBase $tenantModel, array $tenantData): array
    {
        $existing = $tenantModel->where('slug', $tenantData['slug'])->first();
        if ($existing !== null) {
            return $existing;
        }

        $id = $tenantModel->insert([
            'uuid'   => uuid_v4(),
            'name'   => $tenantData['name'],
            'slug'   => $tenantData['slug'],
            'status' => 'active',
        ], true);

        return $tenantModel->find($id);
    }

    /**
     * ดึง Person ประจำ Tenant หรือสร้างขึ้นใหม่ (idempotent)
     */
    protected function createOrGetPerson(PersonModel $personModel, int $tenantId, array $personData): array
    {
        return TenantModel::withoutTenant(function () use ($personModel, $tenantId, $personData) {
            $existing = $personModel
                ->where('tenant_id', $tenantId)
                ->where('email', $personData['email'])
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            $personData['tenant_id'] = $tenantId;
            $id                      = $personModel->insert($personData, true);

            return $personModel->find($id);
        });
    }

    /**
     * ผูก User เข้ากับ Tenant (idempotent)
     */
    protected function attachTenantUser(
        TenantUserModel $tenantUserModel,
        int $tenantId,
        int $userId,
        string $role,
        ?int $personId = null
    ): void {
        TenantModel::withoutTenant(function () use ($tenantUserModel, $tenantId, $userId, $role, $personId) {
            $existing = $tenantUserModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->first();

            if ($existing !== null) {
                return;
            }

            $tenantUserModel->insert([
                'tenant_id' => $tenantId,
                'user_id'   => $userId,
                'person_id' => $personId,
                'role'      => $role,
                'status'    => 'active',
            ]);
        });
    }

    /**
     * กำหนดสิทธิ์ granular ประจำ Tenant (idempotent)
     */
    protected function grantTenantPermission(
        TenantUserPermissionModel $permModel,
        int $tenantId,
        int $userId,
        string $permission
    ): void {
        TenantModel::withoutTenant(function () use ($permModel, $tenantId, $userId, $permission) {
            $existing = $permModel
                ->where('tenant_id', $tenantId)
                ->where('user_id', $userId)
                ->where('permission', $permission)
                ->first();

            if ($existing !== null) {
                return;
            }

            $permModel->insert([
                'tenant_id'  => $tenantId,
                'user_id'    => $userId,
                'permission' => $permission,
            ]);
        });
    }
}
