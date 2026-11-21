<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * ทดสอบการแสดงผลหน้า Auth (Login & Register) ตามตัวอย่างของ AdminLTE
 *
 * @internal
 */
final class AuthViewsTest extends CIUnitTestCase
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
    }

    /**
     * ทดสอบว่า route '/login' เรนเดอร์หน้าจอ Login ตามแบบ AdminLTE v2
     */
    public function testLoginViewRendersAdminLteLayout(): void
    {
        $result = $this->get('login');

        $result->assertOK();
        $result->assertSee('login-box');
        $result->assertSee('card-outline card-primary');
        $result->assertSee('name="email"');
        $result->assertSee('name="password"');
        $result->assertSee('name="remember"');
        $result->assertSee(esc(lang('Auth.login')));
    }

    /**
     * ทดสอบว่า route '/register' เรนเดอร์หน้าจอ Register ตามแบบ AdminLTE v2
     */
    public function testRegisterViewRendersAdminLteLayout(): void
    {
        $result = $this->get('register');

        $result->assertOK();
        $result->assertSee('register-box');
        $result->assertSee('card-outline card-primary');
        $result->assertSee('name="email"');
        $result->assertSee('name="username"');
        $result->assertSee('name="password"');
        $result->assertSee('name="password_confirm"');
        $result->assertSee(esc(lang('Auth.register')));
    }
}
