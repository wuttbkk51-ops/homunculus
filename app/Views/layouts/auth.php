<!DOCTYPE html>
<html lang="th" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?: 'เข้าสู่ระบบ - Homunculus' ?></title>

    <!-- กำหนด Default Theme = Dark (Semi-Dark Navy) -->
    <script>
        (function() {
            const currentTheme = localStorage.getItem('lte-theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', currentTheme);
        })();
    </script>

    <!-- Local Assets (Bootstrap Icons, AdminLTE 4 CSS & Colors) -->
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte-colors.min.css') ?>">

    <!-- Google Fonts: Noto Sans Thai (Local Offline Assets) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/noto-sans-thai.css') ?>">

    <style>
        body {
            font-family: 'Noto Sans Thai', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="login-page bg-body-secondary d-flex flex-column justify-content-center align-items-center min-vh-100 p-3">

    <?= $this->renderSection('main') ?>

    <!-- Local Core Scripts -->
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
