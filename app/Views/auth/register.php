<?= $this->extend('auth/layout') ?>

<?= $this->section('title') ?>สมัครสมาชิก - Homunculus<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="register-box" style="width: 100%; max-width: 420px;">
    <!-- Logo -->
    <div class="register-logo mb-4 text-center">
        <a href="<?= site_url('/') ?>" class="link-body-emphasis text-decoration-none fs-2 fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-cpu-fill text-primary fs-1"></i>
            <span><b>Homunculus</b></span>
        </a>
    </div>

    <!-- Card -->
    <div class="card card-outline card-primary shadow-sm border-0">
        <div class="card-body register-card-body p-4">
            <p class="register-box-msg text-center text-secondary mb-4">ลงทะเบียนเปิดใช้งานบัญชีใหม่</p>

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

            <form action="<?= url_to('register') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Username -->
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="username" inputmode="text" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" required>
                    <div class="input-group-text bg-body-tertiary">
                        <span class="bi bi-person text-secondary"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
                    <div class="input-group-text bg-body-tertiary">
                        <span class="bi bi-envelope text-secondary"></span>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required>
                    <div class="input-group-text bg-body-tertiary">
                        <span class="bi bi-lock text-secondary"></span>
                    </div>
                </div>

                <!-- Password Confirm -->
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required>
                    <div class="input-group-text bg-body-tertiary">
                        <span class="bi bi-shield-check text-secondary"></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary fw-medium">
                        <?= lang('Auth.register') ?>
                    </button>
                </div>
            </form>

            <p class="mb-0 text-center small">
                <?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>" class="text-decoration-none fw-medium"><?= lang('Auth.login') ?></a>
            </p>
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
