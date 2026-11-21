<?php
$uri = service('uri')->getPath();
?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <a href="<?= site_url('/') ?>" class="brand-link text-decoration-none">
            <span class="brand-text fw-bold text-primary fs-4 ms-2"><?= esc(lang('App.appName')) ?></span>
        </a>
    </div>

    <!-- Sidebar Wrapper -->
    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Main navigation">
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                data-accordion="false"
                id="navigation"
            >
                <li class="nav-item">
                    <a href="<?= site_url('/') ?>" class="nav-link <?= ($uri === '' || $uri === 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p><?= esc(lang('App.dashboard')) ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('profile') ?>" class="nav-link <?= str_starts_with($uri, 'profile') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-person"></i>
                        <p><?= esc(lang('App.profile')) ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('settings') ?>" class="nav-link <?= str_starts_with($uri, 'settings') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-gear"></i>
                        <p><?= esc(lang('App.settings')) ?></p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= site_url('starter') ?>" class="nav-link <?= str_starts_with($uri, 'starter') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-file-earmark"></i>
                        <p><?= esc(lang('App.starterPage')) ?></p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
