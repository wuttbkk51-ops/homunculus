<?php
/**
 * @var string|null $title
 */
$title ??= lang('Auth.login');
$pageClass = 'login-page';
?>
<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
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
<?= $this->endSection() ?>
