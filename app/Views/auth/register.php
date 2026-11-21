<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= esc(lang('Auth.register') . ' - ' . lang('App.appName')) ?></title>

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
<body class="register-page bg-body-secondary">
    <main class="register-box">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header text-center py-3">
                <a href="<?= site_url('/') ?>" class="link-dark text-decoration-none">
                    <h1 class="mb-0 fs-2 fw-bold text-primary"><?= esc(lang('App.appName')) ?></h1>
                </a>
            </div>
            <div class="card-body register-card-body p-4">
                <p class="register-box-msg text-center text-body-secondary mb-4"><?= esc(lang('Auth.registerMembership')) ?></p>

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

                <form action="<?= url_to('register') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="registerEmail"
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="<?= esc(lang('Auth.email')) ?>"
                                value="<?= old('email') ?>"
                                required
                                autocomplete="email"
                            >
                            <label for="registerEmail"><?= esc(lang('Auth.email')) ?></label>
                        </div>
                        <div class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="registerUsername"
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="<?= esc(lang('Auth.username')) ?>"
                                value="<?= old('username') ?>"
                                required
                                autocomplete="username"
                            >
                            <label for="registerUsername"><?= esc(lang('Auth.username')) ?></label>
                        </div>
                        <div class="input-group-text">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="registerPassword"
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="<?= esc(lang('Auth.password')) ?>"
                                required
                                autocomplete="new-password"
                            >
                            <label for="registerPassword"><?= esc(lang('Auth.password')) ?></label>
                        </div>
                        <div class="input-group-text">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="registerPasswordConfirm"
                                type="password"
                                name="password_confirm"
                                class="form-control"
                                placeholder="<?= esc(lang('Auth.passwordConfirm')) ?>"
                                required
                                autocomplete="new-password"
                            >
                            <label for="registerPasswordConfirm"><?= esc(lang('Auth.passwordConfirm')) ?></label>
                        </div>
                        <div class="input-group-text">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>

                    <!-- Terms and Submit -->
                    <div class="row align-items-center mb-3">
                        <div class="col-7">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                <label class="form-check-label small text-body-secondary" for="agreeTerms">
                                    <?= esc(lang('Auth.agreeTerms')) ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-person-plus me-1"></i><?= esc(lang('Auth.register')) ?>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="text-center small mt-4 pt-3 border-top">
                    <span class="text-body-secondary"><?= esc(lang('Auth.haveAccount')) ?></span>
                    <a href="<?= url_to('login') ?>" class="text-decoration-none fw-semibold ms-1">
                        <?= esc(lang('Auth.login')) ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Core Scripts -->
    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>
</body>
</html>
