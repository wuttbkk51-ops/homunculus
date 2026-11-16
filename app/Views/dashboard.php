<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>แดชบอร์ด - Homunculus Admin<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Page-Specific Styles (สามารถใส่ Custom CSS เฉพาะหน้านี้ได้) -->
<style>
    .dashboard-stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dashboard-stat-card:hover {
        transform: translateY(-2px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('pageHeader') ?>
<?= pageTitle('แดชบอร์ดผู้ดูแลระบบ', 'ระบบบริหารจัดการและภาพรวมสิทธิ์การเข้าถึงข้อมูล', 'bi bi-speedometer2') ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<?= breadcrumb([
    'หน้าหลัก' => 'dashboard',
    'แดชบอร์ด' => null,
]) ?>
<?= $this->endSection() ?>

<?= $this->section('pageActions') ?>
<!-- Quick Action Buttons ที่ตรวจสอบสิทธิ์อัตโนมัติด้วย navButton() -->
<?= navButton('pos', 'admin/pos', ['class' => 'btn btn-primary btn-sm']) ?>
<?= navButton('sales', 'admin/sales', ['class' => 'btn btn-outline-primary btn-sm']) ?>
<?= navButton('items', 'admin/products', ['class' => 'btn btn-outline-secondary btn-sm']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Minimal Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-medium">สาขาปัจจุบัน</span>
                        <h5 class="fw-bold text-body-emphasis mb-0 mt-1">
                            <?= esc($activeBranch['name'] ?? 'ทุกสาขา (SuperAdmin)') ?>
                        </h5>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-medium">บทบาทในสาขานี้</span>
                        <h5 class="fw-bold text-body-emphasis mb-0 mt-1">
                            <?= esc($activeBranch['branch_role'] ?? ($user->isSuperAdmin() ? 'superadmin' : '-')) ?>
                        </h5>
                    </div>
                    <div class="bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-person-badge fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-medium">Tenant ID</span>
                        <h5 class="fw-bold text-body-emphasis mb-0 mt-1">
                            <?= esc(tenant_id() ?? 'NULL (SuperAdmin)') ?>
                        </h5>
                    </div>
                    <div class="bg-info-subtle text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-medium">ผู้ใช้งาน</span>
                        <h5 class="fw-bold text-body-emphasis mb-0 mt-1">
                            <?= esc($user->username ?? '-') ?>
                        </h5>
                    </div>
                    <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-person fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Cards -->
<div class="row g-4">
    <!-- บริบทผู้ใช้งาน (Contextual State) -->
    <div class="col-lg-5">
        <div class="card card-outline card-primary shadow-sm border-0 h-100">
            <div class="card-header bg-transparent py-3">
                <h5 class="card-title fw-bold text-body-emphasis mb-0">
                    <i class="bi bi-info-circle me-1 text-primary"></i>บริบทผู้ใช้งาน (Contextual State)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <tbody>
                            <tr>
                                <th class="text-secondary small ps-3" style="width: 40%;">User ID / Username</th>
                                <td class="pe-3 fw-medium">#<?= esc($user->id ?? '-') ?> (<?= esc($user->username ?? '-') ?>)</td>
                            </tr>
                            <tr>
                                <th class="text-secondary small ps-3">Tenant ID</th>
                                <td class="pe-3">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        <?= esc(tenant_id() ?? 'SuperAdmin (ไม่มี Tenant)') ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-secondary small ps-3">Active Branch</th>
                                <td class="pe-3">
                                    <?php if ($activeBranch): ?>
                                        <strong><?= esc($activeBranch['name']) ?></strong>
                                        <span class="text-secondary small">(Code: <?= esc($activeBranch['code']) ?>)</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis">SuperAdmin Mode</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-secondary small ps-3">Contextual Role</th>
                                <td class="pe-3">
                                    <span class="badge bg-primary">
                                        <?= esc($activeBranch['branch_role'] ?? ($user->isSuperAdmin() ? 'superadmin' : '-')) ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ผลการตรวจสอบสิทธิ์แบบบริบท (Contextual RBAC: auth_can) -->
    <div class="col-lg-7">
        <div class="card card-outline card-success shadow-sm border-0 h-100">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-body-emphasis mb-0">
                    <i class="bi bi-shield-check me-1 text-success"></i>ผลการตรวจสอบสิทธิ์แบบบริบท
                </h5>
                <span class="badge bg-secondary-subtle text-secondary small">Permission-First</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th class="ps-3">Permission Key</th>
                                <th>คำอธิบาย</th>
                                <th class="text-end pe-3">สถานะสิทธิ์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3"><code class="text-primary">sale.view.own</code></td>
                                <td class="small text-secondary">ดูยอดขายเฉพาะของตนเอง (.own)</td>
                                <td class="text-end pe-3">
                                    <?php if (auth_can('sale.view.own')): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i>อนุญาต (Allowed)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i>ปฏิเสธ (Denied)
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><code class="text-primary">sale.create.branch</code></td>
                                <td class="small text-secondary">เปิดบิลขายภายในสาขา (.branch)</td>
                                <td class="text-end pe-3">
                                    <?php if (auth_can('sale.create.branch')): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i>อนุญาต (Allowed)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i>ปฏิเสธ (Denied)
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><code class="text-primary">sale.delete.branch</code></td>
                                <td class="small text-secondary">ยกเลิกบิลขายภายในสาขา (.branch)</td>
                                <td class="text-end pe-3">
                                    <?php if (auth_can('sale.delete.branch')): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i>อนุญาต (Allowed)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i>ปฏิเสธ (Denied)
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><code class="text-primary">sale.view.all</code></td>
                                <td class="small text-secondary">ดูยอดขายรวมทุกสาขาใน Tenant (.all)</td>
                                <td class="text-end pe-3">
                                    <?php if (auth_can('sale.view.all')): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i>อนุญาต (Allowed)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i>ปฏิเสธ (Denied)
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="ps-3"><code class="text-primary">user.manage.all</code></td>
                                <td class="small text-secondary">จัดการผู้ใช้ทั้งหมดใน Tenant (.all)</td>
                                <td class="text-end pe-3">
                                    <?php if (auth_can('user.manage.all')): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle me-1"></i>อนุญาต (Allowed)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle me-1"></i>ปฏิเสธ (Denied)
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Navigation & Actions Generated by navbar_helper (AdminLTE Native Helpers) -->
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold text-body-emphasis mb-0">
                    <i class="bi bi-compass me-2 text-primary"></i>ระบบเมนูด่วน (AdminLTE Native Helpers)
                </h5>
                <span class="badge bg-primary-subtle text-primary small">navItem, navLink, dropdownItem, navButton</span>
            </div>
            <div class="card-body">
                <p class="text-secondary small mb-3">
                    เมนูด้านล่างนี้ถูกสร้างผ่านฟังก์ชัน Helper ที่ตั้งชื่อสอดคล้องกับ Native Class ของ AdminLTE 4 เช่น <code>navItem()</code>, <code>navLink()</code>, <code>dropdownItem()</code>, <code>navButton()</code> โดยระบบตรวจสอบสิทธิ์แบบบริบท (Contextual RBAC), ดึงไอคอนเริ่มต้น และดึงข้อความภาษาอัตโนมัติจาก <code>lang()</code>
                </p>

                <div class="row g-3">
                    <!-- การแสดงผลแบบ Buttons (navButton) -->
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                            <h6 class="fw-bold mb-2 text-body"><i class="bi bi-cursor me-1"></i>ปุ่มดำเนินการ (<code>navButton()</code>)</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?= navButton('pos', 'admin/pos', ['class' => 'btn btn-primary btn-sm']) ?>
                                <?= navButton('sales', 'admin/sales', ['class' => 'btn btn-success btn-sm']) ?>
                                <?= navButton('items', 'admin/products', ['class' => 'btn btn-info btn-sm text-white']) ?>
                                <?= navButton('users', 'admin/users', ['class' => 'btn btn-secondary btn-sm']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- การแสดงผลแบบ Nav Links (navLink) -->
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                            <h6 class="fw-bold mb-2 text-body"><i class="bi bi-link-45deg me-1"></i>รายการลิงก์ (<code>navLink()</code>)</h6>
                            <nav class="nav nav-pills flex-column gap-1">
                                <?= navLink('sales', 'admin/sales') ?>
                                <?= navLink('inventory', 'admin/inventory') ?>
                                <?= navLink('customers', 'admin/customers') ?>
                                <?= navLink('reports', 'admin/reports') ?>
                            </nav>
                        </div>
                    </div>

                    <!-- การแสดงผลแบบ Dropdown Items (dropdownItem) -->
                    <div class="col-md-12 col-lg-4">
                        <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                            <h6 class="fw-bold mb-2 text-body"><i class="bi bi-menu-button-wide me-1"></i>เมนูย่อย (<code>dropdownItem()</code>)</h6>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-grid me-1"></i>โมดูลที่สามารถเข้าถึงได้
                                </button>
                                <ul class="dropdown-menu shadow w-100">
                                    <li><?= dropdownItem('pos', 'admin/pos') ?></li>
                                    <li><?= dropdownItem('sales', 'admin/sales') ?></li>
                                    <li><?= dropdownItem('items', 'admin/products') ?></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><?= dropdownItem('branches', 'admin/branches') ?></li>
                                    <li><?= dropdownItem('users', 'admin/users') ?></li>
                                    <li><?= dropdownItem('settings', 'admin/settings') ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page-Specific JavaScript (สามารถใส่ Custom JS เฉพาะหน้านี้ได้) -->
<script>
    console.log('Homunculus Dashboard Initialized.');
</script>
<?= $this->endSection() ?>
