<?php
/**
 * @var string|null $title
 * @var array<int, array{title: string, url?: string}>|null $breadcrumbs
 */
$title ??= lang('Settings.title');
?>
<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <!-- Left Navigation Rail -->
    <div class="col-md-4 col-lg-3">
        <div class="card shadow-sm mb-3">
            <div class="list-group list-group-flush nav nav-pills flex-column p-2" id="settings-nav" role="tablist">
                <button
                    class="list-group-item list-group-item-action active rounded-2 mb-1 border-0"
                    id="account-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#account-pane"
                    type="button"
                    role="tab"
                    aria-controls="account-pane"
                    aria-selected="true"
                >
                    <i class="bi bi-person me-2"></i><?= esc(lang('Settings.account')) ?>
                </button>
                <button
                    class="list-group-item list-group-item-action rounded-2 mb-1 border-0"
                    id="notifications-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#notifications-pane"
                    type="button"
                    role="tab"
                    aria-controls="notifications-pane"
                    aria-selected="false"
                >
                    <i class="bi bi-bell me-2"></i><?= esc(lang('Settings.notifications')) ?>
                </button>
                <button
                    class="list-group-item list-group-item-action rounded-2 mb-1 border-0"
                    id="security-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#security-pane"
                    type="button"
                    role="tab"
                    aria-controls="security-pane"
                    aria-selected="false"
                >
                    <i class="bi bi-shield-lock me-2"></i><?= esc(lang('Settings.security')) ?>
                </button>
                <button
                    class="list-group-item list-group-item-action rounded-2 border-0 text-danger"
                    id="danger-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#danger-pane"
                    type="button"
                    role="tab"
                    aria-controls="danger-pane"
                    aria-selected="false"
                >
                    <i class="bi bi-exclamation-triangle me-2"></i><?= esc(lang('Settings.dangerZone')) ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Right Settings Content -->
    <div class="col-md-8 col-lg-9">
        <div class="tab-content" id="settings-tabContent">
            <!-- Account Settings Tab -->
            <div class="tab-pane fade show active" id="account-pane" role="tabpanel" aria-labelledby="account-tab">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0"><?= esc(lang('Settings.account')) ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="<?= site_url('settings') ?>" class="row g-3">
                            <?= csrf_field() ?>
                            <div class="col-md-6">
                                <label class="form-label" for="settings-fullname"><?= esc(lang('Settings.fullName')) ?></label>
                                <input type="text" class="form-control" id="settings-fullname" value="Admin Homunculus">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="settings-email"><?= esc(lang('Settings.email')) ?></label>
                                <input type="email" class="form-control" id="settings-email" value="admin@homunculus.local">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="settings-tz"><?= esc(lang('Settings.timezone')) ?></label>
                                <select class="form-select" id="settings-tz">
                                    <option value="Asia/Bangkok" selected>Asia/Bangkok (UTC+07:00)</option>
                                    <option value="UTC">UTC (UTC+00:00)</option>
                                    <option value="America/New_York">America/New_York (UTC-05:00)</option>
                                    <option value="Europe/London">Europe/London (UTC+00:00)</option>
                                    <option value="Asia/Tokyo">Asia/Tokyo (UTC+09:00)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="settings-lang"><?= esc(lang('Settings.language')) ?></label>
                                <select class="form-select" id="settings-lang">
                                    <option value="th" <?= service('request')->getLocale() === 'th' ? 'selected' : '' ?>>ไทย (Thai)</option>
                                    <option value="en" <?= service('request')->getLocale() === 'en' ? 'selected' : '' ?>>English</option>
                                </select>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check2 me-1"></i><?= esc(lang('App.saveChanges')) ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Settings Tab -->
            <div class="tab-pane fade" id="notifications-pane" role="tabpanel" aria-labelledby="notifications-tab">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0"><?= esc(lang('Settings.notifications')) ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label fw-semibold" for="emailNotifications">
                                <?= esc(lang('Settings.emailNotifications')) ?>
                            </label>
                            <p class="text-body-secondary small mb-0"><?= esc(lang('Settings.emailNotificationsDesc')) ?></p>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="securityAlerts" checked>
                            <label class="form-check-label fw-semibold" for="securityAlerts">
                                <?= esc(lang('Settings.securityAlerts')) ?>
                            </label>
                            <p class="text-body-secondary small mb-0"><?= esc(lang('Settings.securityAlertsDesc')) ?></p>
                        </div>
                        <button type="button" class="btn btn-primary">
                            <i class="bi bi-check2 me-1"></i><?= esc(lang('App.saveChanges')) ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security Settings Tab -->
            <div class="tab-pane fade" id="security-pane" role="tabpanel" aria-labelledby="security-tab">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0"><?= esc(lang('Settings.security')) ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="<?= site_url('settings') ?>" class="row g-3">
                            <?= csrf_field() ?>
                            <div class="col-12">
                                <label class="form-label" for="currentPassword"><?= esc(lang('Settings.currentPassword')) ?></label>
                                <input type="password" class="form-control" id="currentPassword">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="newPassword"><?= esc(lang('Settings.newPassword')) ?></label>
                                <input type="password" class="form-control" id="newPassword">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="confirmPassword"><?= esc(lang('Settings.confirmPassword')) ?></label>
                                <input type="password" class="form-control" id="confirmPassword">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-shield-check me-1"></i><?= esc(lang('Settings.changePassword')) ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Settings Tab -->
            <div class="tab-pane fade" id="danger-pane" role="tabpanel" aria-labelledby="danger-tab">
                <div class="card border-danger shadow-sm mb-3">
                    <div class="card-header bg-danger-subtle text-danger-emphasis py-3">
                        <h5 class="card-title fw-bold mb-0"><?= esc(lang('Settings.dangerZone')) ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="fw-semibold text-danger"><?= esc(lang('Settings.deleteAccount')) ?></h6>
                        <p class="text-body-secondary small mb-3"><?= esc(lang('Settings.deleteAccountDesc')) ?></p>
                        <button type="button" class="btn btn-outline-danger">
                            <i class="bi bi-trash3 me-1"></i><?= esc(lang('Settings.deleteAccount')) ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
