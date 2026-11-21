<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
<?php
$rawSectionTitle = $this->renderSection('title');
$rawPageTitle    = $this->renderSection('page_title');
$resolvedTitle   = $rawPageTitle ?: ($title ?? '');
$appName         = lang('App.appName');

if ($rawSectionTitle !== '') {
    $browserTitle = $rawSectionTitle;
} elseif ($resolvedTitle !== '') {
    $browserTitle = $resolvedTitle . ' - ' . $appName;
} else {
    $browserTitle = $appName;
}
?>
<title><?= esc($browserTitle) ?></title>

<!--begin::Theme Init (prevents flash of incorrect theme on load)-->
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
<!--end::Theme Init-->

<meta name="color-scheme" content="light dark">
<meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)">

<!-- Google Fonts: Noto Sans Thai (Local Offline) -->
<link rel="stylesheet" href="<?= base_url('assets/css/noto-sans-thai.css') ?>">

<!-- Local Plugins & Icons -->
<link rel="stylesheet" href="<?= base_url('assets/bootstrap-icons/bootstrap-icons.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/overlayscrollbars/overlayscrollbars.min.css') ?>">

<!-- AdminLTE 4 Local CSS -->
<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte-colors.min.css') ?>">

<style>
    body {
        font-family: 'Noto Sans Thai', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    }
</style>
