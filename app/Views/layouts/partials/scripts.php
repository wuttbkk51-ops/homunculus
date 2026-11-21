<!-- Local Vendor Scripts -->
<script src="<?= base_url('assets/overlayscrollbars/overlayscrollbars.browser.es6.min.js') ?>"></script>
<script src="<?= base_url('assets/popper/popper.min.js') ?>"></script>
<script src="<?= base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/js/adminlte.min.js') ?>"></script>

<!-- OverlayScrollbars Configuration -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
    };
    const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
    const isMobile = window.innerWidth <= 992;

    if (
        sidebarWrapper &&
        globalThis.OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
        !isMobile
    ) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
            },
        });
    }
});
</script>
