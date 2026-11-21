<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-envelope-paper me-2"></i><?= esc($title) ?>
    </h4>
    <a href="<?= site_url('invitations/create') ?>" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i><?= esc(lang('Invite.sendInvite')) ?>
    </a>
</div>

<?php if (session('message')): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
        <?= esc(session('message')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
        <?= esc(session('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>อีเมล</th>
                        <th>Role</th>
                        <th>Tenant</th>
                        <th>สถานะ</th>
                        <th>หมดอายุ</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invitations)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-4">
                                <i class="bi bi-inbox me-1"></i> ยังไม่มีคำเชิญ
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invitations as $inv): ?>
                            <tr>
                                <td class="fw-medium"><?= esc($inv['email']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= esc($inv['role_group']) ?></span>
                                </td>
                                <td class="text-body-secondary small">
                                    <?php if ($inv['tenant_id'] !== null): ?>
                                        <?= esc($inv['tenant_display_name'] ?? '—') ?>
                                    <?php elseif (! empty($inv['tenant_name'])): ?>
                                        <i class="bi bi-plus-circle me-1 text-success"></i><?= esc($inv['tenant_name']) ?>
                                    <?php else: ?>
                                        <i class="bi bi-plus-circle me-1 text-success"></i><em>ให้ผู้รับกรอก</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($inv['status'] === 'accepted'): ?>
                                        <span class="badge bg-success"><?= esc(lang('Invite.statusAccepted')) ?></span>
                                    <?php elseif ($inv['status'] === 'expired'): ?>
                                        <span class="badge bg-danger"><?= esc(lang('Invite.statusExpired')) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><?= esc(lang('Invite.statusPending')) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-body-secondary">
                                    <?= $inv['expires_at'] !== null ? esc(date('d/m/Y H:i', strtotime($inv['expires_at']))) : '—' ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($inv['status'] === 'pending'): ?>
                                        <form action="<?= site_url("invitations/{$inv['id']}/revoke") ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('ยืนยันการยกเลิกคำเชิญ?')"
                                            >
                                                <i class="bi bi-x-lg me-1"></i><?= esc(lang('Invite.revokeInvite')) ?>
                                            </button>
                                        </form>
                                        <form action="<?= site_url("invitations/{$inv['id']}/resend") ?>" method="post" class="d-inline ms-1">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-send me-1"></i><?= esc(lang('Invite.resendInvite')) ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
