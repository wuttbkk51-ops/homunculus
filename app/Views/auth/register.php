<!doctype html>
<html lang="th">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= lang('Auth.register') ?> | Homunculus</title>

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
  <body class="register-page bg-body-secondary">
    <main class="register-box">
      <h1 class="register-logo">
        <a href="<?= base_url() ?>"><b>Homun</b>culus</a>
      </h1>
      <!-- /.register-logo -->

      <div class="card">
        <div class="card-body register-card-body">
          <p class="register-box-msg"><?= lang('Auth.register') ?></p>

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

          <form action="<?= url_to('register') ?>" method="post">
            <?= csrf_field() ?>

            <!--begin::Company Name-->
            <label class="visually-hidden" for="registerCompanyName"><?= lang('Auth.companyName') ?></label>
            <div class="input-group mb-3">
              <input
                id="registerCompanyName"
                type="text"
                name="company_name"
                class="form-control<?= (session('errors.company_name') ?? '') !== '' ? ' is-invalid' : '' ?>"
                placeholder="<?= lang('Auth.companyName') ?>"
                value="<?= old('company_name') ?>"
                autocomplete="organization"
                required
              />
              <div class="input-group-text">
                <span class="bi bi-building"></span>
              </div>
              <?php if ((session('errors.company_name') ?? '') !== '') : ?>
                <div class="invalid-feedback"><?= esc(session('errors.company_name')) ?></div>
              <?php endif ?>
            </div>
            <!--end::Company Name-->

            <?php $validFields = setting('Auth.validFields'); ?>

            <!--begin::Email-->
            <?php if (in_array('email', $validFields, true)) : ?>
            <label class="visually-hidden" for="registerEmail"><?= lang('Auth.email') ?></label>
            <div class="input-group mb-3">
              <input
                id="registerEmail"
                type="email"
                name="email"
                class="form-control<?= (session('errors.email') ?? '') !== '' ? ' is-invalid' : '' ?>"
                placeholder="<?= lang('Auth.email') ?>"
                value="<?= old('email') ?>"
                inputmode="email"
                autocomplete="email"
                <?php if (! in_array('username', $validFields, true)) : ?>required<?php endif ?>
              />
              <div class="input-group-text">
                <span class="bi bi-envelope"></span>
              </div>
              <?php if ((session('errors.email') ?? '') !== '') : ?>
                <div class="invalid-feedback"><?= esc(session('errors.email')) ?></div>
              <?php endif ?>
            </div>
            <?php endif ?>
            <!--end::Email-->

            <!--begin::Username-->
            <?php if (in_array('username', $validFields, true)) : ?>
            <label class="visually-hidden" for="registerUsername"><?= lang('Auth.username') ?></label>
            <div class="input-group mb-3">
              <input
                id="registerUsername"
                type="text"
                name="username"
                class="form-control<?= (session('errors.username') ?? '') !== '' ? ' is-invalid' : '' ?>"
                placeholder="<?= lang('Auth.username') ?>"
                value="<?= old('username') ?>"
                autocomplete="username"
                required
              />
              <div class="input-group-text">
                <span class="bi bi-person"></span>
              </div>
              <?php if ((session('errors.username') ?? '') !== '') : ?>
                <div class="invalid-feedback"><?= esc(session('errors.username')) ?></div>
              <?php endif ?>
            </div>
            <?php endif ?>
            <!--end::Username-->

            <!--begin::Password-->
            <label class="visually-hidden" for="registerPassword"><?= lang('Auth.password') ?></label>
            <div class="input-group mb-3">
              <input
                id="registerPassword"
                type="password"
                name="password"
                class="form-control<?= (session('errors.password') ?? '') !== '' ? ' is-invalid' : '' ?>"
                placeholder="<?= lang('Auth.password') ?>"
                autocomplete="new-password"
                required
              />
              <div class="input-group-text">
                <span class="bi bi-lock-fill"></span>
              </div>
              <?php if ((session('errors.password') ?? '') !== '') : ?>
                <div class="invalid-feedback"><?= esc(session('errors.password')) ?></div>
              <?php endif ?>
            </div>
            <!--end::Password-->

            <!--begin::Confirm Password-->
            <label class="visually-hidden" for="registerPasswordConfirm"><?= lang('Auth.passwordConfirm') ?></label>
            <div class="input-group mb-3">
              <input
                id="registerPasswordConfirm"
                type="password"
                name="password_confirm"
                class="form-control<?= (session('errors.password_confirm') ?? '') !== '' ? ' is-invalid' : '' ?>"
                placeholder="<?= lang('Auth.passwordConfirm') ?>"
                autocomplete="new-password"
                required
              />
              <div class="input-group-text">
                <span class="bi bi-lock"></span>
              </div>
              <?php if ((session('errors.password_confirm') ?? '') !== '') : ?>
                <div class="invalid-feedback"><?= esc(session('errors.password_confirm')) ?></div>
              <?php endif ?>
            </div>
            <!--end::Confirm Password-->

            <!--begin::Row-->
            <div class="row">
              <div class="col-8">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    name="agree_terms"
                    id="agreeTerms"
                    value="1"
                    required
                  />
                  <label class="form-check-label" for="agreeTerms">
                    <?= lang('Auth.agreeToThe') ?> <a href="#"><?= lang('Auth.terms') ?></a>
                  </label>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-4">
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">
                    <?= lang('Auth.register') ?>
                  </button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </form>

          <p class="mb-0 text-center mt-3">
            <a href="<?= url_to('login') ?>">
              <i class="bi bi-box-arrow-in-right me-1"></i><?= lang('Auth.haveAccount') ?> <?= lang('Auth.login') ?>
            </a>
          </p>
        </div>
        <!-- /.register-card-body -->
      </div>
    </main>
    <!-- /.register-box -->

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
