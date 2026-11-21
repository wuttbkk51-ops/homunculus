<?php
/**
 * @var string|null $title
 * @var array<int, array{title: string, url?: string}>|null $breadcrumbs
 */
$title ??= lang('App.starterPage');
?>
<?= $this->extend('layouts/master') ?>

<?= $this->section('contentStyles') ?>
<style>
    .starter-page-badge {
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- Starter Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= esc(lang('App.starterCard')) ?></h3>

                <div class="card-tools">
                    <button
                        type="button"
                        class="btn btn-tool"
                        data-lte-toggle="card-collapse"
                        aria-label="Collapse card"
                    >
                        <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                        <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-tool"
                        data-lte-toggle="card-remove"
                        aria-label="Remove card"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?= esc(lang('App.starterText')) ?>
            </div>
            <div class="card-footer text-secondary">
                <?= esc(lang('App.cardFooter')) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('contentScript') ?>
<script>
    // Page specific script for starter page
</script>
<?= $this->endSection() ?>
