<?php
/**
 * @var string|null $title
 * @var array<int, array{title: string, url?: string}>|null $breadcrumbs
 */
$title ??= lang('Dashboard.title');
?>
<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<!-- Small Box Widgets Row -->
<div class="row g-3 mb-4">
    <!-- Orders Box -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary shadow-sm rounded-3">
            <div class="inner p-3">
                <h3 class="fw-bold mb-1">150</h3>
                <p class="mb-0 text-white-50"><?= esc(lang('Dashboard.newOrders')) ?></p>
            </div>
            <i class="small-box-icon bi bi-bag-check position-absolute top-0 end-0 p-3 opacity-25 fs-1"></i>
            <a href="#" class="small-box-footer link-light d-block text-center py-2 text-decoration-none small bg-dark bg-opacity-10">
                <?= esc(lang('Dashboard.moreInfo')) ?> <i class="bi bi-arrow-right-circle ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Bounce Rate Box -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success shadow-sm rounded-3">
            <div class="inner p-3">
                <h3 class="fw-bold mb-1">53<sup class="fs-5">%</sup></h3>
                <p class="mb-0 text-white-50"><?= esc(lang('Dashboard.bounceRate')) ?></p>
            </div>
            <i class="small-box-icon bi bi-graph-up-arrow position-absolute top-0 end-0 p-3 opacity-25 fs-1"></i>
            <a href="#" class="small-box-footer link-light d-block text-center py-2 text-decoration-none small bg-dark bg-opacity-10">
                <?= esc(lang('Dashboard.moreInfo')) ?> <i class="bi bi-arrow-right-circle ms-1"></i>
            </a>
        </div>
    </div>

    <!-- User Registrations Box -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning shadow-sm rounded-3">
            <div class="inner p-3">
                <h3 class="fw-bold mb-1">44</h3>
                <p class="mb-0 text-dark-50"><?= esc(lang('Dashboard.userRegistrations')) ?></p>
            </div>
            <i class="small-box-icon bi bi-person-plus position-absolute top-0 end-0 p-3 opacity-25 fs-1"></i>
            <a href="#" class="small-box-footer link-dark d-block text-center py-2 text-decoration-none small bg-dark bg-opacity-10">
                <?= esc(lang('Dashboard.moreInfo')) ?> <i class="bi bi-arrow-right-circle ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Unique Visitors Box -->
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-danger shadow-sm rounded-3">
            <div class="inner p-3">
                <h3 class="fw-bold mb-1">65</h3>
                <p class="mb-0 text-white-50"><?= esc(lang('Dashboard.uniqueVisitors')) ?></p>
            </div>
            <i class="small-box-icon bi bi-pie-chart position-absolute top-0 end-0 p-3 opacity-25 fs-1"></i>
            <a href="#" class="small-box-footer link-light d-block text-center py-2 text-decoration-none small bg-dark bg-opacity-10">
                <?= esc(lang('Dashboard.moreInfo')) ?> <i class="bi bi-arrow-right-circle ms-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main Dashboard Grid -->
<div class="row g-3">
    <!-- Overview Card -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-semibold mb-0"><?= esc(lang('Dashboard.overview')) ?></h3>
                <div class="card-tools">
                    <span class="badge bg-primary-subtle text-primary"><?= esc(lang('Dashboard.salesSummary')) ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th><?= esc(lang('Dashboard.item')) ?></th>
                                <th><?= esc(lang('Dashboard.status')) ?></th>
                                <th><?= esc(lang('Dashboard.popularity')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="fw-semibold">Enterprise License</td>
                                <td><span class="badge bg-success"><?= esc(lang('Dashboard.active')) ?></span></td>
                                <td>
                                    <div class="progress" role="progressbar" style="height: 6px;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-success" style="width: 85%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="fw-semibold">Custom Add-on Module</td>
                                <td><span class="badge bg-warning"><?= esc(lang('Dashboard.pending')) ?></span></td>
                                <td>
                                    <div class="progress" role="progressbar" style="height: 6px;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning" style="width: 55%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="fw-semibold">API Gateway Pro</td>
                                <td><span class="badge bg-primary"><?= esc(lang('Dashboard.running')) ?></span></td>
                                <td>
                                    <div class="progress" role="progressbar" style="height: 6px;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-primary" style="width: 70%"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Card -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header border-0">
                <h3 class="card-title fw-semibold mb-0"><?= esc(lang('Dashboard.recentActivity')) ?></h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="rounded-circle bg-primary-subtle text-primary p-2 me-3">
                            <i class="bi bi-person-check fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-semibold">New user registration</p>
                            <small class="text-body-secondary">2 minutes ago</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="rounded-circle bg-success-subtle text-success p-2 me-3">
                            <i class="bi bi-cart-check fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-semibold">New order completed</p>
                            <small class="text-body-secondary">15 minutes ago</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="rounded-circle bg-warning-subtle text-warning p-2 me-3">
                            <i class="bi bi-shield-lock fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-semibold">Security policy updated</p>
                            <small class="text-body-secondary">1 hour ago</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
