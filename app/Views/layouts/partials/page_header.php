<?php
$currentPageTitle     = $this->renderSection('page_title') ?: ($title ?? '');
$hasBreadcrumbSection = (bool) $this->renderSection('breadcrumb');
$breadcrumbList       = $breadcrumbs ?? [];
?>
<?php if ($currentPageTitle !== '' || $hasBreadcrumbSection || ! empty($breadcrumbList)): ?>
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <?php if ($currentPageTitle !== ''): ?>
                    <h1 class="mb-0 fs-3 fw-semibold"><?= esc($currentPageTitle) ?></h1>
                <?php endif; ?>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-end mb-0">
                        <?php if ($hasBreadcrumbSection): ?>
                            <?= $this->renderSection('breadcrumb') ?>
                        <?php elseif (! empty($breadcrumbList)): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= site_url('/') ?>"><?= esc(lang('App.home')) ?></a>
                            </li>
                            <?php foreach ($breadcrumbList as $item): ?>
                                <?php if (! empty($item['url'])): ?>
                                    <li class="breadcrumb-item"><a href="<?= esc($item['url']) ?>"><?= esc($item['title'] ?? '') ?></a></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active" aria-current="page"><?= esc($item['title'] ?? '') ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php elseif ($currentPageTitle !== ''): ?>
                            <li class="breadcrumb-item"><a href="<?= site_url('/') ?>"><?= esc(lang('App.home')) ?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= esc($currentPageTitle) ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
