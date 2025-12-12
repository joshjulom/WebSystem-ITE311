<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-9">
            <h2 class="text-white mb-2">
                <i class="fas fa-bullhorn"></i> Announcements
            </h2>
            <p class="text-muted">Stay updated with the latest news and information</p>
        </div>
        <div class="col-md-3 text-end">
            <?php if (in_array(session('role'), ['admin', 'teacher'])): ?>
                <a href="<?= base_url('announcement/manage') ?>" class="btn btn-primary">
                    <i class="fas fa-cog"></i> Manage Announcements
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (empty($announcements)): ?>
        <div class="card bg-dark text-light border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-white">No Announcements Yet</h5>
                <p class="text-muted">There are currently no announcements to display. Check back later for updates.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($announcements as $announcement): ?>
                <div class="col-md-6 mb-4">
                    <?php
                    // Determine alert type based on priority
                    $alertType = 'info';
                    $icon = 'fa-info-circle';
                    if ($announcement['priority'] === 'urgent') {
                        $alertType = 'danger';
                        $icon = 'fa-exclamation-circle';
                    } elseif ($announcement['priority'] === 'high') {
                        $alertType = 'warning';
                        $icon = 'fa-exclamation-triangle';
                    }
                    ?>
                    <div class="card bg-dark text-light border-0 shadow-sm h-100">
                        <div class="card-header bg-<?= $alertType ?> bg-opacity-25 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas <?= $icon ?>"></i>
                                    <?= esc($announcement['title']) ?>
                                </h5>
                                <span class="badge bg-<?= $alertType ?>">
                                    <?= ucfirst($announcement['priority']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?= nl2br(esc($announcement['content'])) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-secondary">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i>
                                    <?= date('M d, Y h:i A', strtotime($announcement['created_at'])) ?>
                                </small>
                                <?php if (!empty($announcement['expires_at'])): ?>
                                    <small class="text-warning">
                                        <i class="fas fa-calendar-times"></i>
                                        Expires: <?= date('M d, Y', strtotime($announcement['expires_at'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($announcement['target_audience']) && $announcement['target_audience'] !== 'all'): ?>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-users"></i>
                                    For: <?= ucfirst($announcement['target_audience']) ?>s
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
