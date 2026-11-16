<?php

namespace Tests\Feature;

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
final class AuthFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    public function testAdminRouteIsGuardedWhenUnauthenticated(): void
    {
        // เมื่อไม่ได้ล็อกอินเข้าใช้งาน /admin ต้องถูก redirect ไปที่ /login
        $result = $this->get('admin');
        $result->assertRedirectTo('login');
    }

    public function testSelectBranchViewRedirectsWhenNotLoggedIn(): void
    {
        $result = $this->get('auth/select-branch');
        $result->assertRedirectTo('login');
    }

    public function testTenantSuspendedPageIsAccessible(): void
    {
        $result = $this->get('auth/tenant-suspended');
        $result->assertOK();
        $result->assertSee('บัญชีองค์กรถูกระงับการใช้งาน');
    }
}
