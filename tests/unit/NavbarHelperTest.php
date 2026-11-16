<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Entities\User;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Unit Test สำหรับ navbar_helper.php
 * ทดสอบการทำงานของ navbar(), module mapping, default icon, i18n, และ page header / breadcrumbs
 *
 * @internal
 */
final class NavbarHelperTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    protected function setUp(): void
    {
        parent::setUp();
        helper(['auth', 'navbar']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (auth()->loggedIn()) {
            auth()->logout();
        }
    }

    /**
     * ทดสอบ navbar_resolve_module() ในการแปลง module alias ไปเป็น wildcard permission
     */
    public function testModuleResolution(): void
    {
        $this->assertSame('sale.*', navbar_resolve_module('sales'));
        $this->assertSame('sale.*', navbar_resolve_module('sale'));
        $this->assertSame('product.*', navbar_resolve_module('items'));
        $this->assertSame('product.*', navbar_resolve_module('item'));
        $this->assertSame('product.*', navbar_resolve_module('products'));
        $this->assertSame('user.*', navbar_resolve_module('users'));
        $this->assertSame('branch.*', navbar_resolve_module('branches'));
        $this->assertSame('report.*', navbar_resolve_module('reports'));
        $this->assertSame('admin.settings', navbar_resolve_module('settings'));
        $this->assertSame('sale.create.branch', navbar_resolve_module('sale.create.branch'));
        $this->assertSame('custom_module.*', navbar_resolve_module('custom_module'));
    }

    /**
     * ทดสอบ navbar_default_icon()
     */
    public function testDefaultIconResolution(): void
    {
        $this->assertSame('bi bi-cart3', navbar_default_icon('sales'));
        $this->assertSame('bi bi-cart3', navbar_default_icon('sale'));
        $this->assertSame('bi bi-box-seam', navbar_default_icon('items'));
        $this->assertSame('bi bi-person-gear', navbar_default_icon('users'));
        $this->assertSame('bi bi-building', navbar_default_icon('branches'));
        $this->assertSame('bi bi-cart3', navbar_default_icon('sale.create.branch'));
    }

    /**
     * ทดสอบ navbar_resolve_module_file() สำหรับแมป key ไปยังไฟล์ภาษาประจำโมดูล
     */
    public function testModuleFileResolution(): void
    {
        $this->assertSame('Sales', navbar_resolve_module_file('sales'));
        $this->assertSame('Sales', navbar_resolve_module_file('sale'));
        $this->assertSame('Sales', navbar_resolve_module_file('sale.create.branch'));
        $this->assertSame('Items', navbar_resolve_module_file('items'));
        $this->assertSame('Items', navbar_resolve_module_file('product.create.all'));
        $this->assertSame('Users', navbar_resolve_module_file('users'));
        $this->assertSame('Dashboard', navbar_resolve_module_file('dashboard'));
        $this->assertSame('Reports', navbar_resolve_module_file('reports'));
        $this->assertSame('Settings', navbar_resolve_module_file('settings'));
    }

    /**
     * ทดสอบ navbar_resolve_label() จากไฟล์ภาษาแยกตามโมดูล (Dashboard, Users, Items, Sales, Reports, Settings)
     * กติกา: ดึงตรงตามชื่อ key (เช่น dashboard -> 'Dashboard.dashboard') และไม่มี 'module_name' อีกต่อไป
     */
    public function testLabelResolution(): void
    {
        service('language')->setLocale('th');

        // ตรวจสอบว่าไม่มี 'module_name' ใน Dashboard
        $this->assertSame('Dashboard.module_name', lang('Dashboard.module_name'));
        $this->assertSame('แดชบอร์ด', lang('Dashboard.dashboard'));

        // ตรวจสอบครบทั้ง 6 โมดูลหลักผ่าน navbar_resolve_label()
        $this->assertSame('แดชบอร์ด', navbar_resolve_label('dashboard'));
        $this->assertSame('การขาย & POS', navbar_resolve_label('sales'));
        $this->assertSame('สินค้า & สต็อก', navbar_resolve_label('items'));
        $this->assertSame('ผู้ใช้งาน & พนักงาน', navbar_resolve_label('users'));
        $this->assertSame('รายงาน & สถิติ', navbar_resolve_label('reports'));
        $this->assertSame('ตั้งค่าระบบ', navbar_resolve_label('settings'));

        // ตรวจสอบระดับ Granular
        $this->assertSame('เปิดบิลขายหน้าร้าน', navbar_resolve_label('sale.create.branch'));
        $this->assertSame('เพิ่มรายการสินค้าใหม่', navbar_resolve_label('product.create.all'));

        // ตรวจสอบ Override
        $this->assertSame('เปิดบิลด่วน', navbar_resolve_label('sale.create.branch', 'เปิดบิลด่วน'));
    }

    /**
     * ทดสอบเมื่อผู้ใช้ไม่มีสิทธิ์เข้าถึง navbar() ต้องคืนค่าสตริงว่าง '' ปลอดภัย 100%
     */
    public function testNavbarReturnsEmptyWhenUnauthorized(): void
    {
        // ยังไม่ได้ล็อกอิน
        $this->assertSame('', navbar('sales', 'admin/sales'));
        $this->assertSame('', navbar('sale.create.branch', 'admin/pos'));
        $this->assertSame('', navbar('users', 'admin/users'));
    }

    /**
     * ทดสอบเมื่อผู้ใช้มีสิทธิ์ (SuperAdmin) navbar() ต้องพ่น HTML DOM ที่ถูกต้องตาม options
     */
    public function testNavbarRendersDomForAuthorizedUser(): void
    {
        // สร้างและล็อกอิน SuperAdmin
        $userModel = model(UserModel::class);
        $superAdminId = (int) $userModel->insert([
            'username'  => 'superadmin_navbar',
            'tenant_id' => null,
            'active'    => 1,
        ]);

        /** @var User $savedUser */
        $savedUser = $userModel->findById($superAdminId);
        $savedUser->addGroup('admin');
        auth()->login($savedUser);

        service('language')->setLocale('th');

        // 1. ทดสอบแบบโมดูล default (nav-link)
        $html1 = navbar('sales', 'admin/sales');
        $this->assertStringContainsString('class="nav-link"', $html1);
        $this->assertStringContainsString('admin/sales', $html1);
        $this->assertStringContainsString('bi bi-cart3', $html1);
        $this->assertStringContainsString('การขาย', $html1);

        // 2. ทดสอบแบบ Button
        $html2 = navbar('sale.create.branch', 'admin/pos', [
            'as'    => 'button',
            'class' => 'btn btn-primary btn-sm',
            'icon'  => 'bi bi-plus-lg',
        ]);
        $this->assertStringContainsString('class="btn btn-primary btn-sm"', $html2);
        $this->assertStringContainsString('admin/pos', $html2);
        $this->assertStringContainsString('bi bi-plus-lg', $html2);
        $this->assertStringContainsString('เปิดบิลขายหน้าร้าน', $html2);

        // 3. ทดสอบแบบ Dropdown-Item พร้อม Override Label
        $html3 = navbar('users', 'admin/users', [
            'as'    => 'dropdown-item',
            'label' => 'จัดการพนักงานทั้งหมด',
        ]);
        $this->assertStringContainsString('class="dropdown-item"', $html3);
        $this->assertStringContainsString('admin/users', $html3);
        $this->assertStringContainsString('จัดการพนักงานทั้งหมด', $html3);

        // 4. ทดสอบแบบ Nav-Link มี Wrapper (<li>)
        $html4 = navbar('items', 'admin/products', ['wrapper' => true]);
        $this->assertStringStartsWith('<li class="nav-item">', $html4);
        $this->assertStringEndsWith('</li>', $html4);

        // 5. ทดสอบกรณีไม่ต้องการ Icon (icon = false)
        $html5 = navbar('sales', 'admin/sales', ['icon' => false]);
        $this->assertStringNotContainsString('<i class="', $html5);
    }

    /**
     * ทดสอบฟังก์ชัน Page Header Title และ Breadcrumbs
     */
    public function testPageHeaderAndBreadcrumbHelpers(): void
    {
        $titleHtml = page_header_title('ภาพรวมระบบ', 'คำอธิบายสั้น', 'bi bi-speedometer2');
        $this->assertStringContainsString('ภาพรวมระบบ', $titleHtml);
        $this->assertStringContainsString('คำอธิบายสั้น', $titleHtml);
        $this->assertStringContainsString('bi bi-speedometer2', $titleHtml);

        $breadcrumbHtml = render_breadcrumbs([
            'หน้าหลัก' => 'dashboard',
            'การขาย'   => 'admin/sales',
            'เปิดบิล'  => null,
        ]);
        $this->assertStringContainsString('breadcrumb', $breadcrumbHtml);
        $this->assertStringContainsString('dashboard', $breadcrumbHtml);
        $this->assertStringContainsString('admin/sales', $breadcrumbHtml);
        $this->assertStringContainsString('active', $breadcrumbHtml);
        $this->assertStringContainsString('เปิดบิล', $breadcrumbHtml);

        $fullHeaderHtml = render_page_header(
            'จัดการสินค้า',
            'หมวดหมู่และสต็อก',
            'bi bi-box-seam',
            ['หน้าหลัก' => 'dashboard', 'สินค้า' => null],
            ['<a href="#" class="btn btn-sm btn-primary">เพิ่มสินค้า</a>']
        );
        $this->assertStringContainsString('app-content-header', $fullHeaderHtml);
        $this->assertStringContainsString('จัดการสินค้า', $fullHeaderHtml);
        $this->assertStringContainsString('เพิ่มสินค้า', $fullHeaderHtml);
    }

    /**
     * ทดสอบ Helper Functions ที่ตั้งชื่อตาม AdminLTE 4 Native Classes:
     * navItem(), navLink(), dropdownItem(), navButton(), breadcrumb(), pageTitle(), pageHeader()
     */
    public function testAdminLteNativeHelpers(): void
    {
        // ล็อกอิน SuperAdmin
        $userModel = model(UserModel::class);
        $superAdminId = (int) $userModel->insert([
            'username'  => 'superadmin_native',
            'tenant_id' => null,
            'active'    => 1,
        ]);

        /** @var User $savedUser */
        $savedUser = $userModel->findById($superAdminId);
        $savedUser->addGroup('admin');
        auth()->login($savedUser);

        service('language')->setLocale('th');

        // 1. navItem() -> ต้องได้ <li class="nav-item"><a class="nav-link" ...
        $itemHtml = navItem('sales', 'admin/sales');
        $this->assertStringStartsWith('<li class="nav-item">', $itemHtml);
        $this->assertStringContainsString('class="nav-link"', $itemHtml);
        $this->assertStringContainsString('admin/sales', $itemHtml);
        $this->assertStringContainsString('การขาย', $itemHtml);
        $this->assertStringEndsWith('</li>', $itemHtml);

        // 2. navLink() -> ต้องได้ <a class="nav-link" ... โดยไม่มี <li>
        $linkHtml = navLink('sales', 'admin/sales');
        $this->assertStringStartsWith('<a href="', $linkHtml);
        $this->assertStringContainsString('class="nav-link"', $linkHtml);
        $this->assertStringNotContainsString('<li', $linkHtml);

        // 3. dropdownItem() -> ต้องได้ <a class="dropdown-item" ...
        $dropHtml = dropdownItem('users', 'admin/users');
        $this->assertStringStartsWith('<a href="', $dropHtml);
        $this->assertStringContainsString('class="dropdown-item"', $dropHtml);
        $this->assertStringContainsString('ผู้ใช้งาน', $dropHtml);

        // 4. navButton() -> ต้องได้ <a class="btn ...
        $btnHtml = navButton('pos', 'admin/pos');
        $this->assertStringStartsWith('<a href="', $btnHtml);
        $this->assertStringContainsString('class="btn btn-primary btn-sm"', $btnHtml);
        $this->assertStringContainsString('จุดขายหน้าร้าน', $btnHtml);

        // 5. pageTitle() & breadcrumb()
        $pTitle = pageTitle('สินค้า', 'คำอธิบาย', 'bi bi-box');
        $this->assertStringContainsString('สินค้า', $pTitle);
        $this->assertStringContainsString('bi bi-box', $pTitle);

        $bcHtml = breadcrumb(['หน้าหลัก' => 'dashboard', 'สินค้า' => null]);
        $this->assertStringContainsString('breadcrumb', $bcHtml);
        $this->assertStringContainsString('active', $bcHtml);

        // 6. pageHeader()
        $headerHtml = pageHeader('หัวข้อ', 'คำอธิบาย', 'bi bi-star', ['หน้าหลัก' => null], $btnHtml);
        $this->assertStringContainsString('app-content-header', $headerHtml);
        $this->assertStringContainsString('หัวข้อ', $headerHtml);
        $this->assertStringContainsString($btnHtml, $headerHtml);
    }
}
