<!doctype html>
<html lang="th">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= lang('Auth.login') ?> | Homunculus</title>

    <!--begin::Theme Init (prevents flash of incorrect theme)-->
    <script>
      (() => {
        'use strict';
        const root = document.documentElement;
        if (root.getAttribute('data-lte-color-mode') === 'off') return;
        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try { stored = localStorage.getItem(STORAGE_KEY); } catch {}
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
        if (resolved !== authored) root.setAttribute('data-lte-theme-resolved', '');
      })();
    </script>
    <!--end::Theme Init-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.3.1/index.css"
      integrity="sha256-VzMk9RVNVApO0cXfOO7il3dD8OPyu0aDyyLDd8EEGk8="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <!--end::Fonts-->

    <!--begin::Bootstrap Icons-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Bootstrap Icons-->

    <!--begin::AdminLTE-->
    <link rel="preload" href="<?= base_url('assets/vendor/adminlte/css/adminlte.min.css') ?>" as="style" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/adminlte/css/adminlte.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/vendor/adminlte/css/adminlte-colors.min.css') ?>" />
    <!--end::AdminLTE-->
  </head>
  <!--end::Head-->

  <!--begin::Body-->
  <body class="login-page bg-body-secondary">
    <main class="login-box">
      <h1 class="login-logo">
        <a href="<?= base_url() ?>"><b>Homun</b>culus</a>
      </h1>

      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg"><?= lang('Auth.login') ?></p>

          <?php if (session('error') !== null) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?= esc(session('error')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php elseif (session('errors') !== null) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?php if (is_array(session('errors'))) : ?>
                <?php foreach (session('errors') as $error) : ?>
                  <?= esc($error) ?><br>
                <?php endforeach ?>
              <?php else : ?>
                <?= esc(session('errors')) ?>
              <?php endif ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif ?>

          <?php if (session('message') !== null) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>
              <?= esc(session('message')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif ?>

          <form action="<?= url_to('login') ?>" method="post">
            <?= csrf_field() ?>

            <!--begin::Email-->
          <?php $validFields = setting('Auth.validFields'); ?>

<!--begin::Login Identifier-->

<?php if (in_array('email', $validFields, true) && in_array('username', $validFields, true)) : ?>

  <label class="visually-hidden" for="loginField">
    Email or Username
  </label>

  <div class="input-group mb-3">
    <input
      id="loginField"
      type="text"
      name="login"
      class="form-control<?= (session('errors.login') ?? '') !== '' ? ' is-invalid' : '' ?>"
      placeholder="Email or Username"
      value="<?= old('login') ?>"
      autocomplete="username"
      required
    />
    <div class="input-group-text">
      <span class="bi bi-person-badge"></span>
    </div>

    <?php if ((session('errors.login') ?? '') !== '') : ?>
      <div class="invalid-feedback">
        <?= esc(session('errors.login')) ?>
      </div>
    <?php endif ?>
  </div>

<?php elseif (in_array('email', $validFields, true)) : ?>

  <label class="visually-hidden" for="loginField">
    <?= lang('Auth.email') ?>
  </label>

  <div class="input-group mb-3">
    <input
      id="loginField"
      type="email"
      name="login"
      class="form-control<?= (session('errors.login') ?? '') !== '' ? ' is-invalid' : '' ?>"
      placeholder="<?= lang('Auth.email') ?>"
      value="<?= old('login') ?>"
      inputmode="email"
      autocomplete="email"
      required
    />
    <div class="input-group-text">
      <span class="bi bi-envelope"></span>
    </div>

    <?php if ((session('errors.login') ?? '') !== '') : ?>
      <div class="invalid-feedback">
        <?= esc(session('errors.login')) ?>
      </div>
    <?php endif ?>
  </div>

<?php elseif (in_array('username', $validFields, true)) : ?>

  <label class="visually-hidden" for="loginField">
    <?= lang('Auth.username') ?>
  </label>

  <div class="input-group mb-3">
    <input
      id="loginField"
      type="text"
      name="login"
      class="form-control<?= (session('errors.login') ?? '') !== '' ? ' is-invalid' : '' ?>"
      placeholder="<?= lang('Auth.username') ?>"
      value="<?= old('login') ?>"
      autocomplete="username"
      required
    />
    <div class="input-group-text">
      <span class="bi bi-person"></span>
    </div>

    <?php if ((session('errors.login') ?? '') !== '') : ?>
      <div class="invalid-feedback">
        <?= esc(session('errors.login')) ?>
      </div>
    <?php endif ?>
  </div>

<?php endif ?>

<!--end::Login Identifier-->
            <!--end::Email-->

            <!--begin::Password-->
            <label class="visually-hidden" for="loginPassword"><?= lang('Auth.password') ?></label>
            <div class="input-group mb-3">
              <input
                id="loginPassword"
                type="password"
                name="password"
                class="form-control"
                placeholder="<?= lang('Auth.password') ?>"
                autocomplete="current-password"
                required
              />
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
            </div>
            <!--end::Password-->

            <!--begin::Row-->
            <div class="row">
              <div class="col-8">
                <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?>
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      name="remember"
                      id="rememberMe"
                      <?php if (old('remember')) : ?>checked<?php endif ?>
                    />
                    <label class="form-check-label" for="rememberMe">
                      <?= lang('Auth.rememberMe') ?>
                    </label>
                  </div>
                <?php endif ?>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">
                    <?= lang('Auth.login') ?>
                  </button>
                </div>
              </div>
            </div>
            <!--end::Row-->
          </form>

          <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
            <p class="mb-1 text-center mt-3">
              <a href="<?= url_to('magic-link') ?>">
                <i class="bi bi-magic me-1"></i><?= lang('Auth.forgotPassword') ?>
              </a>
            </p>
          <?php endif ?>

          <?php if (setting('Auth.allowRegistration')) : ?>
            <p class="mb-0 text-center">
              <a href="<?= url_to('register') ?>">
                <i class="bi bi-person-plus me-1"></i><?= lang('Auth.needAccount') ?> <?= lang('Auth.register') ?>
              </a>
            </p>
          <?php endif ?>

        </div>
        <!--/.login-card-body-->
      </div>
    </main>
    <!--/.login-box-->

    <!--begin::OverlayScrollbars-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.17.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::OverlayScrollbars-->

    <!--begin::Popper + Bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <!--end::Bootstrap-->

    <!--begin::AdminLTE-->
    <script src="<?= base_url('assets/vendor/adminlte/js/adminlte.min.js') ?>"></script>
    <!--end::AdminLTE-->
  </body>
  <!--end::Body-->
</html>
