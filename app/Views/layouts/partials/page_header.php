<?php
/**
 * Partial สำหรับแสดงผล App Content Header (Page Header, Breadcrumbs & Actions)
 * ทำงานร่วมกับ navbar_helper เพื่ออำนวยความสะดวกให้ Developer (Zero Boilerplate)
 */

$customHeader = $this->renderSection('pageHeader');
$customBreadcrumb = $this->renderSection('breadcrumb');
$customActions = $this->renderSection('pageActions');

$title = $pageTitle ?? ($title ?? null);
$subtitle = $pageSubtitle ?? ($subtitle ?? null);
$icon = $pageIcon ?? ($icon ?? null);
$breadcrumbsList = $breadcrumbs ?? null;
?>
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center gy-2">
            <!-- ฝั่งซ้าย: Page Title & Icon -->
            <div class="col-sm-6">
                <?php if ($customHeader): ?>
                    <?= $customHeader ?>
                <?php elseif ($title): ?>
                    <?= pageTitle($title, $subtitle ?? '', $icon ?? '') ?>
                <?php else: ?>
                    <h3 class="mb-0 fw-bold fs-4">ระบบจัดการ</h3>
                <?php endif; ?>
            </div>

            <!-- ฝั่งขวา: Breadcrumbs & Page Action Buttons -->
            <div class="col-sm-6 text-sm-end d-flex flex-column align-items-sm-end gap-1">
                <?php if ($customBreadcrumb): ?>
                    <?= $customBreadcrumb ?>
                <?php elseif (! empty($breadcrumbsList) && is_array($breadcrumbsList)): ?>
                    <?= breadcrumb($breadcrumbsList) ?>
                <?php endif; ?>

                <?php if ($customActions): ?>
                    <div class="d-flex align-items-center gap-2 justify-content-sm-end flex-wrap mt-1">
                        <?= $customActions ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
