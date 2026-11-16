<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>หน้าแรก - Homunculus Enterprise Platform<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="py-5 bg-white border-bottom">
    <div class="container py-lg-4 text-center">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill mb-3 fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> Multi-Tenant &amp; Multi-Branch Platform
                </span>
                <h1 class="display-5 fw-bold text-dark mb-3">
                    ระบบบริหารจัดการองค์กร <span class="text-primary">Homunculus</span>
                </h1>
                <p class="lead text-secondary mb-4">
                    แพลตฟอร์ม ERP &amp; POS ยุคใหม่ ออกแบบสถาปัตยกรรม CSMV ควบคุมการเข้าถึงด้วย Contextual RBAC รองรับหลายสาขาและแยกข้อมูลองค์กรอย่างปลอดภัย
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <?php if (auth()->loggedIn()): ?>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-speedometer2 me-2"></i>เข้าสู่หน้าจัดการ (Dashboard)
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('login') ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ Admin
                        </a>
                        <?php if (setting('Auth.allowRegistration')): ?>
                            <a href="<?= site_url('register') ?>" class="btn btn-outline-secondary btn-lg px-4">
                                สมัครสมาชิก
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container py-lg-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-2">จุดเด่นและความสามารถหลัก</h2>
            <p class="text-secondary">โครงสร้างที่แข็งแกร่ง เรียบง่าย และมีความปลอดภัยสูงสุด</p>
        </div>

        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-3">
                    <div class="bg-primary-subtle text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Multi-Tenant &amp; Branches</h5>
                    <p class="text-secondary small mb-0">
                        แยกข้อมูลของแต่ละองค์กรโดยอัตโนมัติผ่าน BaseTenantModel พร้อมระบบเลือกและสลับสาขา (Branch Switcher) ได้อย่างยืดหยุ่น
                    </p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-3">
                    <div class="bg-success-subtle text-success rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-lock fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Contextual RBAC</h5>
                    <p class="text-secondary small mb-0">
                        ตรวจสอบสิทธิ์แบบ Permission-First ตามบริบทของสาขาที่กำลังทำงานอยู่ ป้องกันการเข้าถึงข้อมูลข้ามบทบาทและข้ามสาขาอย่างรัดกุม
                    </p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 rounded-3">
                    <div class="bg-info-subtle text-info rounded-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-layers fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">AdminLTE 4 &amp; CSMV</h5>
                    <p class="text-secondary small mb-0">
                        หน้าจอจัดการ Admin สวยงาม ทันสมัย สร้างบน Bootstrap 5 แท้ ไร้โค้ดรกรุงรัง โครงสร้างโค้ดแบบ Controller-Service-Model-View ที่เป็นระเบียบ
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Architecture Overview Section -->
<section id="architecture" class="py-5 bg-white border-top">
    <div class="container py-lg-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-primary fw-semibold text-uppercase small">Clean &amp; Minimal Architecture</span>
                <h3 class="fw-bold mt-2 mb-3">ความปลอดภัยและการป้องกัน 2 ระดับ</h3>
                <p class="text-secondary mb-4">
                    ระบบ Homunculus ใช้การรักษาความปลอดภัยแบบ 2 ชั้น (Two-Layer Defense) ผ่าน Filter Gatekeeper ระดับ URL และ Contextual RBAC ระดับ Service/Controller
                </p>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="d-flex align-items-start gap-3">
                        <i class="bi bi-check2-circle text-primary fs-5 mt-1"></i>
                        <div>
                            <strong>Layer 1: Gatekeeper Filters</strong>
                            <p class="text-secondary small mb-0">ป้องกันเส้นทาง admin ทั้งหมดด้วย Session, Tenant Active Check และ Active Branch Check</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <i class="bi bi-check2-circle text-primary fs-5 mt-1"></i>
                        <div>
                            <strong>Layer 2: Granular RBAC &amp; Tenant Isolation</strong>
                            <p class="text-secondary small mb-0">ป้องกันการรั่วไหลของข้อมูลด้วย BaseTenantModel และตรวจสอบสิทธิ์แบบเจาะจงระดับข้อมูล</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 bg-light p-4 rounded-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <span class="fw-bold text-dark">สถานะระบบปัจจุบัน</span>
                        <span class="badge bg-success">พร้อมทำงาน (Active)</span>
                    </div>
                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">CodeIgniter Framework:</span>
                            <span class="fw-semibold">v<?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">UI Engine:</span>
                            <span class="fw-semibold">AdminLTE 4 (Bootstrap 5.3)</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Auth Engine:</span>
                            <span class="fw-semibold">CodeIgniter Shield</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-secondary">Database Mode:</span>
                            <span class="fw-semibold">Dual-DB Compatible (SQLite &amp; MySQL)</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center pt-2">
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary btn-sm w-100">
                            เข้าสู่ระบบจัดการ Admin &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
