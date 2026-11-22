<?php

declare(strict_types=1);

/**
 * Download external frontend dependencies for AdminLTE 4 and local fonts.
 * - Dev Mode: Downloads missing assets to ./public/assets/*
 * - Production Mode: Skips downloading completely
 */

$baseDir = dirname(__DIR__);
$args = $argv ?? [];
$force = in_array('--force', $args, true);

// 1. Detect Production Mode
$isComposerNoDev = getenv('COMPOSER_DEV_MODE') === '0';
$isCliNoDev = in_array('--no-dev', $args, true) || in_array('--prod', $args, true);

// Check environment variables (CI_ENVIRONMENT, ENVIRONMENT, APP_ENV)
$env = getenv('CI_ENVIRONMENT') ?: getenv('ENVIRONMENT') ?: getenv('APP_ENV');

// Check .env file fallback
if (! $env && file_exists($baseDir . '/.env')) {
    $lines = file($baseDir . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (preg_match('/^CI_ENVIRONMENT\s*=\s*(.+)$/i', $line, $matches)) {
            $env = trim($matches[1], " \t\n\r\0\x0B\"'");
            break;
        }
    }
}

$isProduction = $isComposerNoDev || $isCliNoDev || (strtolower((string) $env) === 'production');

// In Production: Skip downloading
if ($isProduction && ! $force) {
    echo "[Assets] Skipped: Production mode detected (CI_ENVIRONMENT=production or --no-dev). Asset download is disabled in production.\n";
    exit(0);
}

echo "[Assets] Dev mode detected: Checking AdminLTE 4 external dependencies...\n";

$assetsDir = $baseDir . '/public/assets/vendor';

$dependencies = [
    // Bootstrap Icons 1.13.2
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css',
        'dest' => 'bootstrap-icons/bootstrap-icons.min.css',
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/fonts/bootstrap-icons.woff2',
        'dest' => 'bootstrap-icons/fonts/bootstrap-icons.woff2',
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/fonts/bootstrap-icons.woff',
        'dest' => 'bootstrap-icons/fonts/bootstrap-icons.woff',
    ],

    // OverlayScrollbars 2.17.0
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/overlayscrollbars@2.17.0/styles/overlayscrollbars.min.css',
        'dest' => 'overlayscrollbars/overlayscrollbars.min.css',
    ],
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/overlayscrollbars@2.17.0/browser/overlayscrollbars.browser.es6.min.js',
        'dest' => 'overlayscrollbars/overlayscrollbars.browser.es6.min.js',
    ],

    // Popper.js Core 2.11.8
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js',
        'dest' => 'popper/popper.min.js',
    ],

    // Bootstrap JS 5.3.9
    [
        'url'  => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.min.js',
        'dest' => 'bootstrap/bootstrap.min.js',
    ],

    // Google Fonts: Noto Sans Thai (Variable font woff2)
    [
        'url'  => 'https://fonts.gstatic.com/s/notosansthai/v29/iJWQBXeUZi_OHPqn4wq6hQ2_hbJ1xyN9wd43SofNWcdvPoemb2k.woff2',
        'dest' => 'fonts/noto-sans-thai/fonts/noto-sans-thai-variable.woff2',
    ],
];

// 1. AdminLTE check (already managed by composer copy-file)
if (file_exists($assetsDir . '/adminlte/css/adminlte.min.css')) {
    echo "  - [SKIP] adminlte (already handled by composer copy-file)\n";
}

// 2. Download missing dependencies
$streamContext = stream_context_create([
    'http' => [
        'timeout' => 15,
        'user_agent' => 'Mozilla/5.0 (compatible; HomunculusAssetsDownloader/1.0)',
    ],
    'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true,
    ],
]);

foreach ($dependencies as $dep) {
    $targetFile = $assetsDir . '/' . $dep['dest'];
    $targetDir = dirname($targetFile);

    if (! $force && file_exists($targetFile) && filesize($targetFile) > 0) {
        echo "  - [SKIP] {$dep['dest']} (already exists)\n";
        continue;
    }

    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    echo "  - [DOWNLOAD] {$dep['dest']} ... ";

    $content = @file_get_contents($dep['url'], false, $streamContext);
    if ($content === false) {
        echo "FAILED!\n";
        fwrite(STDERR, "Error: Could not download {$dep['url']}\n");
        continue;
    }

    file_put_contents($targetFile, $content);
    echo "OK (" . round(strlen($content) / 1024, 1) . " KB)\n";
}

// 3. Generate local CSS for Noto Sans Thai if not exists
$fontCssFile = $assetsDir . '/fonts/noto-sans-thai/noto-sans-thai.css';
if ($force || ! file_exists($fontCssFile)) {
    $fontCssContent = <<<'CSS'
@font-face {
  font-family: 'Noto Sans Thai';
  font-style: normal;
  font-weight: 100 900;
  font-stretch: 100%;
  font-display: swap;
  src: url('fonts/noto-sans-thai-variable.woff2') format('woff2');
}

body {
  font-family: 'Noto Sans Thai', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}
CSS;

    file_put_contents($fontCssFile, $fontCssContent);
    echo "  - [GENERATE] fonts/noto-sans-thai/noto-sans-thai.css ... OK\n";
} else {
    echo "  - [SKIP] fonts/noto-sans-thai/noto-sans-thai.css (already exists)\n";
}

echo "[Assets] All dependencies are ready in ./public/assets/vendor/*\n";
