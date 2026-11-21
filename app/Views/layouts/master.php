<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <?= $this->include('layouts/partials/header') ?>

    <!-- Page Specific Styles -->
    <?= $this->renderSection('contentStyles') ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <!-- App Header (Navbar) -->
        <?= $this->include('layouts/partials/navbar') ?>

        <!-- App Sidebar -->
        <?= $this->include('layouts/partials/sidebar') ?>

        <!-- App Main Content -->
        <main class="app-main">
            <!-- App Content Header -->
            <?= $this->include('layouts/partials/page_header') ?>

            <!-- App Content Body -->
            <div class="app-content pb-4">
                <div class="container-fluid">
                    <!-- Flash Alerts -->
                    <?= $this->include('layouts/partials/alerts') ?>

                    <!-- Dynamic Page Content Slot -->
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </main>

        <!-- App Footer -->
        <?= $this->include('layouts/partials/footer') ?>
    </div>

    <!-- Core Scripts -->
    <?= $this->include('layouts/partials/scripts') ?>

    <!-- Page Specific Scripts -->
    <?= $this->renderSection('contentScript') ?>
</body>
</html>
