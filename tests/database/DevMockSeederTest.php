<?php

declare(strict_types=1);

namespace Tests\Database;

use App\Models\PersonModel;
use App\Models\TenantModel;
use App\Models\TenantModelBase;
use App\Models\TenantUserModel;
use App\Models\TenantUserPermissionModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Test ตรวจสอบความถูกต้องของ DevMockSeeder และ InitSeeder
 *
 * @internal
 */
final class DevMockSeederTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = false;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
        cache()->clean();
        TenantModel::setCurrentTenantId(null);
    }

    protected function tearDown(): void
    {
        TenantModel::setCurrentTenantId(null);
        parent::tearDown();
    }

    /**
     * ทดสอบว่า Seeder สร้าง Shield Users, Groups และ Permissions ได้ถูกต้อง
     */
    public function testSeederPopulatesUsersAndGroups(): void
    {
        $seeder = Database::seeder();
        $seeder->call('DevMockSeeder');

        $userModel = model(UserModel::class);

        // 1. ตรวจสอบ Platform Admins (Group: admin)
        $superAdmin = $userModel->findByCredentials(['email' => 'superadmin@homunculus.local']);
        $this->assertInstanceOf(User::class, $superAdmin);
        $this->assertSame('superAdmin', $superAdmin->username);
        $this->assertTrue($superAdmin->inGroup('admin'));
        $this->assertTrue($superAdmin->hasPermission('admin.access'));
        $this->assertTrue($superAdmin->hasPermission('admin.settings'));

        $juniorDev = $userModel->findByCredentials(['email' => 'juniordev@homunculus.local']);
        $this->assertInstanceOf(User::class, $juniorDev);
        $this->assertSame('juniorDev', $juniorDev->username);
        $this->assertTrue($juniorDev->inGroup('admin'));
        $this->assertTrue($juniorDev->hasPermission('admin.access'));
        $this->assertTrue($juniorDev->hasPermission('users.create'));

        // 2. ตรวจสอบ เจ้าของและลูกน้องของบริษัททั้ง 2 (Group: user)
        $siamOwner = $userModel->findByCredentials(['email' => 'owner@siamfuture.local']);
        $this->assertInstanceOf(User::class, $siamOwner);
        $this->assertTrue($siamOwner->inGroup('user'));
        $this->assertFalse($siamOwner->inGroup('admin'));
        $this->assertTrue($siamOwner->hasPermission('beta.access'));

        $siamStaff = $userModel->findByCredentials(['email' => 'staff@siamfuture.local']);
        $this->assertInstanceOf(User::class, $siamStaff);
        $this->assertTrue($siamStaff->inGroup('user'));

        $bkkOwner = $userModel->findByCredentials(['email' => 'owner@bangkoklogistic.local']);
        $this->assertInstanceOf(User::class, $bkkOwner);
        $this->assertTrue($bkkOwner->inGroup('user'));
        $this->assertTrue($bkkOwner->hasPermission('beta.access'));

        $bkkStaff = $userModel->findByCredentials(['email' => 'staff@bangkoklogistic.local']);
        $this->assertInstanceOf(User::class, $bkkStaff);
        $this->assertTrue($bkkStaff->inGroup('user'));
    }

    /**
     * ทดสอบว่า Seeder สร้าง Tenants, People, TenantUsers และ Granular Permissions ครบถ้วน
     */
    public function testSeederPopulatesTenantsAndMembers(): void
    {
        $seeder = Database::seeder();
        $seeder->call('DevMockSeeder');

        $tenantModel     = new TenantModelBase();
        $tenantUserModel = new TenantUserModel();
        $personModel     = new PersonModel();
        $permModel       = new TenantUserPermissionModel();
        $userModel       = model(UserModel::class);

        // 1. ตรวจสอบ Tenants ทั้ง 2 เจ้า
        $tenant1 = $tenantModel->where('slug', 'siam-future')->first();
        $this->assertNotNull($tenant1);
        $this->assertSame('บริษัท สยาม ฟิวเจอร์ จำกัด', $tenant1['name']);
        $this->assertNotEmpty($tenant1['uuid']);

        $tenant2 = $tenantModel->where('slug', 'bangkok-logistic')->first();
        $this->assertNotNull($tenant2);
        $this->assertSame('บริษัท บางกอก โลจิสติกส์ จำกัด', $tenant2['name']);
        $this->assertNotEmpty($tenant2['uuid']);

        // 2. ตรวจสอบ People และความเชื่อมโยงกับ Tenant
        $siamPeople = TenantModel::withoutTenant(fn () =>
            $personModel->where('tenant_id', (int) $tenant1['id'])->findAll()
        );
        $this->assertCount(2, $siamPeople);

        $bkkPeople = TenantModel::withoutTenant(fn () =>
            $personModel->where('tenant_id', (int) $tenant2['id'])->findAll()
        );
        $this->assertCount(2, $bkkPeople);

        // 3. ตรวจสอบ TenantUsers และ Role
        $siamOwnerUser = $userModel->findByCredentials(['email' => 'owner@siamfuture.local']);
        $siamStaffUser = $userModel->findByCredentials(['email' => 'staff@siamfuture.local']);

        $siamOwnerMember = TenantModel::withoutTenant(fn () =>
            $tenantUserModel
                ->where('tenant_id', (int) $tenant1['id'])
                ->where('user_id', (int) $siamOwnerUser->id)
                ->first()
        );
        $this->assertNotNull($siamOwnerMember);
        $this->assertSame('admin', $siamOwnerMember['role']);
        $this->assertNotEmpty($siamOwnerMember['person_id']);

        $siamStaffMember = TenantModel::withoutTenant(fn () =>
            $tenantUserModel
                ->where('tenant_id', (int) $tenant1['id'])
                ->where('user_id', (int) $siamStaffUser->id)
                ->first()
        );
        $this->assertNotNull($siamStaffMember);
        $this->assertSame('staff', $siamStaffMember['role']);

        // 4. ตรวจสอบ Granular Tenant Permissions
        $ownerPerm = TenantModel::withoutTenant(fn () =>
            $permModel
                ->where('tenant_id', (int) $tenant1['id'])
                ->where('user_id', (int) $siamOwnerUser->id)
                ->where('permission', 'reports.export.*')
                ->first()
        );
        $this->assertNotNull($ownerPerm);
    }

    /**
     * ทดสอบว่า Seeder ทำงานแบบ Idempotent สามารถรันซ้ำได้โดยข้อมูลไม่บวมหรือ error
     */
    public function testSeederIsIdempotent(): void
    {
        $seeder = Database::seeder();

        // รันซ้ำรอบที่ 1 และ 2
        $seeder->call('DevMockSeeder');
        $seeder->call('DevMockSeeder');

        $tenantModel = new TenantModelBase();
        $userModel   = model(UserModel::class);

        // ตรวจสอบว่า Tenant ยังคงมีแค่ 2 เจ้าเท่าเดิม
        $tenants = $tenantModel->whereIn('slug', ['siam-future', 'bangkok-logistic'])->findAll();
        $this->assertCount(2, $tenants);

        // ตรวจสอบว่า User ที่สร้างโดย seeder ยังคงมี 6 คนเท่าเดิม (admin 2 + user 4)
        $users = $userModel->whereIn('username', [
            'superAdmin',
            'juniorDev',
            'siamOwner',
            'siamStaff',
            'bkkOwner',
            'bkkStaff',
        ])->findAll();
        $this->assertCount(6, $users);
    }
}
