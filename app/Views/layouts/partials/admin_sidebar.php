<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!-- Brand Logo -->
    <div class="sidebar-brand">
        <a href="<?= site_url('dashboard') ?>" class="brand-link text-decoration-none">
            <i class="bi bi-cpu-fill brand-image text-primary fs-4 ms-2 me-2"></i>
            <span class="brand-text fw-bold">Homunculus</span>
        </a>
    </div>

    <!-- Sidebar Content & Navigation -->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <?= render_sidebar_menu() ?>
        </nav>
    </div>
</aside>
