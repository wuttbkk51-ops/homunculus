<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= esc(lang('Invite.btnRegister') . ' - ' . lang('App.appName')) ?></title>

    <!-- Theme Init -->
    <script>
    (() => {
        'use strict';
        const root = document.documentElement;
        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try { stored = localStorage.getItem(STORAGE_KEY); } catch {}
        const authored = root.getAttribute('data-bs-theme');
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') { resolved = stored; }
        else if (authored === 'dark' || authored === 'light') { resolved = authored; }
        else if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) { resolved = 'dark'; }
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
    <main class="login-box" style="width: 420px;">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header text-center py-3">
                <a href="<?= site_url('/') ?>" class="link-dark text-decoration-none">
                    <h1 class="mb-0 fs-2 fw-bold text-primary"><?= esc(lang('App.appName')) ?></h1>
                </a>
            </div>
            <div class="card-body p-4">
                <p class="text-center text-body-secondary mb-1">
                    <i class="bi bi-envelope-check me-1 text-primary"></i>
                    <?= esc(lang('Invite.btnRegister')) ?>
                </p>

                <?php if ($invitation['tenant_id'] !== null): ?>
                    <p class="text-center small text-body-secondary mb-3">
                        <?= esc(lang('Invite.labelRole')) ?>:
                        <span class="badge bg-primary"><?= esc($invitation['role_group']) ?></span>
                    </p>
                <?php else: ?>
                    <p class="text-center small text-body-secondary mb-3">
                        <?= esc(lang('Invite.optionNewTenant')) ?> —
                        <?= esc(lang('Invite.labelRole')) ?>:
                        <span class="badge bg-success"><?= esc($invitation['role_group']) ?></span>
                    </p>
                <?php endif; ?>

                <?php if (session('error') !== null): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?= esc(session('error')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session('errors') !== null): ?>
                    <div class="alert alert-danger py-2 small" role="alert">
                        <?php if (is_array(session('errors'))): ?>
                            <ul class="mb-0 ps-3">
                                <?php foreach (session('errors') as $err): ?>
                                    <li><?= esc($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= esc(session('errors')) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('register/invite') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>">

                    <?php if ($needTenantInfo): ?>
                        <input type="hidden" name="need_tenant_info" value="1">
                    <?php endif; ?>

                    <!-- Email (readonly ตามที่ถูกเชิญ) -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="regEmail"
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="<?= esc(lang('Invite.labelEmail')) ?>"
                                value="<?= esc($invitation['email']) ?>"
                                readonly
                                autocomplete="email"
                            >
                            <label for="regEmail"><?= esc(lang('Invite.labelEmail')) ?></label>
                        </div>
                        <div class="input-group-text"><i class="bi bi-envelope-fill"></i></div>
                    </div>

                    <!-- Password -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="regPassword"
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="<?= esc(lang('Invite.labelPassword')) ?>"
                                required
                                autocomplete="new-password"
                            >
                            <label for="regPassword"><?= esc(lang('Invite.labelPassword')) ?></label>
                        </div>
                        <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <input
                                id="regPasswordConfirm"
                                type="password"
                                name="password_confirm"
                                class="form-control"
                                placeholder="<?= esc(lang('Invite.labelPasswordConfirm')) ?>"
                                required
                                autocomplete="new-password"
                            >
                            <label for="regPasswordConfirm"><?= esc(lang('Invite.labelPasswordConfirm')) ?></label>
                        </div>
                        <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                    </div>

                    <?php if ($needTenantInfo): ?>
                        <!-- ส่วนข้อมูลร้าน (แสดงเฉพาะเมื่อ Admin ไม่ได้กำหนดไว้) -->
                        <hr class="my-3">
                        <p class="small text-body-secondary mb-2">
                            <i class="bi bi-shop me-1"></i><?= esc(lang('Invite.optionNewTenant')) ?>
                        </p>

                        <div class="input-group mb-3">
                            <div class="form-floating">
                                <input
                                    id="tenantName"
                                    type="text"
                                    name="tenant_name"
                                    class="form-control"
                                    placeholder="<?= esc(lang('Invite.labelTenantName')) ?>"
                                    value="<?= esc(old('tenant_name')) ?>"
                                    required
                                >
                                <label for="tenantName"><?= esc(lang('Invite.labelTenantName')) ?></label>
                            </div>
                            <div class="input-group-text"><i class="bi bi-building"></i></div>
                        </div>

                        <div class="input-group mb-3">
                            <div class="form-floating">
                                <input
                                    id="tenantSlug"
                                    type="text"
                                    name="tenant_slug"
                                    class="form-control"
                                    placeholder="<?= esc(lang('Invite.labelTenantSlug')) ?>"
                                    value="<?= esc(old('tenant_slug')) ?>"
                                    required
                                    pattern="[a-z0-9\-_]+"
                                    title="a-z, 0-9, -, _ เท่านั้น"
                                >
                                <label for="tenantSlug"><?= esc(lang('Invite.labelTenantSlug')) ?></label>
                            </div>
                            <div class="input-group-text"><i class="bi bi-link-45deg"></i></div>
                        </div>
                    <?php elseif ($invitation['tenant_id'] === null && ! empty($invitation['tenant_name'])): ?>
                        <!-- Hybrid: Admin กำหนดชื่อร้านไว้แล้ว แสดงเป็น read-only -->
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-shop me-1"></i>
                            <?= esc(lang('Invite.labelTenantName')) ?>:
                            <strong><?= esc($invitation['tenant_name']) ?></strong>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 mt-1">
                        <i class="bi bi-person-check me-1"></i><?= esc(lang('Invite.btnRegister')) ?>
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="<?= site_url('login') ?>" class="small text-decoration-none text-body-secondary">
                        <i class="bi bi-box-arrow-in-right me-1"></i><?= esc(lang('Invite.backToLogin')) ?>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>
</body>
</html>
