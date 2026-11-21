<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <?= $this->include('layouts/partials/header') ?>

    <!-- Page Specific Styles -->
    <?= $this->renderSection('contentStyles') ?>
</head>
<body class="<?= esc($pageClass ?? 'login-page') ?> bg-body-secondary">
    <?= $this->renderSection('content') ?>

    <!-- Core Scripts -->
    <?= $this->include('layouts/partials/scripts') ?>

    <!-- Page Specific Scripts -->
    <?= $this->renderSection('contentScript') ?>
</body>
</html>
