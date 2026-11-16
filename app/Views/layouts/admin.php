<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?: 'ระบบจัดการ - Homunculus' ?></title>

    <!-- Google Fonts & System Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons (AdminLTE 4 Default) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">

    <!-- AdminLTE 4 CSS (Built on Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">

    <style>
        /* กำหนด Font สากลสำหรับภาษาไทยให้ดูสวยงาม เป็นทางการ ไม่กระทบ layout AdminLTE 4 */
        body {
            font-family: 'Sarabun', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- App Header (Navbar) -->
        <?= $this->include('layouts/partials/admin_navbar') ?>

        <!-- App Sidebar -->
        <?= $this->include('layouts/partials/admin_sidebar') ?>

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

                    <!-- Dynamic Page Content -->
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </main>

        <!-- App Footer -->
        <?= $this->include('layouts/partials/admin_footer') ?>
    </div>

    <!-- Core Scripts (AdminLTE 4 & Bootstrap 5 Native) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
