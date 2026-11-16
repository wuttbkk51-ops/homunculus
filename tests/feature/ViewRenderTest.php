<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Entities\User;
use App\Models\BranchModel;
use App\Models\TenantModel;
use App\Models\UserBranchModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class ViewRenderTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        session()->remove('active_branch');
        if (auth()->loggedIn()) {
            auth()->logout();
        }
    }

    public function testHomePageRendersSuccessfully(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        $result->assertSee('Homunculus');
        $result->assertSee('เข้าสู่ระบบ Admin');
        $result->assertSee('จุดเด่นและความสามารถหลัก');
        $result->assertSee('Contextual RBAC');
    }

    public function testLoginPageRendersAdminLteStyle(): void
    {
        $result = $this->get('login');

        $result->assertOK();
        $result->assertSee('login-box');
        $result->assertSee('card-outline card-primary');
        $result->assertSee('login-card-body');
        $result->assertSee('Homunculus');
    }

    public function testSidebarMenuHelperWorks(): void
    {
        helper(['auth', 'menu']);

        $menuHtml = render_sidebar_menu();

        $this->assertStringContainsString('sidebar-menu', $menuHtml);
        $this->assertStringContainsString('แดชบอร์ด', $menuHtml);
    }

    public function testDashboardRendersWithAdminLteTemplate(): void
    {
        // 1. Seed Tenant
        $tenantModel = model(TenantModel::class);
        $tenantId    = (int) $tenantModel->insert([
            'name'   => 'บริษัท ทดสอบ จำกัด',
            'code'   => 'TEST-TNT',
            'status' => 'active',
        ]);

        // 2. Seed Branch
        $branchModel = model(BranchModel::class);
        $branchId    = (int) $branchModel->insert([
            'tenant_id' => $tenantId,
            'name'      => 'สาขาใหญ่',
            'code'      => 'HQ',
            'is_active' => 1,
        ]);

        // 3. Seed User
        $userModel = model(UserModel::class);
        $userId    = (int) $userModel->insert([
            'username'  => 'adminuser',
            'tenant_id' => $tenantId,
            'active'    => 1,
        ]);
        /** @var User $user */
        $user = $userModel->findById($userId);

        // 4. Assign Branch
        $userBranchModel = model(UserBranchModel::class);
        $userBranchModel->insert([
            'user_id'     => $userId,
            'branch_id'   => $branchId,
            'tenant_id'   => $tenantId,
            'branch_role' => 'manager',
        ]);

        // 5. Authenticate & Setup Session
        auth()->login($user);
        session()->set('active_branch', [
            'id'          => $branchId,
            'name'        => 'สาขาใหญ่',
            'code'        => 'HQ',
            'tenant_id'   => $tenantId,
            'branch_role' => 'manager',
        ]);

        // 6. Request Dashboard
        $result = $this->get('dashboard');

        $result->assertOK();
        $result->assertSee('app-wrapper');
        $result->assertSee('app-sidebar');
        $result->assertSee('app-header');
        $result->assertSee('แดชบอร์ดผู้ดูแลระบบ');
        $result->assertSee('สาขาใหญ่');
        $result->assertSee('manager');
        $result->assertSee('Contextual State');
        $result->assertSee('Permission-First');
        $result->assertSee('app-content-header');
        $result->assertSee('breadcrumb');
        $result->assertSee('navbar_helper');
        // ตรวจสอบว่า custom styles & scripts block ทำงานได้จริง
        $result->assertSee('dashboard-stat-card');
        $result->assertSee('Homunculus Dashboard Initialized');
    }
}
