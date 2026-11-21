<?php
/**
 * @var string|null $title
 */
$title ??= lang('Auth.register');
$pageClass = 'register-page';
?>
<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
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
<?= $this->endSection() ?>
