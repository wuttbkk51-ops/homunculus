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

<!-- Block สำหรับให้แต่ละ View แทรก Custom CSS เฉพาะหน้า -->
<?= $this->renderSection('styles') ?>
