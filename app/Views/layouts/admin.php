<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?: 'ระบบจัดการ - Homunculus' ?></title>

    <!-- กำหนด Default Theme = Dark (Semi-Dark Navy) ป้องกันการกะพริบของหน้าจอ -->
    <script>
        (function() {
            const currentTheme = localStorage.getItem('lte-theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', currentTheme);
        })();
    </script>

    <!-- Local Assets (Bootstrap Icons & OverlayScrollbars) -->
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/overlayscrollbars/overlayscrollbars.min.css') ?>">

    <!-- AdminLTE 4 Local CSS (Copied via composer-copy-file) -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte-colors.min.css') ?>">

    <!-- Google Fonts: Noto Sans Thai (Local Offline Assets) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/noto-sans-thai.css') ?>">

    <style>
        /* ใช้ฟอนต์ Noto Sans Thai แบบ Local ทันที */
        body {
            font-family: 'Noto Sans Thai', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        /* โทน Semi-Dark Navy เสริมสำหรับ AdminLTE */
        .bg-navy {
            background-color: #001f3f !important;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- App Header (Navbar) -->
        <?= $this->include('layouts/partials/admin_navbar') ?>

        <!-- App Sidebar (Semi-dark Navy) -->
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

    <!-- Core Scripts (Local Assets via composer-copy-file) -->
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/overlayscrollbars/overlayscrollbars.browser.es6.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
