<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Announcements</h2>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">
                    <h5>No announcements yet</h5>
                    <p>There are currently no announcements to display. Check back later for updates.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= esc($announcement['title']) ?></h5>
                                    <p class="card-text"><?= nl2br(esc($announcement['content'])) ?></p>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>
                                        <i class="fas fa-calendar-alt"></i>
                                        Posted on <?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
