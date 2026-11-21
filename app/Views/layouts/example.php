<?php
/**
 * Example Template for Master Layout
 *
 * ไฟล์ตัวอย่างการใช้งาน Master Layout (layouts/master)
 * ใช้เป็นแนวทางเริ่มต้นสำหรับการสร้างหน้าจอใหม่ในระบบ
 *
 * @var string|null $title
 * @var array<int, array{title: string, url?: string}>|null $breadcrumbs
 */
$title ??= 'Example Page';
?>
<?= $this->extend('layouts/master') ?>

<?= $this->section('contentStyles') ?>
<style>
    .example-page-badge {
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- Example Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title d-flex align-items-center gap-2">
                    <span>Example Card</span>
                    <span class="badge text-bg-primary example-page-badge">Example</span>
                </h3>

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
                Start creating your amazing application!
            </div>
            <div class="card-footer text-secondary">
                The footer of the card
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('contentScript') ?>
<script>
    // Page specific script for example page
</script>
<?= $this->endSection() ?>
