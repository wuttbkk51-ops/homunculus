<?php

declare(strict_types=1);

namespace Tests\Database;

use App\Filters\TenantAuthFilter;
use App\Models\PersonModel;
use App\Models\TenantModel;
use App\Models\TenantModelBase;
use App\Services\PersonService;
use App\Services\TenantAuthService;
use App\Services\TenantService;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\App;

/**
 * Test สถาปัตยกรรม Core Auth, Multi-Tenancy, People Table และ Auth Change Flag
 *
 * @internal
 */
final class TenantAuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = false;
    protected $namespace   = null;

    protected TenantService $tenantService;
    protected TenantAuthService $authService;
    protected PersonService $personService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenantService = new TenantService();
        $this->authService   = new TenantAuthService();
        $this->personService = new PersonService();
        cache()->clean();
        TenantModel::setCurrentTenantId(null);
    }

    protected function tearDown(): void
    {
        TenantModel::setCurrentTenantId(null);
        parent::tearDown();
    }

    /**
     * Helper สำหรับสร้าง Shield User สำหรับการทดสอบ
     */
    protected function createTestUser(string $username = 'user'): User
    {
        $userModel = new UserModel();
        $unique    = uniqid();
        $user      = new User([
            'username' => "{$username}_{$unique}",
            'email'    => "{$username}_{$unique}@example.com",
            'password' => 'secret_pass_123',
            'active'   => 1,
        ]);

        $userId = $userModel->insert($user, true);

        return $userModel->find($userId);
    }

    /**
     * ทดสอบระบบ Wildcard Permission Engine (module.action.scope)
     */
    public function testWildcardPermissionMatching(): void
    {
        // 1. Exact match
        $this->assertTrue($this->authService->matchesPermission('orders.create.own', 'orders.create.own'));
        $this->assertFalse($this->authService->matchesPermission('orders.create.own', 'orders.create.tenant'));

        // 2. Wildcard ท้ายสุด
        $this->assertTrue($this->authService->matchesPermission('orders.create.*', 'orders.create.own'));
        $this->assertTrue($this->authService->matchesPermission('orders.create.*', 'orders.create.tenant'));
        $this->assertFalse($this->authService->matchesPermission('orders.create.*', 'orders.delete.own'));

        // 3. Wildcard ตรงกลาง
        $this->assertTrue($this->authService->matchesPermission('orders.*.own', 'orders.create.own'));
        $this->assertTrue($this->authService->matchesPermission('orders.*.own', 'orders.delete.own'));
        $this->assertFalse($this->authService->matchesPermission('orders.*.own', 'orders.create.tenant'));

        // 4. Wildcard หลายตำแหน่ง
        $this->assertTrue($this->authService->matchesPermission('orders.*.*', 'orders.create.own'));
        $this->assertTrue($this->authService->matchesPermission('orders.*.*', 'orders.view.tenant'));
        $this->assertFalse($this->authService->matchesPermission('orders.*.*', 'reports.view.tenant'));

        // 5. Full access wildcard
        $this->assertTrue($this->authService->matchesPermission('*.*.*', 'anything.action.scope'));
        $this->assertTrue($this->authService->matchesPermission('*', 'anything.action.scope'));
    }

    /**
     * ทดสอบการสร้าง Tenant และ UUID ที่เดายากสำหรับ External Header
     */
    public function testTenantCreationAndUuid(): void
    {
        $tenant = $this->tenantService->createTenant([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
        ]);

        $this->assertNotEmpty($tenant['uuid']);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $tenant['uuid']
        );

        // ค้นหาผ่าน UUID
        $found = $this->tenantService->findByUuid($tenant['uuid']);
        $this->assertNotNull($found);
        $this->assertSame('Acme Corporation', $found['name']);
        $this->assertSame((int) $tenant['id'], (int) $found['id']);
    }

    /**
     * ทดสอบ Hybrid Permission Resolution (Role Preset + User Overrides ใน DB)
     */
    public function testHybridPermissionResolution(): void
    {
        $tenant = $this->tenantService->createTenant([
            'name' => 'Beta Tech',
            'slug' => 'beta-tech',
        ]);

        $user   = $this->createTestUser('staff');
        $userId = (int) $user->id;
        $role   = 'staff';

        // staff ตาม config จะมี: 'people.view.*', 'orders.create.own', 'orders.view.own'
        $permissions = $this->authService->resolveUserPermissions((int) $tenant['id'], $userId, $role);
        $this->assertContains('orders.create.own', $permissions);
        $this->assertNotContains('orders.delete.tenant', $permissions);

        // มอบสิทธิ์พิเศษเฉพาะบุคคล (Override) ลงใน Database
        $this->authService->grantPermission((int) $tenant['id'], $userId, 'orders.delete.tenant');

        $updatedPermissions = $this->authService->resolveUserPermissions((int) $tenant['id'], $userId, $role);
        $this->assertContains('orders.delete.tenant', $updatedPermissions);
        $this->assertContains('orders.create.own', $updatedPermissions);

        // ทดสอบ hasPermission
        $this->assertTrue($this->authService->hasPermission($updatedPermissions, 'orders.delete.tenant'));
        $this->assertTrue($this->authService->hasPermission($updatedPermissions, 'people.view.tenant'));
        $this->assertFalse($this->authService->hasPermission($updatedPermissions, 'finance.transfer.all'));
    }

    /**
     * ทดสอบ Multi-Tenancy Scoping ในตาราง people (แยกข้อมูลเด็ดขาดระหว่าง Tenant)
     */
    public function testMultiTenancyScopingInPeople(): void
    {
        $tenantA = $this->tenantService->createTenant(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        $tenantB = $this->tenantService->createTenant(['name' => 'Tenant B', 'slug' => 'tenant-b']);

        // สร้างคนใน Tenant A
        TenantModel::setCurrentTenantId((int) $tenantA['id']);
        $personA = $this->personService->createPerson([
            'first_name' => 'Alice',
            'last_name'  => 'Smith',
        ]);

        // สร้างคนใน Tenant B
        TenantModel::setCurrentTenantId((int) $tenantB['id']);
        $personB = $this->personService->createPerson([
            'first_name' => 'Bob',
            'last_name'  => 'Jones',
        ]);

        // ตรวจสอบในมุมมองของ Tenant A (ต้องเห็น Alice และไม่เห็น Bob)
        TenantModel::setCurrentTenantId((int) $tenantA['id']);
        $peopleInA = $this->personService->listPeople();
        $idsInA    = array_column($peopleInA, 'id');

        $this->assertContains((int) $personA['id'], $idsInA);
        $this->assertNotContains((int) $personB['id'], $idsInA);

        // ตรวจสอบในมุมมองของ Tenant B (ต้องเห็น Bob และไม่เห็น Alice)
        TenantModel::setCurrentTenantId((int) $tenantB['id']);
        $peopleInB = $this->personService->listPeople();
        $idsInB    = array_column($peopleInB, 'id');

        $this->assertContains((int) $personB['id'], $idsInB);
        $this->assertNotContains((int) $personA['id'], $idsInB);
    }

    /**
     * ทดสอบการล้าง Auth Cache เมื่อสถานะสมาชิกถูกเปลี่ยนหรือถูกเตะออก
     */
    public function testCacheInvalidationOnMemberStatusChange(): void
    {
        $tenant = $this->tenantService->createTenant(['name' => 'Tenant C', 'slug' => 'tenant-c']);
        $user   = $this->createTestUser('member');
        $userId = (int) $user->id;

        $this->tenantService->addMember((int) $tenant['id'], $userId, 'member');

        // บันทึก Cache Flag
        $this->authService->setUserAuthCache((int) $tenant['id'], $userId, [
            'role'        => 'member',
            'permissions' => ['orders.view.own'],
        ]);

        $this->assertNotNull($this->authService->getUserAuthCache((int) $tenant['id'], $userId));

        // เมื่อสั่งเปลี่ยนสถานะสมาชิก
        $this->tenantService->updateMemberStatus((int) $tenant['id'], $userId, 'suspended');

        // Cache Flag ต้องถูกลบทิ้งทันที เพื่อส่งสัญญาณให้ Request ถัดไปรับรู้
        $this->assertNull($this->authService->getUserAuthCache((int) $tenant['id'], $userId));
    }

    /**
     * ทดสอบ TenantAuthFilter: การทำงานร่วมกับ Shield, Cache Flag, และ Header X-Auth-Refreshed
     */
    public function testTenantAuthFilterLifecycle(): void
    {
        $user   = $this->createTestUser('filter_user');
        $userId = (int) $user->id;

        $tenant = $this->tenantService->createTenant(['name' => 'Filter Tenant', 'slug' => 'filter-tnt']);
        $this->tenantService->addMember((int) $tenant['id'], $userId, 'manager');

        // จำลองการ login ผ่าน Shield
        auth()->login($user);

        $filter   = new TenantAuthFilter();
        $request  = service('incomingrequest');
        $request  = $request->setHeader('X-Tenant-ID', $tenant['uuid']);

        // Request 1: Cache ยังว่าง (Cache Miss) -> Filter ต้องโหลดสิทธิ์ลง Cache และส่ง Header แจ้ง Client
        $result = $filter->before($request);
        $this->assertSame($request, $result);

        $response1          = new Response(config(App::class));
        $responseWithHeader = $filter->after($request, $response1);
        $this->assertSame('true', $responseWithHeader->getHeaderLine('X-Auth-Refreshed'));

        // ตรวจสอบว่า Cache Flag ถูกสร้างขึ้นจริง
        $cached = $this->authService->getUserAuthCache((int) $tenant['id'], $userId);
        $this->assertNotNull($cached);
        $this->assertSame('manager', $cached['role']);

        // Request 2: Cache Hit -> Filter ผ่านปกติและไม่ต้องส่ง Header ซ้ำ
        $result2   = $filter->before($request);
        $response2 = new Response(config(App::class));
        $response2 = $filter->after($request, $response2);
        $this->assertSame($request, $result2);
        $this->assertSame('', $response2->getHeaderLine('X-Auth-Refreshed'));

        // Request 3: สมาชิกถูกระงับสิทธิ์ (Suspended/เตะออก) -> Cache โดนลบ -> Filter ต้องตอบ 404 เพื่อไม่เปิดเผยว่า Tenant มีอยู่จริง และคง session ไว้
        $this->tenantService->updateMemberStatus((int) $tenant['id'], $userId, 'suspended');

        $responseKicked = $filter->before($request);
        $this->assertSame(404, $responseKicked->getStatusCode());
        $this->assertTrue(auth()->loggedIn());
    }

    /**
     * ทดสอบ Admin Platform Role: Bypass การตรวจสิทธิ์ใน tenant_users และสลับ Context ด้วย UUID
     */
    public function testAdminBypassWithTenantUuid(): void
    {
        $adminUser = $this->createTestUser('superadmin');
        $adminId   = (int) $adminUser->id;

        // กำหนด Shield Group เป็น 'admin'
        $adminUser->addGroup('admin');
        auth()->login($adminUser);

        $tenant = $this->tenantService->createTenant(['name' => 'Admin Test Tenant', 'slug' => 'admin-tnt']);

        // หมายเหตุ: บัญชี Admin ไม่ได้ถูกเพิ่มใน tenant_users!
        $this->assertNull($this->tenantService->getMember((int) $tenant['id'], $adminId));

        $filter  = new TenantAuthFilter();
        $request = service('incomingrequest');
        $request = $request->setHeader('X-Tenant-ID', $tenant['uuid']);

        // Admin ส่ง Header UUID เข้ามา -> Filter ต้องยินยอมให้ผ่านและตั้ง Active Tenant สำเร็จ
        $result = $filter->before($request);
        $this->assertSame($request, $result);
        $this->assertSame((int) $tenant['id'], TenantModel::getCurrentTenantId());

        // Admin ไม่ส่ง Header UUID -> Filter ตั้งค่าระดับ Platform (ID: 0) ทันที
        $emptyRequest = clone $request;
        $emptyRequest->removeHeader('X-Tenant-ID');
        $resultEmpty = $filter->before($emptyRequest);
        $this->assertSame($emptyRequest, $resultEmpty);
        $this->assertSame(0, TenantModel::getCurrentTenantId());
    }

    /**
     * ทดสอบการปฏิเสธ Numeric ID จากภายนอกเพื่อป้องกันการสุ่มเดา ID (Enumeration)
     */
    public function testRejectNumericTenantIdentifier(): void
    {
        $user   = $this->createTestUser('numeric_tester');
        $tenant = $this->tenantService->createTenant(['name' => 'Numeric Test', 'slug' => 'numeric-tnt']);
        $this->tenantService->addMember((int) $tenant['id'], (int) $user->id, 'member');

        auth()->login($user);

        $filter  = new TenantAuthFilter();
        $request = service('incomingrequest');
        // ส่ง Integer ID ผ่าน Header
        $request = $request->setHeader('X-Tenant-ID', (string) $tenant['id']);

        $response = $filter->before($request);
        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * ทดสอบผู้ใช้ไม่ได้เป็นสมาชิกใน Tenant ต้องได้ 404 เพื่อป้องกันการสุ่มเดาว่ามี Tenant หรือไม่ และไม่ถูก logout
     */
    public function testNonMemberTenantAccessReturns404WithoutLogout(): void
    {
        $userA   = $this->createTestUser('user_a');
        $tenantB = $this->tenantService->createTenant(['name' => 'Tenant B', 'slug' => 'tenant-b-test']);

        auth()->login($userA);

        $filter  = new TenantAuthFilter();
        $request = service('incomingrequest');
        $request = $request->setHeader('X-Tenant-ID', $tenantB['uuid']);

        $response = $filter->before($request);
        $this->assertSame(404, $response->getStatusCode());
        // ยืนยันว่า session ยังไม่หลุด
        $this->assertTrue(auth()->loggedIn());
    }
}
