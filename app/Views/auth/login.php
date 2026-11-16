<?= $this->extend('auth/layout') ?>

<?= $this->section('title') ?>เข้าสู่ระบบ - Homunculus<?= $this->endSection() ?>

<?= $this->section('main') ?>
<?php
$isDev           = ENVIRONMENT === 'development';
$defaultEmail    = $isDev ? 'superadmin@homunculus.test' : '';
$defaultPassword = $isDev ? 'Password123!' : '';
?>
<div class="login-box">
    <!-- Login Logo -->
    <div class="login-logo mb-4 text-center">
        <a href="<?= site_url('/') ?>" class="link-body-emphasis text-decoration-none fs-2 fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-cpu-fill text-primary fs-1"></i>
            <span><b>Homunculus</b></span>
        </a>
    </div>

    <!-- Login Card -->
    <div class="card card-outline card-primary shadow-sm border-0">
        <div class="card-body login-card-body p-4">
            <p class="login-box-msg text-center text-secondary mb-3">เข้าสู่ระบบเพื่อเริ่มใช้งาน</p>

            <!-- Dev Mode Helper Alert -->
            <?php if ($isDev): ?>
                <div class="alert alert-info py-2 px-3 small mb-3 border-info-subtle bg-info-subtle text-info-emphasis">
                    <div class="d-flex align-items-center mb-1 fw-bold">
                        <i class="bi bi-code-slash me-1"></i> Dev Mode Pre-filled
                    </div>
                    <div>SuperAdmin: <code>superadmin@homunculus.test</code></div>
                </div>
            <?php endif; ?>

            <!-- Alerts -->
            <?php if (session('error') !== null): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <?= esc(session('error')) ?>
                </div>
            <?php elseif (session('errors') !== null): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php if (is_array(session('errors'))): ?>
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><?= esc(session('errors')) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session('message') !== null): ?>
                <div class="alert alert-success py-2 small" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <?= esc(session('message')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= url_to('login') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email หรือ Username -->
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email', $defaultEmail) ?>" required autofocus>
                    <div class="input-group-text bg-body-tertiary">
                        <span class="bi bi-envelope text-secondary"></span>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>" value="<?= $defaultPassword ?>" required>
                    <div class="input-group-text bg-body-tertiary">
                        <span class="bi bi-lock text-secondary"></span>
                    </div>
                </div>

                <!-- Remember Me & Submit Button -->
                <div class="row align-items-center mb-3">
                    <div class="col-7">
                        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                            <div class="form-check mb-0">
                                <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck" <?php if (old('remember')): ?> checked<?php endif ?>>
                                <label class="form-check-label small text-secondary" for="rememberCheck">
                                    <?= lang('Auth.rememberMe') ?>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-5">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary fw-medium">
                                <?= lang('Auth.login') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Links (Magic Link / Register) -->
            <?php if (setting('Auth.allowMagicLinkLogins')): ?>
                <p class="mb-1 text-center small">
                    <a href="<?= url_to('magic-link') ?>" class="text-decoration-none">
                        <?= lang('Auth.forgotPassword') ?>
                    </a>
                </p>
            <?php endif; ?>

            <?php if (setting('Auth.allowRegistration')): ?>
                <p class="mb-0 text-center small mt-2">
                    <a href="<?= url_to('register') ?>" class="text-decoration-none">
                        <?= lang('Auth.needAccount') ?> <?= lang('Auth.register') ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to Public Link -->
    <div class="text-center mt-3">
        <a href="<?= site_url('/') ?>" class="text-secondary small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>กลับสู่หน้าหลัก
        </a>
    </div>
</div>
<?= $this->endSection() ?>
