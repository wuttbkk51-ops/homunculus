<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">
<head>
    <?= $this->include('layouts/partials/header') ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- App Header (Navbar) -->
        <?= $this->include('layouts/partials/navbar') ?>

        <!-- App Sidebar (Semi-dark Navy) -->
        <?= $this->include('layouts/partials/sidebar') ?>

        <!-- Main Content Area -->
        <main class="app-main">
            <!-- App Content Header (Page Header & Breadcrumbs) -->
            <?= $this->include('layouts/partials/page_header') ?>

            <!-- App Content Body -->
            <div class="app-content pb-4">
                <div class="container-fluid">
                    <!-- Session Flash Messages / Alerts -->
                    <?= $this->include('layouts/partials/alerts') ?>

                    <!-- Dynamic Page Content Slot -->
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </main>

        <!-- App Footer -->
        <?= $this->include('layouts/partials/footer') ?>
    </div>

    <!-- Core & Page Scripts -->
    <?= $this->include('layouts/partials/scripts') ?>
</body>
</html>
