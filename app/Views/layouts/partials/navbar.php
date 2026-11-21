<?php
$user = function_exists('auth') && auth()->loggedIn() ? auth()->user() : null;
$username = $user ? ($user->username ?? $user->email ?? lang('App.user')) : lang('App.guest');
?>
<nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
        <!-- Start Navbar Links -->
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="<?= esc(lang('App.toggleSidebar')) ?>">
                    <i class="bi bi-list fs-4"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="<?= site_url('/') ?>" class="nav-link">
                    <i class="bi bi-house me-1"></i><?= esc(lang('App.home')) ?>
                </a>
            </li>
        </ul>

        <!-- End Navbar Links -->
        <ul class="navbar-nav ms-auto align-items-center gap-2">
            <!-- Theme Mode Toggle Dropdown -->
            <li class="nav-item dropdown">
                <button
                    class="btn btn-link nav-link py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center"
                    id="bd-theme"
                    type="button"
                    aria-expanded="false"
                    data-bs-toggle="dropdown"
                    aria-label="<?= esc(lang('App.toggleTheme')) ?>"
                >
                    <i class="bi bi-sun-fill my-1 theme-icon-active" data-lte-theme-icon="light"></i>
                    <i class="bi bi-moon-fill my-1 theme-icon-active d-none" data-lte-theme-icon="dark"></i>
                    <i class="bi bi-circle-half my-1 theme-icon-active d-none" data-lte-theme-icon="auto"></i>
                    <span class="d-lg-none ms-2"><?= esc(lang('App.toggleTheme')) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="bd-theme" style="--bs-dropdown-min-width: 8rem;">
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                            <i class="bi bi-sun-fill me-2 opacity-50"></i>
                            <?= esc(lang('App.themeLight')) ?>
                            <i class="bi bi-check-lg ms-auto d-none"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
                            <i class="bi bi-moon-fill me-2 opacity-50"></i>
                            <?= esc(lang('App.themeDark')) ?>
                            <i class="bi bi-check-lg ms-auto d-none"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
                            <i class="bi bi-circle-half me-2 opacity-50"></i>
                            <?= esc(lang('App.themeAuto')) ?>
                            <i class="bi bi-check-lg ms-auto d-none"></i>
                        </button>
                    </li>
                </ul>
            </li>

            <!-- User Menu Dropdown -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <span class="d-none d-md-inline fw-semibold"><?= esc($username) ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0">
                    <li class="dropdown-header">
                        <div class="fw-bold text-body"><?= esc($username) ?></div>
                        <?php if ($user && !empty($user->email)): ?>
                            <small class="text-secondary"><?= esc($user->email) ?></small>
                        <?php endif; ?>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="p-2 d-flex justify-content-between align-items-center gap-1">
                        <a href="<?= site_url('profile') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-person me-1"></i><?= esc(lang('App.profile')) ?>
                        </a>
                        <a href="<?= site_url('settings') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-gear me-1"></i><?= esc(lang('App.settings')) ?>
                        </a>
                        <?php if ($user): ?>
                            <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-box-arrow-right me-1"></i><?= esc(lang('App.signOut')) ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= site_url('login') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i><?= esc(lang('Auth.login')) ?>
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
