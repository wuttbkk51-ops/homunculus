<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยินดีต้อนรับ - Homunculus</title>
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

        .hero-section {
            padding: 5.5rem 0 4rem;
        }

        .feature-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-blur">
        <div class="container py-1">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 text-dark" href="<?= base_url() ?>">
                <img src="<?= base_url('assets/img/icon.svg') ?>" alt="Homunculus" width="32" height="32" class="rounded-2">
                <span>Homunculus</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="สลับเมนู">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link text-secondary active" aria-current="page" href="<?= base_url() ?>">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="<?= site_url('about') ?>">เกี่ยวกับ</a>
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
        <!-- Hero Section -->
        <section class="hero-section text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-4 rounded-pill bg-light border border-secondary-subtle small text-secondary">
                            <span class="badge bg-primary rounded-pill">ใหม่</span>
                            <span>ระบบจัดการและพัฒนาแบบมินิมอล</span>
                        </div>

                        <h1 class="display-4 fw-bold mb-3 tracking-tight">
                            เรียบง่าย ทรงพลัง เพื่อการพัฒนาอย่างไร้ขีดจำกัด
                        </h1>

                        <p class="lead text-secondary mb-4 mx-auto" style="max-width: 600px; font-weight: 400; line-height: 1.8;">
                            แพลตฟอร์มที่ออกแบบอย่างใส่ใจในทุกรายละเอียด ให้คุณเริ่มต้นใช้งานและโฟกัสกับสิ่งที่สำคัญที่สุดได้อย่างรวดเร็วและปลอดภัย
                        </p>

                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <?php if (auth()->loggedIn()): ?>
                                <a href="<?= site_url('dashboard') ?>" class="btn btn-primary btn-lg rounded-pill px-4 py-2 fs-6 fw-semibold shadow-sm">
                                    ไปยังแดชบอร์ด <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?= url_to('register') ?>" class="btn btn-primary btn-lg rounded-pill px-4 py-2 fs-6 fw-semibold shadow-sm">
                                    เริ่มต้นใช้งานฟรี <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="<?= url_to('login') ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4 py-2 fs-6 fw-semibold">
                                    เข้าสู่ระบบ
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features / Highlights -->
        <section class="py-5 bg-white border-top border-bottom border-light-subtle">
            <div class="container py-4">
                <div class="text-center mb-5">
                    <h2 class="h3 fw-bold mb-2">ทำไมต้องเลือกเรา</h2>
                    <p class="text-secondary small mb-0">ออกแบบโครงสร้างและฟีเจอร์อย่างพิถีพิถันเพื่อตอบโจทย์การทำงาน</p>
                </div>

                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="card h-100 feature-card p-4 bg-white">
                            <div class="icon-box bg-primary-subtle text-primary mb-3">
                                <i class="bi bi-lightning-charge"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-2">รวดเร็วและเบา</h3>
                            <p class="text-secondary small mb-0">
                                ทำงานบนสถาปัตยกรรมที่คล่องตัว โหลดเร็ว ไร้ส่วนเกินที่ไม่จำเป็น ช่วยให้คุณทำงานได้ต่อเนื่อง
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 feature-card p-4 bg-white">
                            <div class="icon-box bg-success-subtle text-success mb-3">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-2">ปลอดภัยสูงสุด</h3>
                            <p class="text-secondary small mb-0">
                                ปกป้องข้อมูลและจัดการการเข้าถึงด้วยระบบสิทธิ์ที่ได้มาตรฐาน และการเข้ารหัสที่รัดกุม
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 feature-card p-4 bg-white">
                            <div class="icon-box bg-warning-subtle text-warning mb-3">
                                <i class="bi bi-sliders"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-2">ปรับแต่งได้ง่าย</h3>
                            <p class="text-secondary small mb-0">
                                โครงสร้างโค้ดเป็นระเบียบ ยืดหยุ่น พร้อมรองรับการต่อยอดและขยายฟังก์ชันตามความต้องการ
                            </p>
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
