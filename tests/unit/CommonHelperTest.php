<?php

namespace Tests\Unit;

use App\Entities\User;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class CommonHelperTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    protected function tearDown(): void
    {
        parent::tearDown();
        session()->remove('active_branch');
        if (auth()->loggedIn()) {
            auth()->logout();
        }
    }

    public function testTenantAndBranchIdHelpers(): void
    {
        // 1. ก่อนล็อกอิน และยังไม่มี Session
        $this->assertNull(tenant_id());
        $this->assertNull(branch_id());
        $this->assertNull(active_branch());
        $this->assertNull(active_branch_role());
        $this->assertFalse(is_superadmin());

        // 2. ทดสอบตั้งค่า Session active_branch
        session()->set('active_branch', [
            'id'          => 42,
            'name'        => 'สาขาทดสอบ',
            'code'        => 'TEST-42',
            'tenant_id'   => 99,
            'branch_role' => 'manager',
        ]);

        $this->assertSame(42, branch_id());
        $this->assertSame(99, tenant_id());
        $this->assertSame('manager', active_branch_role());
        $this->assertIsArray(active_branch());
        $this->assertSame('สาขาทดสอบ', active_branch()['name']);
    }

    public function testTenantIdFromUserEntity(): void
    {
        $tenantModel = model(TenantModel::class);
        $userModel   = model(UserModel::class);

        $tenantId = (int) $tenantModel->insert([
            'name'   => 'Helper Org',
            'code'   => 'HORG',
            'status' => 'active',
        ]);

        // สร้างผู้ใช้คนแรก (ID = 1 คือ SuperAdmin)
        $userModel->insert([
            'username'  => 'dummy_admin',
            'tenant_id' => null,
            'active'    => 1,
        ]);

        $userId = (int) $userModel->insert([
            'username'  => 'helper_user',
            'tenant_id' => $tenantId,
            'active'    => 1,
        ]);

        /** @var User $user */
        $user = $userModel->findById($userId);

        auth()->login($user);

        // tenant_id() ต้องคืนค่าจาก User ได้โดยตรง แม้ไม่มี session active_branch
        session()->remove('active_branch');
        $this->assertSame($tenantId, tenant_id());
        $this->assertFalse(is_superadmin());
    }

    public function testIsSuperadminHelper(): void
    {
        $userModel = model(UserModel::class);

        $userId = (int) $userModel->insert([
            'username'  => 'superadmin_helper',
            'tenant_id' => null,
            'active'    => 1,
        ]);

        /** @var User $superAdmin */
        $superAdmin = $userModel->findById($userId);
        $superAdmin->addGroup('admin');

        auth()->login($superAdmin);

        $this->assertTrue(is_superadmin());
        $this->assertTrue(can('any.permission.key')); // SuperAdmin ได้รับสิทธิ์เสมอ
    }
}
