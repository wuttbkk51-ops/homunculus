<?php
/**
 * @var string|null $title
 * @var array<int, array{title: string, url?: string}>|null $breadcrumbs
 */
$title ??= lang('Profile.title');
$user = function_exists('auth') && auth()->loggedIn() ? auth()->user() : null;
$username = $user ? ($user->username ?? $user->email ?? lang('App.user')) : 'Admin User';
$userEmail = $user ? ($user->email ?? 'admin@example.com') : 'admin@example.com';
?>
<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <!-- Profile Sidebar (Left) -->
    <div class="col-md-4 col-lg-3">
        <!-- User Summary Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center p-4">
                <div
                    class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm"
                    style="width: 88px; height: 88px; font-size: 2rem; font-weight: 600;"
                    aria-hidden="true"
                >
                    <?= esc(mb_strtoupper(mb_substr($username, 0, 2))) ?>
                </div>
                <h4 class="card-title fw-bold mb-1"><?= esc($username) ?></h4>
                <p class="text-body-secondary small mb-3"><?= esc($userEmail) ?></p>

                <ul class="list-group list-group-flush text-start small border-top pt-2">
                    <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                        <span class="text-body-secondary"><?= esc(lang('Profile.followers')) ?></span>
                        <span class="fw-semibold">1,322</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                        <span class="text-body-secondary"><?= esc(lang('Profile.following')) ?></span>
                        <span class="fw-semibold">543</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                        <span class="text-body-secondary"><?= esc(lang('Profile.friends')) ?></span>
                        <span class="fw-semibold">128</span>
                    </li>
                </ul>

                <a href="#settings-pane" data-bs-toggle="tab" class="btn btn-outline-primary btn-sm w-100 mt-3">
                    <i class="bi bi-pencil me-1"></i><?= esc(lang('Profile.editProfile')) ?>
                </a>
            </div>
        </div>

        <!-- About Me Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="card-title fw-semibold mb-0"><?= esc(lang('Profile.aboutMe')) ?></h5>
            </div>
            <div class="card-body small">
                <p class="fw-semibold mb-1 text-primary">
                    <i class="bi bi-mortarboard me-1"></i><?= esc(lang('Profile.education')) ?>
                </p>
                <p class="text-body-secondary mb-3">B.S. in Computer Engineering &amp; Information Systems</p>

                <p class="fw-semibold mb-1 text-primary">
                    <i class="bi bi-geo-alt me-1"></i><?= esc(lang('Profile.location')) ?>
                </p>
                <p class="text-body-secondary mb-3">Bangkok, Thailand</p>

                <p class="fw-semibold mb-1 text-primary">
                    <i class="bi bi-tags me-1"></i><?= esc(lang('Profile.skills')) ?>
                </p>
                <div class="mb-3 d-flex flex-wrap gap-1">
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">PHP 8.3</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">CodeIgniter 4</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Bootstrap 5</span>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">AdminLTE</span>
                </div>

                <p class="fw-semibold mb-1 text-primary">
                    <i class="bi bi-file-text me-1"></i><?= esc(lang('Profile.notes')) ?>
                </p>
                <p class="text-body-secondary mb-0">Senior Architect &amp; System Lead Developer</p>
            </div>
        </div>
    </div>

    <!-- Profile Details & Tabs (Right) -->
    <div class="col-md-8 col-lg-9">
        <div class="card shadow-sm">
            <div class="card-header p-2 bg-body-tertiary border-bottom-0">
                <ul class="nav nav-pills" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-pane" type="button" role="tab" aria-controls="activity-pane" aria-selected="true">
                            <i class="bi bi-activity me-1"></i><?= esc(lang('Profile.activity')) ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline-pane" type="button" role="tab" aria-controls="timeline-pane" aria-selected="false">
                            <i class="bi bi-clock-history me-1"></i><?= esc(lang('Profile.timeline')) ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-pane" type="button" role="tab" aria-controls="settings-pane" aria-selected="false">
                            <i class="bi bi-gear me-1"></i><?= esc(lang('Profile.editProfile')) ?>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="profileTabContent">
                    <!-- Activity Tab -->
                    <div class="tab-pane fade show active" id="activity-pane" role="tabpanel" aria-labelledby="activity-tab">
                        <div class="post pb-3 mb-3 border-bottom">
                            <div class="user-block d-flex align-items-center mb-2">
                                <span class="badge rounded-pill bg-primary me-2"><i class="bi bi-check-lg"></i></span>
                                <span class="fw-semibold me-2"><?= esc($username) ?></span>
                                <span class="text-body-secondary small">shared a new release - 3 days ago</span>
                            </div>
                            <p class="text-body-secondary mb-0">
                                Deployed core UI layout and AdminLTE components integration successfully with 100% test coverage.
                            </p>
                        </div>
                        <div class="post pb-3 mb-3 border-bottom">
                            <div class="user-block d-flex align-items-center mb-2">
                                <span class="badge rounded-pill bg-success me-2"><i class="bi bi-git"></i></span>
                                <span class="fw-semibold me-2"><?= esc($username) ?></span>
                                <span class="text-body-secondary small">committed code - 5 days ago</span>
                            </div>
                            <p class="text-body-secondary mb-0">
                                Refactored authentication filter and tenant multi-tenancy verification services.
                            </p>
                        </div>
                    </div>

                    <!-- Timeline Tab -->
                    <div class="tab-pane fade" id="timeline-pane" role="tabpanel" aria-labelledby="timeline-tab">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 py-3">
                                <span class="badge bg-info-subtle text-info-emphasis me-2">Sprint 1</span>
                                <strong>Repository Setup:</strong> Initialized CodeIgniter 4 repository with strict multi-tenancy rules.
                            </li>
                            <li class="list-group-item px-0 py-3">
                                <span class="badge bg-success-subtle text-success-emphasis me-2">Sprint 2</span>
                                <strong>Auth &amp; Shield:</strong> Configured CodeIgniter Shield auth integration and tenant session.
                            </li>
                            <li class="list-group-item px-0 py-3">
                                <span class="badge bg-primary-subtle text-primary-emphasis me-2">Sprint 3</span>
                                <strong>AdminLTE 4 UI:</strong> Implemented responsive layouts, partials, and localized views.
                            </li>
                        </ul>
                    </div>

                    <!-- Edit Profile Tab -->
                    <div class="tab-pane fade" id="settings-pane" role="tabpanel" aria-labelledby="settings-tab">
                        <form method="post" action="<?= site_url('profile') ?>">
                            <?= csrf_field() ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="profileUsername" class="form-label"><?= esc(lang('Auth.username')) ?></label>
                                    <input type="text" class="form-control" id="profileUsername" value="<?= esc($username) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="profileEmail" class="form-label"><?= esc(lang('Auth.email')) ?></label>
                                    <input type="email" class="form-control" id="profileEmail" value="<?= esc($userEmail) ?>">
                                </div>
                                <div class="col-12">
                                    <label for="profileEducation" class="form-label"><?= esc(lang('Profile.education')) ?></label>
                                    <input type="text" class="form-control" id="profileEducation" value="B.S. in Computer Engineering">
                                </div>
                                <div class="col-12">
                                    <label for="profileLocation" class="form-label"><?= esc(lang('Profile.location')) ?></label>
                                    <input type="text" class="form-control" id="profileLocation" value="Bangkok, Thailand">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check2 me-1"></i><?= esc(lang('App.saveChanges')) ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
