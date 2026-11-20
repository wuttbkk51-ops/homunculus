<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filters\AuthFilter;
use CodeIgniter\Filters\Filters;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Filters as FiltersConfig;

/**
 * ทดสอบการทำงานของ AuthFilter ในระดับ Global Filter และการยกเว้น public routes
 *
 * @internal
 */
final class AuthGlobalFilterTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = true;
    protected $refresh     = false;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();

        // เคลียร์สถานะ auth และ session ให้เป็น guest เสมอก่อนเริ่ม test
        if (auth()->loggedIn()) {
            auth()->logout();
        }
        session()->destroy();
    }

    /**
     * ทดสอบว่า route สาธารณะ (public routes) ได้รับการยกเว้นจาก AuthFilter
     */
    public function testPublicRoutesAreExcludedFromAuthFilter(): void
    {
        $config  = config(FiltersConfig::class);
        $request = service('incomingrequest');
        $filters = new Filters($config, $request, service('response'));

        $publicPaths = [
            '',
            'login',
            'login/magic-link',
            'login/verify-magic-link',
            'register',
            'auth/a/show',
            'auth/a/handle',
            'logout',
        ];

        foreach ($publicPaths as $path) {
            $filters->reset();
            $filters->initialize($path);
            $activeFilters = $filters->getFilters();

            $this->assertNotContains(
                'tenant_auth',
                $activeFilters['before'],
                "Route '{$path}' ไม่ควรติด tenant_auth ใน before",
            );
            $this->assertNotContains(
                'tenant_auth',
                $activeFilters['after'],
                "Route '{$path}' ไม่ควรติด tenant_auth ใน after",
            );
        }
    }

    /**
     * ทดสอบว่า route ที่ไม่ได้ยกเว้น (protected routes) จะถูกตรวจด้วย AuthFilter เสมอ
     */
    public function testProtectedRoutesIncludeAuthFilter(): void
    {
        $config  = config(FiltersConfig::class);
        $request = service('incomingrequest');
        $filters = new Filters($config, $request, service('response'));

        $protectedPaths = [
            'dashboard',
            'api/users',
            'admin/settings',
        ];

        foreach ($protectedPaths as $path) {
            $filters->reset();
            $filters->initialize($path);
            $activeFilters = $filters->getFilters();

            $this->assertContains(
                'tenant_auth',
                $activeFilters['before'],
                "Route '{$path}' ต้องมี tenant_auth ใน before",
            );
            $this->assertContains(
                'tenant_auth',
                $activeFilters['after'],
                "Route '{$path}' ต้องมี tenant_auth ใน after",
            );
        }
    }

    /**
     * ทดสอบ HTTP Request เข้า route ที่ไม่ได้ยกเว้น โดยไม่ login ต้องถูกดักด้วย 401
     */
    public function testUnauthenticatedRequestToProtectedRouteReturns401(): void
    {
        $routes = service('routes');
        $routes->get('api/secure-data', static fn () => 'ok');

        $result = $this->get('api/secure-data');

        $result->assertStatus(401);
        $result->assertJSONFragment([
            'status' => 'error',
            'code'   => 'loginRequired',
        ]);
    }

    /**
     * ทดสอบ HTTP Request เข้า route สาธารณะ เช่น / หรือ login ไม่ถูก intercept ด้วย 401
     */
    public function testPublicRouteRequestIsNotInterceptedWith401(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);

        $loginResult = $this->get('login');
        $loginResult->assertStatus(200);
    }
}
