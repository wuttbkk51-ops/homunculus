<?php

namespace Tests\Unit;

use App\Models\BranchModel;
use App\Models\TenantModel;
use App\Models\UserBranchModel;
use App\Models\UserModel;
use App\Services\BranchService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class BranchServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    public function testBranchServiceGetAndSetActiveBranch(): void
    {
        $tenantModel     = model(TenantModel::class);
        $branchModel     = model(BranchModel::class);
        $userBranchModel = model(UserBranchModel::class);
        $userModel       = model(UserModel::class);

        // สร้าง Tenant
        $tenantId = (int) $tenantModel->insert([
            'name'   => 'Test Org',
            'code'   => 'TORG',
            'status' => 'active',
        ]);

        // สร้าง 2 สาขา
        $branch1Id = (int) $branchModel->insert([
            'tenant_id' => $tenantId,
            'name'      => 'สาขา 1',
            'code'      => 'B1',
        ]);
        $branch2Id = (int) $branchModel->insert([
            'tenant_id' => $tenantId,
            'name'      => 'สาขา 2',
            'code'      => 'B2',
        ]);

        // สร้าง User
        $userId = (int) $userModel->insert([
            'username'  => 'tester1',
            'active'    => 1,
            'tenant_id' => $tenantId,
        ]);

        // มอบหมายสาขา 1 เป็น manager
        $userBranchModel->insert([
            'tenant_id'   => $tenantId,
            'user_id'     => $userId,
            'branch_id'   => $branch1Id,
            'branch_role' => 'manager',
        ]);

        $service = new BranchService();

        // ตรวจสอบการดึงรายการสาขา
        $branches = $service->getUserBranches($userId);
        $this->assertCount(1, $branches);
        $this->assertSame('สาขา 1', $branches[0]['branch_name']);

        // ตรวจสอบสิทธิ์เข้าถึงสาขา
        $this->assertTrue($service->hasBranchAccess($userId, $branch1Id));
        $this->assertFalse($service->hasBranchAccess($userId, $branch2Id));

        // ทดสอบการตั้งค่า Active Branch
        $this->assertTrue($service->setActiveBranch($userId, $branch1Id));
        $this->assertSame($branch1Id, $service->getActiveBranchId());

        $activeData = $service->getActiveBranch();
        $this->assertSame('manager', $activeData['branch_role']);

        // ทดสอบตั้งค่าสาขาที่ไม่มีสิทธิ์ ต้องไม่สำเร็จ
        $this->assertFalse($service->setActiveBranch($userId, $branch2Id));

        // ทดสอบการล้างค่า
        $service->clearActiveBranch();
        $this->assertNull($service->getActiveBranchId());
    }
}
