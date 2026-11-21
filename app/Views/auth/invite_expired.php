<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc(lang('Invite.expiredTitle') . ' - ' . lang('App.appName')) ?></title>

    <script>
    (() => {
        'use strict';
        const root = document.documentElement;
        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try { stored = localStorage.getItem(STORAGE_KEY); } catch {}
        const resolved = (stored === 'dark' || stored === 'light') ? stored : 'light';
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
    })();
    </script>

    <meta name="color-scheme" content="light dark">
    <link rel="stylesheet" href="<?= base_url('assets/css/noto-sans-thai.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte-colors.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/theme-luxury.css') ?>">
    <style>
        body { font-family: 'Noto Sans Thai', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
    </style>
</head>
<body class="login-page bg-body-secondary">
    <main class="login-box">
        <div class="card card-outline card-danger shadow-sm text-center">
            <div class="card-header py-3">
                <h1 class="mb-0 fs-2 fw-bold text-primary"><?= esc(lang('App.appName')) ?></h1>
            </div>
            <div class="card-body p-4">
                <i class="bi bi-envelope-x display-4 text-danger mb-3 d-block"></i>
                <h4 class="fw-bold text-danger mb-2"><?= esc(lang('Invite.expiredTitle')) ?></h4>
                <p class="text-body-secondary small mb-4"><?= esc(lang('Invite.expiredMessage')) ?></p>
                <a href="<?= site_url('login') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i><?= esc(lang('Invite.backToLogin')) ?>
                </a>
            </div>
        </div>
    </main>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>
</body>
</html>
