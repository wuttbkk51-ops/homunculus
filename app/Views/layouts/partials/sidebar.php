<aside class="app-sidebar bg-navy shadow" data-bs-theme="dark">
    <!-- Brand Logo -->
    <div class="sidebar-brand border-bottom border-secondary border-opacity-25">
        <a href="<?= site_url('dashboard') ?>" class="brand-link text-decoration-none">
            <i class="bi bi-cpu-fill brand-image text-primary fs-4 ms-2 me-2"></i>
            <span class="brand-text fw-bold text-white">Homunculus</span>
        </a>
    </div>

    <!-- Sidebar Content & Navigation (Clean & Minimal) -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <!-- 1. Dashboard (แดชบอร์ด) -->
                <?= navItem('dashboard', 'dashboard') ?>

                <?= navHeader('ระบบงานหลัก') ?>

                <!-- 2. Tenants (ผู้เช่า & องค์กร) -->
                <?= navTree('tenants', [
                    navItem('tenant.view.*', 'admin/tenants', 'รายชื่อองค์กร / ผู้เช่า'),
                    navItem('tenant.create.*', 'admin/tenants/new', 'ลงทะเบียนผู้เช่าใหม่'),
                ]) ?>

                <!-- 3. Users (ผู้ใช้งาน & พนักงาน) -->
                <?= navTree('users', [
                    navItem('user.view.all', 'admin/users', 'รายชื่อพนักงาน'),
                    navItem('user.manage.all', 'admin/roles', 'จัดการสิทธิ์ & บทบาท'),
                ]) ?>

                <!-- 4. Items (สินค้า & สต็อก) -->
                <?= navTree('items', [
                    navItem('product.view.all', 'admin/products', 'รายการสินค้า'),
                    navItem('product.view.all', 'admin/categories', 'หมวดหมู่สินค้า'),
                    navItem('inventory.view.*', 'admin/inventory', 'สต็อกคงเหลือ'),
                ]) ?>

                <!-- 5. Sales (การขาย & POS - สามารถผสมผสาน can() แมนนวลได้) -->
                <?= navTree('sales', [
                    can('sale.create.branch') ? navItem(null, 'admin/pos', ['label' => 'เปิดบิลขายหน้าร้าน (POS)', 'icon' => 'bi bi-shop']) : '',
                    navItem('sale.view.*', 'admin/sales', 'รายการขายทั้งหมด'),
                    navItem('customer.view.*', 'admin/customers', 'ข้อมูลลูกค้า & สมาชิก'),
                ]) ?>

                <?= navHeader('ตั้งค่าระบบ') ?>

                <!-- 6. Settings (ตั้งค่าระบบ & สาขา) -->
                <?= navTree('settings', [
                    navItem('admin.settings', 'admin/settings', 'ตั้งค่าทั่วไป'),
                    navItem('branch.view.all', 'admin/branches', 'จัดการสาขา'),
                ]) ?>

            </ul>
        </nav>
    </div>
</aside>
