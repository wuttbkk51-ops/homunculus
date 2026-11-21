<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= esc(($title ?? lang('Home.title')) . ' - ' . lang('App.appName')) ?></title>

    <!-- Theme Init -->
    <script>
    (() => {
        'use strict';
        const root = document.documentElement;
        if (root.getAttribute('data-lte-color-mode') === 'off') {
            return;
        }

        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try {
            stored = localStorage.getItem(STORAGE_KEY);
        } catch {}

        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
            resolved = stored;
        } else if (authored === 'dark' || authored === 'light') {
            resolved = authored;
        } else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
            resolved = 'dark';
        }
        root.setAttribute('data-bs-theme', resolved);
        root.style.colorScheme = resolved;
        if (resolved !== authored) {
            root.setAttribute('data-lte-theme-resolved', '');
        }
    })();
    </script>

    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)">

    <!-- Google Fonts: Noto Sans Thai (Local Offline) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/noto-sans-thai.css') ?>">

    <!-- Local Plugins & Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">

    <!-- AdminLTE 4 Local CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte-colors.min.css') ?>">

    <!-- Custom Sharp & Luxury Theme -->
    <link rel="stylesheet" href="<?= base_url('assets/css/theme-luxury.css') ?>">

    <style>
        body {
            font-family: 'Noto Sans Thai', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-body-tertiary">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg bg-body border-bottom sticky-top">
        <div class="container py-1">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="<?= site_url('/') ?>">
                <i class="bi bi-box-fill fs-4"></i>
                <span><?= esc(lang('App.appName')) ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#homeNavbar" aria-controls="homeNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="homeNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link active fw-medium" href="<?= site_url('/') ?>"><?= esc(lang('Home.title')) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-body-secondary" href="<?= site_url('starter') ?>"><?= esc(lang('App.starterPage')) ?></a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if (auth()->loggedIn()): ?>
                        <a href="<?= site_url('admin') ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
                            <i class="bi bi-speedometer2 me-1"></i><?= esc(lang('Home.adminPortal')) ?>
                        </a>
                        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i><?= esc(lang('App.signOut')) ?>
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('login') ?>" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i><?= esc(lang('App.signIn')) ?>
                        </a>
                        <a href="<?= site_url('admin') ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
                            <i class="bi bi-shield-lock me-1"></i><?= esc(lang('Home.adminPortal')) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-5 bg-body text-center border-bottom">
        <div class="container py-lg-4">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3 fw-semibold">
                        <i class="bi bi-stars me-1"></i><?= esc(lang('Home.heroBadge')) ?>
                    </span>
                    <h1 class="display-5 fw-bold text-body-emphasis mb-3"><?= esc(lang('Home.heroTitle')) ?></h1>
                    <p class="lead text-body-secondary mb-4 mx-auto" style="max-width: 680px;">
                        <?= esc(lang('Home.heroSubtitle')) ?>
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="<?= site_url('admin') ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-speedometer2 me-2"></i><?= esc(lang('Home.enterAdmin')) ?>
                        </a>
                        <a href="<?= site_url('starter') ?>" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-file-earmark-code me-2"></i><?= esc(lang('App.starterPage')) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Features Section -->
    <section class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-body-emphasis"><?= esc(lang('Home.featuresTitle')) ?></h2>
                <p class="text-body-secondary"><?= esc(lang('Home.featuresSubtitle')) ?></p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center text-bg-primary rounded-3 p-3 mb-3">
                                <i class="bi bi-buildings fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2"><?= esc(lang('Home.featureMultiTenant')) ?></h5>
                            <p class="card-text text-body-secondary small"><?= esc(lang('Home.featureMultiTenantDesc')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center text-bg-success rounded-3 p-3 mb-3">
                                <i class="bi bi-shield-check fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2"><?= esc(lang('Home.featureSecurity')) ?></h5>
                            <p class="card-text text-body-secondary small"><?= esc(lang('Home.featureSecurityDesc')) ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center text-bg-info text-white rounded-3 p-3 mb-3">
                                <i class="bi bi-window-desktop fs-3"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-2"><?= esc(lang('Home.featureModernUI')) ?></h5>
                            <p class="card-text text-body-secondary small"><?= esc(lang('Home.featureModernUIDesc')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Backend Portal Quick Access Card -->
    <section class="pb-5">
        <div class="container">
            <div class="card border-0 shadow-sm bg-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="fw-bold text-body-emphasis mb-2"><?= esc(lang('Home.backendEntryTitle')) ?></h3>
                        <p class="text-body-secondary mb-3 mb-lg-0"><?= esc(lang('Home.backendEntrySubtitle')) ?></p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="<?= site_url('admin') ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i><?= esc(lang('Home.enterAdmin')) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-body border-top text-center text-body-secondary small">
        <div class="container">
            <p class="mb-0">
                <strong>&copy; <?= date('Y') ?> <a href="<?= site_url('/') ?>" class="text-decoration-none"><?= esc(lang('App.appName')) ?></a>.</strong> <?= esc(lang('App.copyright')) ?>
            </p>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>
</body>
</html>
