<?php

namespace Tests\Unit;

use App\Entities\Tenant;
use App\Models\TenantModel;
use App\Services\TenantService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
final class TenantServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    protected function setUp(): void
    {
        parent::setUp();
        cache()->clean();
    }

    public function testGetTenantUsesCacheAndReturnsActiveTenant(): void
    {
        $tenantModel = model(TenantModel::class);
        $tenantId = $tenantModel->insert([
            'name'        => 'Test Tenant',
            'code'        => 'TEST01',
            'status'      => 'active',
            'expire_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
        ]);

        $service = new TenantService();

        // ดึงครั้งแรก (Cache Miss -> DB)
        $tenant = $service->getTenant((int) $tenantId);
        $this->assertInstanceOf(Tenant::class, $tenant);
        $this->assertSame('Test Tenant', $tenant->name);
        $this->assertTrue($service->isTenantActive((int) $tenantId));

        // ตรวจสอบว่ามีข้อมูลใน Cache
        $this->assertNotNull(cache("tenant_{$tenantId}"));

        // ล้าง Cache และทดสอบ Instant Invalidation
        $service->clearCache((int) $tenantId);
        $this->assertNull(cache("tenant_{$tenantId}"));
    }

    public function testTenantIsInactiveWhenSuspendedOrExpired(): void
    {
        $tenantModel = model(TenantModel::class);

        // 1. กรณี Suspended
        $suspendedId = $tenantModel->insert([
            'name'        => 'Suspended Tenant',
            'code'        => 'SUSP01',
            'status'      => 'suspended',
            'expire_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
        ]);

        // 2. กรณี Expired
        $expiredId = $tenantModel->insert([
            'name'        => 'Expired Tenant',
            'code'        => 'EXP01',
            'status'      => 'active',
            'expire_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $service = new TenantService();

        $this->assertFalse($service->isTenantActive((int) $suspendedId));
        $this->assertFalse($service->isTenantActive((int) $expiredId));
    }
}
