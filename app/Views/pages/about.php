<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา - Homunculus</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

    <!-- Assets -->
    <link rel="stylesheet" href="<?= vendor('fonts/noto-sans-thai/noto-sans-thai.css') ?>">
    <link rel="stylesheet" href="<?= vendor('adminlte/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('bootstrap-icons/bootstrap-icons.min.css') ?>">

    <style>
        body {
            font-family: 'Noto Sans Thai', system-ui, -apple-system, sans-serif;
            background-color: #fdfdfd;
            color: #1e293b;
        }

        .navbar-blur {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background-color: rgba(255, 255, 255, 0.85);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .page-header {
            padding: 5.5rem 0 3rem;
        }

        .info-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
        }

        .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-blur">
        <div class="container py-1">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 text-dark" href="<?= base_url() ?>">
                <span class="d-inline-flex align-items-center justify-content-center bg-dark text-white rounded-3 p-2" style="width: 32px; height: 32px;">
                    <i class="bi bi-cpu-fill" style="font-size: 1rem;"></i>
                </span>
                <span>Homunculus</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="สลับเมนู">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="<?= base_url() ?>">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-dark fw-medium" aria-current="page" href="<?= site_url('about') ?>">เกี่ยวกับ</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if (auth()->loggedIn()): ?>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            <i class="bi bi-speedometer2 me-1"></i> แดชบอร์ด
                        </a>
                        <a href="<?= url_to('logout') ?>" class="btn btn-light btn-sm rounded-pill px-3 text-secondary">
                            ออกจากระบบ
                        </a>
                    <?php else: ?>
                        <a href="<?= url_to('login') ?>" class="btn btn-link text-decoration-none text-secondary btn-sm px-3 fw-medium">
                            เข้าสู่ระบบ
                        </a>
                        <a href="<?= url_to('register') ?>" class="btn btn-dark btn-sm rounded-pill px-3 fw-medium shadow-sm">
                            <i class="bi bi-person-plus me-1"></i> ลงทะเบียน
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <!-- Page Header -->
        <section class="page-header text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill bg-light border border-secondary-subtle small text-secondary">
                            <i class="bi bi-info-circle"></i>
                            <span>เกี่ยวกับโครงการ</span>
                        </div>
                        <h1 class="display-5 fw-bold mb-3">เกี่ยวกับ Homunculus</h1>
                        <p class="lead text-secondary mx-auto" style="max-width: 620px; line-height: 1.8;">
                            มุ่งเน้นการสร้างเว็บแอปพลิเคชันที่เรียบง่าย ปลอดภัย และมีประสิทธิภาพสูง ด้วยสถาปัตยกรรมที่คลีนและเครื่องมือที่ทันสมัย
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Story / Overview Section -->
        <section class="py-4">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-10">
                        <div class="card p-4 p-md-5 info-card bg-white">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-6">
                                    <h2 class="h4 fw-bold mb-3">แนวคิดและเป้าหมาย</h2>
                                    <p class="text-secondary mb-3" style="line-height: 1.8;">
                                        Homunculus ถูกพัฒนาขึ้นโดยยึดหลัก Minimalist & Clean Architecture ลดทอนความซับซ้อนที่ไม่จำเป็น และคงไว้ซึ่งสิ่งที่ส่งมอบคุณค่าสูงสุดแก่ผู้ใช้งาน
                                    </p>
                                    <p class="text-secondary mb-0" style="line-height: 1.8;">
                                        ทุกโมดูลได้รับการออกแบบให้รองรับการขยายตัวได้อย่างราบรื่น ไม่ว่าจะเป็นระบบยืนยันตัวตน การจัดการสิทธิ์ หรือการปรับแต่งหน้าแดชบอร์ด
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-4 bg-light rounded-4 border border-light-subtle">
                                        <h3 class="h6 fw-bold text-dark mb-3">เทคโนโลยีหลัก</h3>
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-secondary">
                                            <li class="d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span><strong>Backend:</strong> CodeIgniter 4 & Shield Authentication</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span><strong>UI Framework:</strong> AdminLTE 4 & Bootstrap 5</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span><strong>Typography:</strong> Noto Sans Thai (Self-hosted)</span>
                                            </li>
                                            <li class="d-flex align-items-center gap-2">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span><strong>Icons:</strong> Bootstrap Icons (Local Assets)</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="py-5">
            <div class="container">
                <div class="text-center mb-4">
                    <h2 class="h4 fw-bold mb-1">ค่านิยมหลักของเรา</h2>
                    <p class="text-secondary small">สิ่งที่เรายึดถือในการออกแบบและพัฒนา</p>
                </div>

                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="card h-100 info-card p-4 bg-white text-center">
                            <div class="icon-box bg-primary-subtle text-primary mx-auto mb-3">
                                <i class="bi bi-feather"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2">ความเรียบง่าย (Simplicity)</h3>
                            <p class="text-secondary small mb-0">
                                อินเทอร์เฟซที่สะอาดตา โค้ดที่กระชับ เข้าใจง่าย และไม่เพิ่มภาระที่ไม่จำเป็น
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 info-card p-4 bg-white text-center">
                            <div class="icon-box bg-success-subtle text-success mx-auto mb-3">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2">ความปลอดภัย (Security)</h3>
                            <p class="text-secondary small mb-0">
                                วางโครงสร้างความปลอดภัยตั้งแต่ระดับรากฐาน ด้วยการเข้ารหัสและการจัดการสิทธิ์ที่ได้มาตรฐาน
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 info-card p-4 bg-white text-center">
                            <div class="icon-box bg-info-subtle text-info mx-auto mb-3">
                                <i class="bi bi-speedometer"></i>
                            </div>
                            <h3 class="h6 fw-bold mb-2">สมรรถนะ (Performance)</h3>
                            <p class="text-secondary small mb-0">
                                โหลดทรัพยากรเฉพาะที่จำเป็น ทำงานได้รวดเร็วทั้งบนคอมพิวเตอร์และอุปกรณ์พกพา
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Call to action card -->
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-8 text-center">
                        <div class="p-4 p-md-5 rounded-4 bg-light border border-light-subtle">
                            <h3 class="h5 fw-bold mb-2">พร้อมเริ่มต้นใช้งานแล้วหรือยัง?</h3>
                            <p class="text-secondary small mb-4">เข้าร่วมกับเราวันนี้เพื่อสัมผัสประสบการณ์การทำงานที่คล่องตัว</p>
                            <div class="d-flex justify-content-center gap-2">
                                <?php if (!auth()->loggedIn()): ?>
                                    <a href="<?= url_to('register') ?>" class="btn btn-dark btn-sm rounded-pill px-4 py-2">
                                        ลงทะเบียนทันที
                                    </a>
                                    <a href="<?= url_to('login') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2">
                                        เข้าสู่ระบบ
                                    </a>
                                <?php else: ?>
                                    <a href="<?= site_url('dashboard') ?>" class="btn btn-dark btn-sm rounded-pill px-4 py-2">
                                        ไปยังแดชบอร์ด
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-4 bg-white text-center text-secondary small border-top">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Homunculus. สงวนลิขสิทธิ์ทั้งหมด</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="<?= vendor('bootstrap/bootstrap.min.js') ?>"></script>
</body>
</html>
