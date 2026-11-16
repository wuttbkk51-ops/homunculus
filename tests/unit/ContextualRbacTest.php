<?php

namespace Tests\Unit;

use App\Entities\User;
use App\Services\AuthService;
use App\Services\BranchService;
use App\Services\TenantService;
use CodeIgniter\Test\CIUnitTestCase;
use Config\AuthGroups;

/**
 * @internal
 */
final class ContextualRbacTest extends CIUnitTestCase
{
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

        $this->assertTrue($this->authService->can('sale.view.own', $superAdmin));
        $this->assertTrue($this->authService->can('sale.view.all', $superAdmin));
        $this->assertTrue($this->authService->can('user.manage.all', $superAdmin));
        $this->assertTrue($this->authService->can('anything.whatever.scope', $superAdmin));
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
}
