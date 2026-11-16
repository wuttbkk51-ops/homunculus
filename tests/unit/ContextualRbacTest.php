<?php

namespace Tests\Unit;

use App\Entities\User;
use App\Services\AuthService;
use App\Services\BranchService;
use App\Services\TenantService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\AuthGroups;

/**
 * @internal
 */
final class ContextualRbacTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = new AuthService(
            $this->createMock(TenantService::class),
            $this->createMock(BranchService::class),
            new AuthGroups()
        );
    }

    public function testSuperAdminAlwaysHasPermission(): void
    {
        $superAdmin = new User([
            'id'        => 1,
            'username'  => 'superadmin',
            'tenant_id' => null,
        ]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($superAdmin->isAdmin());
        $this->assertTrue($this->authService->can('sale.view.own', $superAdmin));
        $this->assertTrue($this->authService->can('sale.view.all', $superAdmin));
        $this->assertTrue($this->authService->can('user.manage.all', $superAdmin));
        $this->assertTrue($this->authService->can('anything.whatever.scope', $superAdmin));
    }

    public function testNonSuperAdminIsRestrictedToTenant(): void
    {
        $tenantAdmin = new User([
            'id'        => 2,
            'username'  => 'tenant_admin',
            'tenant_id' => 10,
        ]);

        $this->assertFalse($tenantAdmin->isSuperAdmin());
        $this->assertSame(10, $tenantAdmin->getTenantId());
    }

    public function testScopeHierarchyMatching(): void
    {
        // 1. สิทธิ์ระดับ .all ครอบคลุมทั้ง .branch และ .own
        $this->assertTrue($this->authService->matchPermission('sale.view.all', 'sale.view.own'));
        $this->assertTrue($this->authService->matchPermission('sale.view.all', 'sale.view.branch'));
        $this->assertTrue($this->authService->matchPermission('sale.view.all', 'sale.view.all'));

        // 2. สิทธิ์ระดับ .branch ครอบคลุม .own แต่ไม่ครอบคลุม .all
        $this->assertTrue($this->authService->matchPermission('sale.view.branch', 'sale.view.own'));
        $this->assertTrue($this->authService->matchPermission('sale.view.branch', 'sale.view.branch'));
        $this->assertFalse($this->authService->matchPermission('sale.view.branch', 'sale.view.all'));

        // 3. สิทธิ์ระดับ .own ครอบคลุมเฉพาะ .own
        $this->assertTrue($this->authService->matchPermission('sale.view.own', 'sale.view.own'));
        $this->assertFalse($this->authService->matchPermission('sale.view.own', 'sale.view.branch'));
        $this->assertFalse($this->authService->matchPermission('sale.view.own', 'sale.view.all'));
    }

    public function testWildcardMatching(): void
    {
        // Global wildcard
        $this->assertTrue($this->authService->matchPermission('*', 'sale.create.branch'));

        // Module wildcard
        $this->assertTrue($this->authService->matchPermission('sale.*', 'sale.create.branch'));
        $this->assertTrue($this->authService->matchPermission('sale.*', 'sale.view.own'));
        $this->assertFalse($this->authService->matchPermission('sale.*', 'user.create.all'));

        // Action wildcard
        $this->assertTrue($this->authService->matchPermission('sale.view.*', 'sale.view.own'));
        $this->assertTrue($this->authService->matchPermission('sale.view.*', 'sale.view.branch'));
        $this->assertFalse($this->authService->matchPermission('sale.view.*', 'sale.create.branch'));

        // Requested wildcard (เช่น ตรวจสอบว่ามีสิทธิ์ใดๆ ในโมดูล inventory หรือไม่ เพื่อแสดงเมนู)
        $this->assertTrue($this->authService->matchPermission('inventory.view.branch', 'inventory.*'));
        $this->assertTrue($this->authService->matchPermission('inventory.create.own', 'inventory.*'));
        $this->assertFalse($this->authService->matchPermission('sale.view.branch', 'inventory.*'));
    }

    public function testContextualRolePermissions(): void
    {
        $tenantService = $this->createMock(TenantService::class);
        $tenantService->method('isTenantActive')->willReturn(true);

        $branchService = $this->createMock(BranchService::class);
        $branchService->method('getUserBranchRole')->willReturnCallback(static function ($userId, $branchId) {
            // สาขา 1 เป็น manager, สาขา 2 เป็น cashier
            return $branchId === 1 ? 'manager' : 'cashier';
        });

        $authService = new AuthService($tenantService, $branchService, new AuthGroups());

        $user = new User([
            'id'        => 2,
            'username'  => 'somchai',
            'tenant_id' => 10,
        ]);

        // เมื่อทำงานในสาขา 1 (Manager):
        // Manager มี 'sale.*', '*.branch' -> ต้องเปิดบิลสาขาได้ และดูรายงานสาขาได้
        $this->assertTrue($authService->can('sale.create.branch', $user, 1));
        $this->assertTrue($authService->can('sale.view.branch', $user, 1));
        $this->assertTrue($authService->can('sale.delete.branch', $user, 1));
        $this->assertTrue($authService->can('report.view.branch', $user, 1));
        // แต่ต้องไม่มีสิทธิ์ระดับ .all ของทั้งระบบ
        $this->assertFalse($authService->can('user.manage.all', $user, 1));

        // เมื่อสลับไปทำงานในสาขา 2 (Cashier):
        // Cashier มีเฉพาะ 'sale.create.branch' และ 'sale.view.own'
        $this->assertTrue($authService->can('sale.create.branch', $user, 2));
        $this->assertTrue($authService->can('sale.view.own', $user, 2));
        // Cashier ต้องไม่มีสิทธิ์ลบบิลสาขา และไม่มีสิทธิ์ดูรายงานสาขา
        $this->assertFalse($authService->can('sale.delete.branch', $user, 2));
        $this->assertFalse($authService->can('report.view.branch', $user, 2));
    }

    public function testCashierVersusOwnerPermissions(): void
    {
        $tenantService = $this->createMock(TenantService::class);
        $tenantService->method('isTenantActive')->willReturn(true);

        $branchService = $this->createMock(BranchService::class);
        // จำลอง Owner (Admin Group)
        $userModel = model(\App\Models\UserModel::class);
        $ownerId   = (int) $userModel->insert(['username' => 'owner_rbac_test', 'tenant_id' => 2, 'active' => 1]);
        $cashierId = (int) $userModel->insert(['username' => 'cashier_rbac_test', 'tenant_id' => 2, 'active' => 1]);

        /** @var User $owner */
        $owner = $userModel->findById($ownerId);
        $owner->addGroup('admin');

        /** @var User $cashier */
        $cashier = $userModel->findById($cashierId);
        $cashier->addGroup('user');

        $branchService->method('getUserBranchRole')->willReturnCallback(static function ($userId, $branchId) use ($ownerId) {
            return $userId === $ownerId ? 'manager' : 'cashier';
        });

        $authService = new AuthService($tenantService, $branchService, new AuthGroups());

        // 1. Owner ทำได้ทุกอย่างใน Tenant และสาขา
        $this->assertTrue($authService->can('sale.create.branch', $owner, 3));
        $this->assertTrue($authService->can('sale.delete.branch', $owner, 3));
        $this->assertTrue($authService->can('inventory.adjust.branch', $owner, 3));
        $this->assertTrue($authService->can('report.view.all', $owner, 3));
        $this->assertTrue($authService->can('product.create.all', $owner, 3));

        // 2. Cashier ทำได้เฉพาะหน้าร้าน
        $this->assertTrue($authService->can('sale.create.branch', $cashier, 3));
        $this->assertTrue($authService->can('sale.view.own', $cashier, 3));
        $this->assertTrue($authService->can('product.view.all', $cashier, 3));
        $this->assertTrue($authService->can('inventory.view.branch', $cashier, 3));

        // 3. Cashier ต้องทำสิ่งต่อไปนี้ไม่ได้เด็ดขาด
        $this->assertFalse($authService->can('sale.delete.branch', $cashier, 3)); // ห้ามลบบิล
        $this->assertFalse($authService->can('inventory.adjust.branch', $cashier, 3)); // ห้ามปรับยอดสต็อก
        $this->assertFalse($authService->can('report.view.all', $cashier, 3)); // ห้ามดูรายงานรวม
        $this->assertFalse($authService->can('product.delete.all', $cashier, 3)); // ห้ามลบสินค้า
        $this->assertFalse($authService->can('user.manage.all', $cashier, 3)); // ห้ามจัดการพนักงาน
    }
}
