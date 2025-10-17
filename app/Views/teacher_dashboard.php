<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Teacher Dashboard</h2>
            
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
            
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title">Welcome, Teacher!</h3>
                    <p class="card-text">You are logged in as: <strong><?= esc($user_name) ?></strong></p>
                    <p class="card-text">Email: <strong><?= esc($user_email) ?></strong></p>
                    <p class="card-text">Role: <strong><?= esc($role) ?></strong></p>
                    
                    <!-- Quick Actions -->
                    <div class="mt-4">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="<?= base_url('/announcements') ?>" class="btn btn-primary">
                                <i class="fas fa-bullhorn"></i> View Announcements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
