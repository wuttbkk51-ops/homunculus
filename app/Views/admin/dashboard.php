<?= $this->extend('layouts/admin') ?>

<?= $this->section('title') ?>แดชบอร์ด - Homunculus Admin<?= $this->endSection() ?>

<?= $this->section('pageHeader') ?>
<div class="d-flex align-items-center gap-2">
    <i class="bi bi-speedometer2 text-primary fs-3"></i>
    <div>
        <h3 class="mb-0 fw-bold fs-4">แดชบอร์ดผู้ดูแลระบบ</h3>
        <p class="text-secondary small mb-0">ระบบบริหารจัดการและภาพรวมสิทธิ์การเข้าถึงข้อมูล</p>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>" class="text-decoration-none">หน้าหลัก</a></li>
        <li class="breadcrumb-item active" aria-current="page">แดชบอร์ด</li>
    </ol>
</nav>
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
                            <?= esc(current_tenant_id() ?? 'NULL (SuperAdmin)') ?>
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
                                        <?= esc(current_tenant_id() ?? 'SuperAdmin (ไม่มี Tenant)') ?>
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
<?= $this->endSection() ?>
