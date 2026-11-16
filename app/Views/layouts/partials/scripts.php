<!-- Core Scripts (Local Assets via composer-copy-file) -->
<script src="<?= base_url('assets/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/overlayscrollbars/overlayscrollbars.browser.es6.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>

<!-- Block สำหรับให้แต่ละ View แทรก Custom JavaScript เฉพาะหน้า -->
<?= $this->renderSection('scripts') ?>
