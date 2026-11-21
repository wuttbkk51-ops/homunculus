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
     * ทดสอบว่า route '/' เรนเดอร์ Small Box Widgets และ Overview ของ Dashboard
     */
    public function testIndexRendersDashboardWidgetsAndOverview(): void
    {
        $result = $this->get('/');

        $result->assertOK();
        $result->assertSee('small-box text-bg-primary');
        $result->assertSee('small-box text-bg-success');
        $result->assertSee('small-box text-bg-warning');
        $result->assertSee('small-box text-bg-danger');
        $result->assertSee(esc(lang('Dashboard.newOrders')));
        $result->assertSee(esc(lang('Dashboard.recentActivity')));
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
        $dashboard = $this->get('dashboard');
        $dashboard->assertRedirectTo('/login');

        $profile = $this->get('profile');
        $profile->assertRedirectTo('/login');

        $settings = $this->get('settings');
        $settings->assertRedirectTo('/login');
    }
}
