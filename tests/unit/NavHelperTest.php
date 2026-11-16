<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Entities\User;
use App\Models\TenantModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Unit Test สำหรับ nav_helper.php
 * ทดสอบการทำงานของ navItem, navLink, dropdownItem, navButton, sidebarMenu, breadcrumb, pageHeader
 *
 * @internal
 */
final class NavHelperTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace   = null;
    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;

    protected function setUp(): void
    {
        parent::setUp();
        helper(['auth', 'nav']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (auth()->loggedIn()) {
            auth()->logout();
        }
    }

    /**
     * ทดสอบ nav_resolve_module() ในการแปลง module alias ไปเป็น wildcard permission
     */
    public function testModuleResolution(): void
    {
        $this->assertSame('sale.*', nav_resolve_module('sales'));
        $this->assertSame('sale.*', nav_resolve_module('sale'));
        $this->assertSame('product.*', nav_resolve_module('items'));
        $this->assertSame('product.*', nav_resolve_module('item'));
        $this->assertSame('product.*', nav_resolve_module('products'));
        $this->assertSame('user.*', nav_resolve_module('users'));
        $this->assertSame('branch.*', nav_resolve_module('branches'));
        $this->assertSame('report.*', nav_resolve_module('reports'));
        $this->assertSame('admin.settings', nav_resolve_module('settings'));
        $this->assertSame('sale.create.branch', nav_resolve_module('sale.create.branch'));
        $this->assertSame('custom_module.*', nav_resolve_module('custom_module'));
    }

    /**
     * ทดสอบ nav_default_icon()
     */
    public function testDefaultIconResolution(): void
    {
        $this->assertSame('bi bi-cart3', nav_default_icon('sales'));
        $this->assertSame('bi bi-cart3', nav_default_icon('sale'));
        $this->assertSame('bi bi-box-seam', nav_default_icon('items'));
        $this->assertSame('bi bi-person-gear', nav_default_icon('users'));
        $this->assertSame('bi bi-building', nav_default_icon('branches'));
        $this->assertSame('bi bi-cart3', nav_default_icon('sale.create.branch'));
    }

    /**
     * ทดสอบ nav_resolve_module_file() สำหรับแมป key ไปยังไฟล์ภาษาประจำโมดูล
     */
    public function testModuleFileResolution(): void
    {
        $this->assertSame('Sales', nav_resolve_module_file('sales'));
        $this->assertSame('Sales', nav_resolve_module_file('sale'));
        $this->assertSame('Sales', nav_resolve_module_file('sale.create.branch'));
        $this->assertSame('Items', nav_resolve_module_file('items'));
        $this->assertSame('Items', nav_resolve_module_file('product.create.all'));
        $this->assertSame('Users', nav_resolve_module_file('users'));
        $this->assertSame('Dashboard', nav_resolve_module_file('dashboard'));
        $this->assertSame('Reports', nav_resolve_module_file('reports'));
        $this->assertSame('Settings', nav_resolve_module_file('settings'));
    }

    /**
     * ทดสอบ nav_resolve_label() จากไฟล์ภาษาแยกตามโมดูล (Dashboard, Users, Items, Sales, Reports, Settings)
     * กติกา: ดึงตรงตามชื่อ key (เช่น dashboard -> 'Dashboard.dashboard') และไม่มี 'module_name' อีกต่อไป
     */
    public function testLabelResolution(): void
    {
        service('language')->setLocale('th');

        // ตรวจสอบว่าไม่มี 'module_name' ใน Dashboard
        $this->assertSame('Dashboard.module_name', lang('Dashboard.module_name'));
        $this->assertSame('แดชบอร์ด', lang('Dashboard.dashboard'));

        // ตรวจสอบครบทั้ง 6 โมดูลหลักผ่าน nav_resolve_label()
        $this->assertSame('แดชบอร์ด', nav_resolve_label('dashboard'));
        $this->assertSame('การขาย & POS', nav_resolve_label('sales'));
        $this->assertSame('สินค้า & สต็อก', nav_resolve_label('items'));
        $this->assertSame('ผู้ใช้งาน & พนักงาน', nav_resolve_label('users'));
        $this->assertSame('รายงาน & สถิติ', nav_resolve_label('reports'));
        $this->assertSame('ตั้งค่าระบบ', nav_resolve_label('settings'));

        // ตรวจสอบระดับ Granular
        $this->assertSame('เปิดบิลขายหน้าร้าน', nav_resolve_label('sale.create.branch'));
        $this->assertSame('เพิ่มรายการสินค้าใหม่', nav_resolve_label('product.create.all'));

        // ตรวจสอบ Override
        $this->assertSame('เปิดบิลด่วน', nav_resolve_label('sale.create.branch', 'เปิดบิลด่วน'));
    }

    /**
     * ทดสอบเมื่อผู้ใช้ไม่มีสิทธิ์เข้าถึง navItem(), navLink(), navButton() ต้องคืนค่าสตริงว่าง '' ปลอดภัย 100%
     */
    public function testNavReturnsEmptyWhenUnauthorized(): void
    {
        // ยังไม่ได้ล็อกอิน
        $this->assertSame('', navItem('sales', 'admin/sales'));
        $this->assertSame('', navLink('sale.create.branch', 'admin/pos'));
        $this->assertSame('', navButton('users', 'admin/users'));
    }

    /**
     * ทดสอบปุ่มหรือลิงก์ที่ไม่ต้องตรวจสอบสิทธิ์ (permission = null หรือ auth = false)
     * เช่น ลิงก์สาธารณะ, ปุ่มย้อนกลับ, ปุ่มออกจากระบบ
     */
    public function testHelpersWithoutAuthCheck(): void
    {
        // แม้ยังไม่ได้ล็อกอิน ฟังก์ชันต้องพ่น HTML DOM ออกมาได้ตามปกติ
        $btn = navButton(null, 'admin/back', [
            'label' => 'ย้อนกลับ',
            'icon'  => 'bi bi-arrow-left',
            'class' => 'btn btn-secondary btn-sm',
        ]);
        $this->assertNotEmpty($btn);
        $this->assertStringContainsString('admin/back', $btn);
        $this->assertStringContainsString('ย้อนกลับ', $btn);
        $this->assertStringContainsString('bi bi-arrow-left', $btn);
        $this->assertStringContainsString('btn-secondary', $btn);

        // navLink โดยไม่ต้องเช็คสิทธิ์
        $link = navLink(null, '/', ['label' => 'หน้าเว็บไซต์', 'icon' => 'bi bi-globe']);
        $this->assertNotEmpty($link);
        $this->assertStringContainsString('class="nav-link"', $link);
        $this->assertStringContainsString('หน้าเว็บไซต์', $link);

        // dropdownItem ออกจากระบบ (wrapper = true)
        $drop = dropdownItem(null, 'logout', [
            'label'   => 'ออกจากระบบ',
            'icon'    => 'bi bi-box-arrow-right',
            'wrapper' => true,
        ]);
        $this->assertStringStartsWith('<li><a href="', $drop);
        $this->assertStringContainsString('class="dropdown-item"', $drop);
        $this->assertStringContainsString('ออกจากระบบ', $drop);
        $this->assertStringEndsWith('</li>', $drop);

        // navItem แบบระบุ module แต่ข้ามการเช็คสิทธิ์ด้วย auth = false
        $unauthItem = navItem('sales', 'admin/sales', ['auth' => false]);
        $this->assertNotEmpty($unauthItem);
        $this->assertStringContainsString('class="nav-link"', $unauthItem);
        $this->assertStringContainsString('การขาย', $unauthItem);
    }

    /**
     * ทดสอบเมื่อผู้ใช้มีสิทธิ์ (SuperAdmin) navLink(), navButton(), dropdownItem(), navItem() ต้องพ่น HTML DOM ที่ถูกต้อง
     */
    public function testNavRendersDomForAuthorizedUser(): void
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

        // 1. ทดสอบแบบ navLink
        $html1 = navLink('sales', 'admin/sales');
        $this->assertStringContainsString('class="nav-link"', $html1);
        $this->assertStringContainsString('admin/sales', $html1);
        $this->assertStringContainsString('bi bi-cart3', $html1);
        $this->assertStringContainsString('การขาย', $html1);

        // 2. ทดสอบแบบ navButton
        $html2 = navButton('sale.create.branch', 'admin/pos', [
            'class' => 'btn btn-primary btn-sm',
            'icon'  => 'bi bi-plus-lg',
        ]);
        $this->assertStringContainsString('class="btn btn-primary btn-sm"', $html2);
        $this->assertStringContainsString('admin/pos', $html2);
        $this->assertStringContainsString('bi bi-plus-lg', $html2);
        $this->assertStringContainsString('เปิดบิลขายหน้าร้าน', $html2);

        // 3. ทดสอบแบบ dropdownItem พร้อม Override Label
        $html3 = dropdownItem('users', 'admin/users', [
            'label' => 'จัดการพนักงานทั้งหมด',
        ]);
        $this->assertStringContainsString('class="dropdown-item"', $html3);
        $this->assertStringContainsString('admin/users', $html3);
        $this->assertStringContainsString('จัดการพนักงานทั้งหมด', $html3);

        // 4. ทดสอบแบบ navItem ที่มี Wrapper (<li>)
        $html4 = navItem('items', 'admin/products');
        $this->assertStringStartsWith('<li class="nav-item">', $html4);
        $this->assertStringEndsWith('</li>', $html4);

        // 5. ทดสอบกรณีไม่ต้องการ Icon (icon = false)
        $html5 = navLink('sales', 'admin/sales', ['icon' => false]);
        $this->assertStringNotContainsString('<i class="', $html5);
    }

    /**
     * ทดสอบฟังก์ชัน Page Header Title และ Breadcrumbs (pageTitle, breadcrumb, pageHeader)
     */
    public function testPageHeaderAndBreadcrumbHelpers(): void
    {
        $titleHtml = pageTitle('ภาพรวมระบบ', 'คำอธิบายสั้น', 'bi bi-speedometer2');
        $this->assertStringContainsString('ภาพรวมระบบ', $titleHtml);
        $this->assertStringContainsString('คำอธิบายสั้น', $titleHtml);
        $this->assertStringContainsString('bi bi-speedometer2', $titleHtml);

        $breadcrumbHtml = breadcrumb([
            'หน้าหลัก' => 'dashboard',
            'การขาย'   => 'admin/sales',
            'เปิดบิล'  => null,
        ]);
        $this->assertStringContainsString('breadcrumb', $breadcrumbHtml);
        $this->assertStringContainsString('dashboard', $breadcrumbHtml);
        $this->assertStringContainsString('admin/sales', $breadcrumbHtml);
        $this->assertStringContainsString('active', $breadcrumbHtml);
        $this->assertStringContainsString('เปิดบิล', $breadcrumbHtml);

        $fullHeaderHtml = pageHeader(
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

        // 7. sidebarMenu() -> ทดสอบ Sidebar Treeview และ Dynamic Language/Icon Fetching
        $sidebarHtml = sidebarMenu();
        $this->assertStringContainsString('sidebar-menu', $sidebarHtml);
        $this->assertStringContainsString('แดชบอร์ด', $sidebarHtml);
        $this->assertStringContainsString('การขาย &amp; POS', $sidebarHtml);
        $this->assertStringContainsString('bi bi-cart3', $sidebarHtml);
        $this->assertStringContainsString('สินค้า &amp; สต็อก', $sidebarHtml);
        $this->assertStringContainsString('bi bi-box-seam', $sidebarHtml);
    }
}
