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

    <!-- CDN Preconnect & DNS-Prefetch -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- AdminLTE 4 CSS & Colors (Built on Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte-colors.min.css">

    <!-- Google Font (Non-blocking) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"></noscript>

    <style>
        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Sarabun', 'Noto Sans Thai', sans-serif;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="login-page bg-body-secondary d-flex flex-column justify-content-center align-items-center min-vh-100 p-3">

    <?= $this->renderSection('main') ?>

    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
