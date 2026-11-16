<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>บัญชีองค์กรถูกระงับการใช้งาน - Homunculus<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="login-box" style="width: 100%; max-width: 440px;">
    <div class="login-logo mb-4 text-center">
        <a href="<?= site_url('/') ?>" class="link-dark text-decoration-none fs-2 fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-cpu-fill text-danger fs-1"></i>
            <span><b>Homunculus</b></span>
        </a>
    </div>

    <div class="card card-outline card-danger shadow-sm border-0">
        <div class="card-body text-center p-4">
            <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                <i class="bi bi-exclamation-octagon-fill fs-2"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">บัญชีองค์กรถูกระงับการใช้งาน</h4>
            <p class="text-secondary small mb-4">
                บัญชีองค์กร (Tenant) ของท่านไม่พร้อมให้บริการในขณะนี้ เนื่องจากสถานะถูกระงับหรือพ้นกำหนดระยะเวลาการใช้งาน กรุณาติดต่อผู้ดูแลระบบเพื่อต่ออายุหรือขอรับความช่วยเหลือ
            </p>
            <div class="d-grid gap-2">
                <a href="<?= site_url('login') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-in-right me-1"></i>กลับสู่หน้าเข้าสู่ระบบ
                </a>
                <a href="<?= site_url('/') ?>" class="btn btn-link text-decoration-none small text-secondary">
                    กลับสู่หน้าแรก
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
