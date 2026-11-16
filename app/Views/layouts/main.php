<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?: 'Homunculus - Enterprise ERP & POS Engine' ?></title>

    <!-- CDN Preconnect & DNS-Prefetch -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font (Non-blocking) -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"></noscript>

    <style>
        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Sarabun', 'Noto Sans Thai', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar สาธารณะ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="<?= site_url('/') ?>">
                <i class="bi bi-cpu-fill fs-3"></i>
                <span class="fs-4">Homunculus</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link active fw-medium" aria-current="page" href="<?= site_url('/') ?>">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-body-secondary" href="#features">คุณสมบัติระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-body-secondary" href="#architecture">สถาปัตยกรรม</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if (auth()->loggedIn()): ?>
                        <span class="text-body-secondary small me-2 d-none d-sm-inline">
                            สวัสดี, <strong><?= esc(auth()->user()->username) ?></strong>
                        </span>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-speedometer2"></i>
                            <span>เข้าสู่ระบบจัดการ (Admin)</span>
                        </a>
                        <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm" title="ออกจากระบบ">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('login') ?>" class="btn btn-outline-primary px-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i>เข้าสู่ระบบ
                        </a>
                        <?php if (setting('Auth.allowRegistration')): ?>
                            <a href="<?= site_url('register') ?>" class="btn btn-primary px-3">
                                <i class="bi bi-person-plus me-1"></i>สมัครใช้งาน
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container text-center text-muted small">
            <p class="mb-1">Homunculus &copy; <?= date('Y') ?> - Multi-Tenant &amp; Multi-Branch ERP Engine</p>
            <p class="mb-0">สร้างด้วย CodeIgniter 4 + AdminLTE 4 (Bootstrap 5)</p>
        </div>
    </footer>

    <!-- Bootstrap 5 Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
