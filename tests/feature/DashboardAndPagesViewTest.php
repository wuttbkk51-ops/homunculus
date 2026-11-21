<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * ทดสอบการแสดงผลหน้า Dashboard, Profile และ Settings ตามตัวอย่างของ AdminLTE
 *
 * @internal
 */
final class DashboardAndPagesViewTest extends CIUnitTestCase
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
     * ทดสอบว่า route '/' แสดงผลหน้า Home สำเร็จสำหรับ Guest (ยังไม่ login)
     */
    public function testIndexRendersHomePageForGuest(): void
    {
        if (auth()->loggedIn()) {
            auth()->logout();
        }

        $result = $this->get('/');

        $result->assertOK();
        $result->assertSee(esc(lang('App.appName')));
        $result->assertSee(esc(lang('Home.adminPortal')));
        $result->assertSee(site_url('admin'));
    }

    /**
     * ทดสอบว่า route '/' จะ redirect ไปยัง '/admin' สำหรับผู้ใช้ที่ login แล้ว
     */
    public function testIndexRedirectsToAdminForAuthenticatedUser(): void
    {
        $userModel = new \CodeIgniter\Shield\Models\UserModel();
        $unique    = uniqid();
        $user      = new \CodeIgniter\Shield\Entities\User([
            'username' => "home_user_{$unique}",
            'email'    => "home_user_{$unique}@example.com",
            'password' => 'secret_123',
            'active'   => 1,
        ]);
        $userId    = $userModel->insert($user, true);
        $savedUser = $userModel->find($userId);

        auth()->login($savedUser);

        $result = $this->get('/');
        $result->assertRedirectTo('/admin');

        auth()->logout();
    }

    /**
     * ทดสอบการเรนเดอร์ View ของ Dashboard
     */
    public function testDashboardViewRendering(): void
    {
        $html = view('dashboard/index', [
            'title' => lang('Dashboard.title'),
        ]);

        $this->assertStringContainsString('small-box text-bg-primary', $html);
        $this->assertStringContainsString('small-box text-bg-success', $html);
        $this->assertStringContainsString('small-box text-bg-warning', $html);
        $this->assertStringContainsString('small-box text-bg-danger', $html);
        $this->assertStringContainsString('Enterprise License', $html);
        $this->assertStringContainsString('theme-luxury.css', $html);
    }

    /**
     * ทดสอบการเรนเดอร์ View ของ Profile
     */
    public function testProfileViewRendering(): void
    {
        $html = view('profile/index', [
            'title' => lang('Profile.title'),
        ]);

        $this->assertStringContainsString('profileTab', $html);
        $this->assertStringContainsString('activity-pane', $html);
        $this->assertStringContainsString('timeline-pane', $html);
        $this->assertStringContainsString('settings-pane', $html);
        $this->assertStringContainsString(esc(lang('Profile.aboutMe')), $html);
        $this->assertStringContainsString(esc(lang('Profile.education')), $html);
    }

    /**
     * ทดสอบการเรนเดอร์ View ของ Settings
     */
    public function testSettingsViewRendering(): void
    {
        $html = view('settings/index', [
            'title' => lang('Settings.title'),
        ]);

        $this->assertStringContainsString('settings-nav', $html);
        $this->assertStringContainsString('account-pane', $html);
        $this->assertStringContainsString('notifications-pane', $html);
        $this->assertStringContainsString('security-pane', $html);
        $this->assertStringContainsString('danger-pane', $html);
        $this->assertStringContainsString(esc(lang('Settings.account')), $html);
        $this->assertStringContainsString(esc(lang('Settings.dangerZone')), $html);
    }

    /**
     * ทดสอบว่า route ที่มีการป้องกันสิทธิ์ ('dashboard', 'profile', 'settings') จะ redirect unauthenticated user ไปยัง /login
     */
    public function testProtectedRoutesRedirectToLogin(): void
    {
        $admin = $this->get('admin');
        $admin->assertRedirectTo('/login');

        $dashboard = $this->get('dashboard');
        $dashboard->assertRedirectTo('/login');

        $profile = $this->get('profile');
        $profile->assertRedirectTo('/login');

        $settings = $this->get('settings');
        $settings->assertRedirectTo('/login');
    }
}
