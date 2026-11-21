<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * ทดสอบการแสดงผลหน้า Starter Template และ Master Layout
 *
 * @internal
 */
final class StarterViewTest extends CIUnitTestCase
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
     * ทดสอบว่า route '/starter' แสดงผลหน้า Starter สำเร็จและมีโครงสร้าง AdminLTE
     */
    public function testStarterRendersStarterTemplate(): void
    {
        $result = $this->get('starter');

        $result->assertOK();
        $result->assertSee('assets/adminlte/css/adminlte.min.css');
        $result->assertSee('assets/adminlte/js/adminlte.min.js');
        $result->assertSee('assets/css/noto-sans-thai.css');
        $result->assertSee('app-wrapper');
        $result->assertSee('app-sidebar');
        $result->assertSee('starter-page-badge');
        $result->assertSee('Page specific script for starter page');
    }

    /**
     * ทดสอบว่า route '/starter' แสดงผลสำเร็จและมีการ์ดเริ่มต้น
     */
    public function testStarterRouteRendersStarterCard(): void
    {
        $result = $this->get('starter');

        $result->assertOK();
        $result->assertSee('card-title');
        $result->assertSee('data-lte-toggle="card-collapse"');
        $result->assertSee('data-lte-toggle="sidebar"');
        $result->assertSee(esc(lang('App.starterPage')), 'title');
        $result->assertSee(esc(lang('App.appName')), 'title');
        $result->assertSee(esc(lang('App.starterPage')), 'h1');
        $result->assertSee('breadcrumb-item active');
    }

    /**
     * ทดสอบว่าระบบ layout จัดการ breadcrumbs จาก array ได้อย่างถูกต้อง
     */
    public function testCustomBreadcrumbsArrayRendering(): void
    {
        $rendered = view('starter', [
            'title'       => 'Custom Page',
            'breadcrumbs' => [
                ['title' => 'Parent Menu', 'url' => '/parent'],
                ['title' => 'Custom Page'],
            ],
        ]);

        $this->assertStringContainsString('<title>Custom Page - ' . esc(lang('App.appName')) . '</title>', $rendered);
        $this->assertStringContainsString('href="/parent"', $rendered);
        $this->assertStringContainsString('Parent Menu', $rendered);
        $this->assertStringContainsString('<li class="breadcrumb-item active" aria-current="page">Custom Page</li>', $rendered);
    }

    /**
     * ทดสอบว่า master layout มีการประกาศ renderSection สำหรับ content, contentStyles และ contentScript
     */
    public function testMasterLayoutContainsCssAndScriptRenderSections(): void
    {
        $masterContent = file_get_contents(APPPATH . 'Views/layouts/master.php');

        $this->assertNotFalse($masterContent);
        $this->assertStringContainsString("renderSection('contentStyles')", $masterContent);
        $this->assertStringContainsString("renderSection('content')", $masterContent);
        $this->assertStringContainsString("renderSection('contentScript')", $masterContent);
    }
}
