<footer class="app-footer text-body-secondary py-3">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <strong><?= esc(lang('App.appName')) ?></strong> &copy; <?= date('Y') ?> <?= esc(lang('App.copyright')) ?>
        </div>
        <div class="small">
            <span class="badge bg-secondary-subtle text-secondary-emphasis">AdminLTE v4</span>
            <span class="badge bg-primary-subtle text-primary-emphasis ms-1">CI v<?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
        </div>
    </div>
</footer>
