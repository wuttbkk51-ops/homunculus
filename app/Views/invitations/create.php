<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-envelope-plus me-2"></i><?= esc($title) ?>
    </h4>
    <a href="<?= site_url('invitations') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>กลับรายการ
    </h4>
</div>

<?php if (session('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
        <?php if (is_array(session('errors'))): ?>
            <ul class="mb-0 ps-3">
                <?php foreach (session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= esc(session('errors')) ?>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 560px;">
    <div class="card-body p-4">
        <form action="<?= site_url('invitations') ?>" method="post" id="inviteForm">
            <?= csrf_field() ?>

            <!-- อีเมลผู้รับ -->
            <div class="mb-3">
                <label for="email" class="form-label fw-medium"><?= esc(lang('Invite.labelEmail')) ?> <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?= esc(old('email')) ?>"
                        required
                        autocomplete="off"
                    >
                </div>
            </div>

            <!-- ประเภทคำเชิญ -->
            <div class="mb-3">
                <label class="form-label fw-medium"><?= esc(lang('Invite.labelInviteType')) ?> <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="invite_type" id="typeStaff"
                            value="existing_tenant"
                            <?= old('invite_type', 'existing_tenant') === 'existing_tenant' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="typeStaff">
                            <i class="bi bi-people me-1"></i><?= esc(lang('Invite.optionExistingTenant')) ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="invite_type" id="typeOwner"
                            value="new_tenant"
                            <?= old('invite_type') === 'new_tenant' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="typeOwner">
                            <i class="bi bi-shop me-1"></i><?= esc(lang('Invite.optionNewTenant')) ?>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Tenant (แสดงเมื่อ existing_tenant) -->
            <div class="mb-3" id="sectionTenantId">
                <label for="tenant_id" class="form-label fw-medium"><?= esc(lang('Invite.labelTenantId')) ?> <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input
                        type="number"
                        id="tenant_id"
                        name="tenant_id"
                        class="form-control"
                        placeholder="Tenant ID"
                        value="<?= esc(old('tenant_id')) ?>"
                    >
                </div>
                <div class="form-text text-body-secondary">ใส่ Tenant ID (ระบบจัดการ Tenant Picker จะเพิ่มในภายหลัง)</div>
            </div>

            <!-- Tenant Name/Slug (แสดงเมื่อ new_tenant, optional hybrid) -->
            <div class="mb-3 d-none" id="sectionNewTenant">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    กรอกชื่อร้าน/Slug ล่วงหน้าได้ ถ้าเว้นว่าง ผู้รับจะกรอกเองในฟอร์มสมัคร
                </div>
                <div class="mb-3">
                    <label for="tenant_name" class="form-label fw-medium"><?= esc(lang('Invite.labelTenantName')) ?></label>
                    <input type="text" id="tenant_name" name="tenant_name" class="form-control"
                        value="<?= esc(old('tenant_name')) ?>" placeholder="ชื่อร้านหรือบริษัท (optional)">
                </div>
                <div>
                    <label for="tenant_slug" class="form-label fw-medium"><?= esc(lang('Invite.labelTenantSlug')) ?></label>
                    <input type="text" id="tenant_slug" name="tenant_slug" class="form-control"
                        value="<?= esc(old('tenant_slug')) ?>" placeholder="slug-ภาษาอังกฤษ (optional)"
                        pattern="[a-z0-9\-_]*">
                    <div class="form-text">a-z, 0-9, -, _ เท่านั้น</div>
                </div>
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role_group" class="form-label fw-medium"><?= esc(lang('Invite.labelRole')) ?> <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                    <select id="role_group" name="role_group" class="form-select" required>
                        <option value="">— เลือก Role —</option>
                        <option value="admin"   <?= old('role_group') === 'admin'   ? 'selected' : '' ?>>admin</option>
                        <option value="manager" <?= old('role_group') === 'manager' ? 'selected' : '' ?>>manager</option>
                        <option value="staff"   <?= old('role_group') === 'staff'   ? 'selected' : '' ?>>staff</option>
                        <option value="member"  <?= old('role_group') === 'member'  ? 'selected' : '' ?>>member</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send me-1"></i><?= esc(lang('Invite.sendInvite')) ?>
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('contentScript') ?>
<script>
(function () {
    'use strict';

    const radios        = document.querySelectorAll('input[name="invite_type"]');
    const sectionTenant = document.getElementById('sectionTenantId');
    const sectionNew    = document.getElementById('sectionNewTenant');

    function toggle() {
        const val = document.querySelector('input[name="invite_type"]:checked')?.value;
        if (val === 'new_tenant') {
            sectionTenant.classList.add('d-none');
            sectionNew.classList.remove('d-none');
        } else {
            sectionTenant.classList.remove('d-none');
            sectionNew.classList.add('d-none');
        }
    }

    radios.forEach(r => r.addEventListener('change', toggle));
    toggle(); // initial state
})();
</script>
<?= $this->endSection() ?>
