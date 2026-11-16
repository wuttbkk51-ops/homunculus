<aside class="app-sidebar bg-navy shadow" data-bs-theme="dark">
    <!-- Brand Logo -->
    <div class="sidebar-brand border-bottom border-secondary border-opacity-25">
        <a href="<?= site_url('dashboard') ?>" class="brand-link text-decoration-none">
            <i class="bi bi-cpu-fill brand-image text-primary fs-4 ms-2 me-2"></i>
            <span class="brand-text fw-bold text-white">Homunculus</span>
        </a>
    </div>

    <!-- Sidebar Content & Navigation (Dev จัดวางลำดับเมนูได้อย่างอิสระ) -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <!-- 1. Dashboard (แดชบอร์ด) - เมนูเดี่ยวด้วย navItem() -->
                <?= navItem('dashboard', 'dashboard', [
                    'active' => admin_menu_is_active('dashboard'),
                ]) ?>

                <!-- Header หมวดหมู่ -->
                <li class="nav-header">ระบบงานหลัก</li>

                <!-- 2. Tenants (ผู้เช่า & องค์กร) - Treeview ครอบด้วย can() + navItem() -->
                <?php if (can('tenant.*')): ?>
                    <li class="nav-item <?= admin_menu_is_active('admin/tenants') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= admin_menu_is_active('admin/tenants') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-buildings"></i>
                            <p>
                                <?= lang('Tenants.tenants') ?>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?= navItem('tenant.view.*', 'admin/tenants', [
                                'label'  => 'รายชื่อองค์กร / ผู้เช่า',
                                'icon'   => 'bi bi-buildings',
                                'active' => admin_menu_is_active('admin/tenants'),
                            ]) ?>
                            <?= navItem('tenant.create.*', 'admin/tenants/new', [
                                'label'  => 'ลงทะเบียนผู้เช่าใหม่',
                                'icon'   => 'bi bi-building-add',
                                'active' => admin_menu_is_active('admin/tenants/new'),
                            ]) ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 3. Users (ผู้ใช้งาน & พนักงาน) - Treeview พร้อมตัวอย่าง can() ครอบ HTML แบบแมนนวล -->
                <?php if (can('user.*')): ?>
                    <li class="nav-item <?= admin_menu_is_active('admin/users') || admin_menu_is_active('admin/roles') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= admin_menu_is_active('admin/users') || admin_menu_is_active('admin/roles') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-people"></i>
                            <p>
                                <?= lang('Users.users') ?>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- เรียกผ่าน helper navItem() -->
                            <?= navItem('user.view.all', 'admin/users', [
                                'label'  => 'รายชื่อพนักงาน',
                                'icon'   => 'bi bi-people',
                                'active' => admin_menu_is_active('admin/users'),
                            ]) ?>

                            <!-- หรือเขียนแมนนวลครอบด้วย can() ได้ตามต้องการ -->
                            <?php if (can('user.manage.all')): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('admin/roles') ?>" class="nav-link <?= admin_menu_is_active('admin/roles') ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-shield-lock"></i>
                                        <p>จัดการสิทธิ์ & บทบาท</p>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 4. Items (สินค้า & สต็อก) -->
                <?php if (can('product.*') || can('inventory.*')): ?>
                    <li class="nav-item <?= admin_menu_is_active('admin/products') || admin_menu_is_active('admin/categories') || admin_menu_is_active('admin/inventory') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= admin_menu_is_active('admin/products') || admin_menu_is_active('admin/categories') || admin_menu_is_active('admin/inventory') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-box-seam"></i>
                            <p>
                                <?= lang('Items.items') ?>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?= navItem('product.view.all', 'admin/products', [
                                'label'  => 'รายการสินค้า',
                                'icon'   => 'bi bi-tags',
                                'active' => admin_menu_is_active('admin/products'),
                            ]) ?>
                            <?= navItem('product.view.all', 'admin/categories', [
                                'label'  => 'หมวดหมู่สินค้า',
                                'icon'   => 'bi bi-grid',
                                'active' => admin_menu_is_active('admin/categories'),
                            ]) ?>
                            <?= navItem('inventory.view.*', 'admin/inventory', [
                                'label'  => 'สต็อกคงเหลือ',
                                'icon'   => 'bi bi-boxes',
                                'active' => admin_menu_is_active('admin/inventory'),
                            ]) ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 5. Sales (การขาย & POS) -->
                <?php if (can('sale.*') || can('customer.*')): ?>
                    <li class="nav-item <?= admin_menu_is_active('admin/pos') || admin_menu_is_active('admin/sales') || admin_menu_is_active('admin/customers') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= admin_menu_is_active('admin/pos') || admin_menu_is_active('admin/sales') || admin_menu_is_active('admin/customers') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-cart3"></i>
                            <p>
                                <?= lang('Sales.sales') ?>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- ตัวอย่างเขียนแมนนวลครอบด้วย can() สำหรับ POS -->
                            <?php if (can('sale.create.branch')): ?>
                                <li class="nav-item">
                                    <a href="<?= site_url('admin/pos') ?>" class="nav-link <?= admin_menu_is_active('admin/pos') ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-shop"></i>
                                        <p>เปิดบิลขายหน้าร้าน (POS)</p>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?= navItem('sale.view.*', 'admin/sales', [
                                'label'  => 'รายการขายทั้งหมด',
                                'icon'   => 'bi bi-receipt',
                                'active' => admin_menu_is_active('admin/sales'),
                            ]) ?>
                            <?= navItem('customer.view.*', 'admin/customers', [
                                'label'  => 'ข้อมูลลูกค้า & สมาชิก',
                                'icon'   => 'bi bi-person-lines-fill',
                                'active' => admin_menu_is_active('admin/customers'),
                            ]) ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Header หมวดหมู่ -->
                <li class="nav-header">ตั้งค่าระบบ</li>

                <!-- 6. Settings (ตั้งค่าระบบ & จัดการสาขา) -->
                <?php if (can('admin.settings') || can('branch.view.all')): ?>
                    <li class="nav-item <?= admin_menu_is_active('admin/settings') || admin_menu_is_active('admin/branches') ? 'menu-open' : '' ?>">
                        <a href="#" class="nav-link <?= admin_menu_is_active('admin/settings') || admin_menu_is_active('admin/branches') ? 'active' : '' ?>">
                            <i class="nav-icon bi bi-gear"></i>
                            <p>
                                <?= lang('Settings.settings') ?>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?= navItem('admin.settings', 'admin/settings', [
                                'label'  => 'ตั้งค่าทั่วไป',
                                'icon'   => 'bi bi-sliders',
                                'active' => admin_menu_is_active('admin/settings'),
                            ]) ?>
                            <?= navItem('branch.view.all', 'admin/branches', [
                                'label'  => 'จัดการสาขา',
                                'icon'   => 'bi bi-building',
                                'active' => admin_menu_is_active('admin/branches'),
                            ]) ?>
                        </ul>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>
