<aside class="app-sidebar bg-navy shadow" data-bs-theme="dark">
    <!-- Brand Logo -->
    <div class="sidebar-brand border-bottom border-secondary border-opacity-25">
        <a href="<?= site_url('dashboard') ?>" class="brand-link text-decoration-none">
            <i class="bi bi-cpu-fill brand-image text-primary fs-4 ms-2 me-2"></i>
            <span class="brand-text fw-bold text-white">Homunculus</span>
        </a>
    </div>

    <!-- Sidebar Content & Navigation -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <?= sidebarMenu() ?>
        </nav>
    </div>
</aside>
