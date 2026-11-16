<?php
$user = auth()->user();
$myBranches = user_branches();
$activeBranchId = active_branch_id();
?>
<nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
        <!-- ฝั่งซ้าย: Toggle Sidebar & ลิงก์ย่อ -->
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
                    <i class="bi bi-list fs-4"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="<?= site_url('/') ?>" class="nav-link text-body-secondary" target="_blank" title="เปิดหน้าเว็บไซต์สาธารณะ">
                    <i class="bi bi-box-arrow-up-right me-1"></i>หน้าเว็บไซต์
                </a>
            </li>
        </ul>

        <!-- ฝั่งขวา: Theme Switcher, Branch Switcher & User Dropdown -->
        <ul class="navbar-nav ms-auto align-items-center gap-2">
            <!-- Theme Mode Switcher (AdminLTE 4 Native) -->
            <li class="nav-item dropdown">
                <button class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars-fill my-1 theme-icon-active"></i>
                    <span class="d-lg-none ms-2" id="bd-theme-text">สลับธีม</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="bd-theme-text">
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                            <i class="bi bi-sun-fill me-2 opacity-50"></i>
                            โหมดสว่าง (Light)
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="dark" aria-pressed="true">
                            <i class="bi bi-moon-stars-fill me-2 opacity-50"></i>
                            โหมดมืด (Dark Navy)
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                            <i class="bi bi-circle-half me-2 opacity-50"></i>
                            ตามระบบ (Auto)
                        </button>
                    </li>
                </ul>
            </li>

            <!-- Branch Switcher -->
            <?php if (! empty($myBranches)): ?>
                <li class="nav-item d-flex align-items-center">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary border-end-0">
                            <i class="bi bi-building text-primary"></i>
                        </span>
                        <select id="branchSwitcher" class="form-select form-select-sm border-start-0" onchange="if(this.value) location.href='<?= site_url('switch-branch') ?>/' + this.value" aria-label="เลือกสาขา">
                            <?php foreach ($myBranches as $b): ?>
                                <option value="<?= esc($b['branch_id']) ?>" <?= (int) $b['branch_id'] === (int) $activeBranchId ? 'selected' : '' ?>>
                                    <?= esc($b['branch_name']) ?> (<?= esc($b['branch_role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </li>
            <?php elseif (is_superadmin()): ?>
                <li class="nav-item">
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                        <i class="bi bi-shield-check me-1"></i>SuperAdmin Mode
                    </span>
                </li>
            <?php endif; ?>

            <!-- User Menu Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <span class="d-none d-md-inline fw-semibold"><?= esc($user->username ?? 'User') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li class="dropdown-header">
                        <div class="fw-bold text-body"><?= esc($user->username ?? '') ?></div>
                        <small class="text-secondary"><?= esc($user->email ?? '') ?></small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <span class="dropdown-item-text small text-secondary">
                            <i class="bi bi-hash me-1"></i>Tenant ID: <?= esc(current_tenant_id() ?? 'SuperAdmin') ?>
                        </span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                            <i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
