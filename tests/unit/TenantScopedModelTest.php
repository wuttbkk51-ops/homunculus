<?php

namespace Tests\Unit;

use App\Models\BaseTenantModel;
use App\Models\TenantModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Dummy Model สำหรับทดสอบ BaseTenantModel และ Auto Scoping
 */
class DummyItemModel extends BaseTenantModel
{
    protected $table          = 'branches'; // ใช้ตาราง branches ที่มี tenant_id ในการทดสอบ
    protected $primaryKey     = 'id';
    protected $allowedFields  = ['tenant_id', 'name', 'code'];
    protected $useTimestamps  = true;
}

/**
 * @internal
 */
final class TenantScopedModelTest extends CIUnitTestCase
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

    public function testAutoInjectTenantIdOnInsert(): void
    {
        $tenantModel = model(TenantModel::class);
        $tenantId    = (int) $tenantModel->insert([
            'name'   => 'Org 1',
            'code'   => 'ORG1',
            'status' => 'active',
        ]);

        // จำลอง Session ที่มี Tenant ID
        session()->set('active_branch', [
            'id'          => 1,
            'name'        => 'สาขาทดสอบ',
            'code'        => 'B01',
            'tenant_id'   => $tenantId,
            'branch_role' => 'manager',
        ]);

        $model = new DummyItemModel();

        // Developer สั่ง insert โดยไม่ได้ส่ง tenant_id เข้าไป
        $insertId = $model->insert([
            'name' => 'สินค้าทดสอบ',
            'code' => 'P001',
        ]);

        $this->assertIsInt($insertId);

        // ตรวจสอบใน DB โดยตรงว่า tenant_id ถูก inject ให้อัตโนมัติ
        $row = $this->db->table('branches')->where('id', $insertId)->get()->getRowArray();
        $this->assertSame((string) $tenantId, (string) $row['tenant_id']);
    }

    public function testAutoScopeFindOnlyCurrentTenant(): void
    {
        $tenantModel = model(TenantModel::class);
        $t1 = (int) $tenantModel->insert(['name' => 'Org A', 'code' => 'ORGA', 'status' => 'active']);
        $t2 = (int) $tenantModel->insert(['name' => 'Org B', 'code' => 'ORGB', 'status' => 'active']);

        $db = $this->db;

        // ใส่ข้อมูลสาขาของ Tenant 1 และ Tenant 2
        $db->table('branches')->insert([
            'tenant_id' => $t1,
            'name'      => 'ขององค์กรที่ 1',
            'code'      => 'T1-01',
        ]);
        $id1 = $db->insertID();

        $db->table('branches')->insert([
            'tenant_id' => $t2,
            'name'      => 'ขององค์กรที่ 2',
            'code'      => 'T2-01',
        ]);
        $id2 = $db->insertID();

        // จำลอง Session ผู้ใช้เข้าทำงานใน Tenant 1
        session()->set('active_branch', [
            'id'          => 1,
            'name'        => 'สาขา T1',
            'code'        => 'B01',
            'tenant_id'   => $t1,
            'branch_role' => 'manager',
        ]);

        $model = new DummyItemModel();

        // เมื่อสั่ง findAll() ต้องพบเฉพาะข้อมูลของ Tenant 1 เท่านั้น
        $items = $model->findAll();
        $this->assertNotEmpty($items);
        foreach ($items as $item) {
            $this->assertSame((string) $t1, (string) $item['tenant_id']);
        }

        // เมื่อสั่ง find($id2) ของ Tenant 2 ต้องไม่พบ (ป้องกัน IDOR)
        $this->assertNull($model->find($id2));

        // เมื่อใช้ withoutTenant() จะสามารถดึงข้อมูลข้าม Tenant ได้
        $allItems = $model->withoutTenant()->findAll();
        $this->assertGreaterThanOrEqual(2, count($allItems));
    }
}
