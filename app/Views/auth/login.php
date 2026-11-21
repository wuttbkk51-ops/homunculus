<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= esc(lang('Auth.login') . ' - ' . lang('App.appName')) ?></title>

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
<body class="login-page bg-body-secondary">
    <main class="login-box">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header text-center py-3">
                <a href="<?= site_url('/') ?>" class="link-dark text-decoration-none">
                    <h1 class="mb-0 fs-2 fw-bold text-primary"><?= esc(lang('App.appName')) ?></h1>
                </a>
            </div>
            <div class="card-body login-card-body p-4">
                <p class="login-box-msg text-center text-body-secondary mb-4"><?= esc(lang('Auth.signInToSession')) ?></p>

                <?php if (session('error') !== null): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?= esc(session('error')) ?>
                    </div>
                <?php elseif (session('errors') !== null): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?php if (is_array(session('errors'))): ?>
                            <ul class="mb-0 ps-3">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= esc(session('errors')) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (session('message') !== null): ?>
                    <div class="alert alert-success py-2 small" role="alert">
                        <?= esc(session('message')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="loginEmail"
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="<?= esc(lang('Auth.email')) ?>"
                                value="<?= old('email') ?>"
                                required
                                autocomplete="email"
                            >
                            <label for="loginEmail"><?= esc(lang('Auth.email')) ?></label>
                        </div>
                        <div class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="loginPassword"
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="<?= esc(lang('Auth.password')) ?>"
                                required
                                autocomplete="current-password"
                            >
                            <label for="loginPassword"><?= esc(lang('Auth.password')) ?></label>
                        </div>
                        <div class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-7">
                            <?php if (setting('Auth.sessionConfig')['allowRemembering'] ?? true): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" <?php if (old('remember')): ?> checked<?php endif ?>>
                                    <label class="form-check-label small text-body-secondary" for="rememberMe">
                                        <?= esc(lang('Auth.rememberMe')) ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i><?= esc(lang('Auth.login')) ?>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex justify-content-between small mt-4 pt-3 border-top">
                    <?php if (setting('Auth.allowMagicLinkLogins') ?? false): ?>
                        <a href="<?= url_to('magic-link') ?>" class="text-decoration-none">
                            <?= esc(lang('Auth.forgotPassword')) ?>
                        </a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>

                    <?php if (setting('Auth.allowRegistration') ?? true): ?>
                        <a href="<?= url_to('register') ?>" class="text-decoration-none">
                            <?= esc(lang('Auth.register')) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Core Scripts -->
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>
</body>
</html>
