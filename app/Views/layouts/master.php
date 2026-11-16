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
            <!-- App Content Header -->
            <div class="app-content-header py-3">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <?= $this->renderSection('pageHeader') ?: '<h3 class="mb-0 fw-bold fs-4">ระบบจัดการ</h3>' ?>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <?= $this->renderSection('breadcrumb') ?>
                        </div>
                    </div>
                </div>
            </div>

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
