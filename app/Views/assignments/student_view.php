<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-10">
            <h2 class="text-white mb-2"><?= esc($course['title']) ?> - Assignments</h2>
            <p class="text-muted">View and submit assignments for this course</p>
        </div>
        <div class="col-md-2 text-end">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
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

    <?php if (!empty($assignments)): ?>
        <div class="row">
            <?php foreach ($assignments as $assignment): ?>
                <div class="col-md-6 mb-4">
                    <div class="card bg-dark text-light border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title text-white mb-0"><?= esc($assignment['title']) ?></h5>
                                <?php if ($assignment['has_submitted']): ?>
                                    <?php if ($assignment['submission']['status'] === 'Graded'): ?>
                                        <span class="badge bg-success">Graded</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Submitted</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Not Submitted</span>
                                <?php endif; ?>
                            </div>

                            <p class="card-text text-muted mb-3"><?= esc($assignment['description']) ?></p>

                            <?php if (!empty($assignment['due_date'])): ?>
                                <p class="mb-2">
                                    <i class="fas fa-clock text-warning"></i>
                                    <strong>Due:</strong> 
                                    <?php
                                    $dueTime = strtotime($assignment['due_date']);
                                    $isPastDue = time() > $dueTime;
                                    ?>
                                    <span class="<?= $isPastDue ? 'text-danger' : 'text-warning' ?>">
                                        <?= date('M d, Y h:i A', $dueTime) ?>
                                        <?= $isPastDue ? ' (Past Due)' : '' ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($assignment['file_attachment'])): ?>
                                <p class="mb-2">
                                    <i class="fas fa-paperclip text-info"></i>
                                    <strong>Attachment:</strong>
                                    <a href="<?= base_url('assignment/download-assignment/' . $assignment['id']) ?>" class="text-info">
                                        Download File
                                    </a>
                                </p>
                            <?php endif; ?>

                            <p class="mb-3">
                                <i class="fas fa-user text-muted"></i>
                                <strong>Teacher:</strong> <?= esc($assignment['teacher_name'] ?? 'N/A') ?>
                            </p>

                            <hr class="border-secondary">

                            <?php if ($assignment['has_submitted']): ?>
                                <!-- Show submission details -->
                                <div class="bg-secondary p-3 rounded mb-3">
                                    <h6 class="text-white mb-2">Your Submission</h6>
                                    <p class="mb-1">
                                        <small class="text-muted">Submitted on: <?= date('M d, Y h:i A', strtotime($assignment['submission']['submission_date'])) ?></small>
                                    </p>
                                    
                                    <?php if (!empty($assignment['submission']['file_path'])): ?>
                                        <p class="mb-1">
                                            <small>
                                                <i class="fas fa-file"></i> 
                                                <a href="<?= base_url('assignment/download-submission/' . $assignment['submission']['id']) ?>" class="text-info">
                                                    Download Your Submission
                                                </a>
                                            </small>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($assignment['submission']['submission_text'])): ?>
                                        <p class="mb-1">
                                            <small><strong>Your notes:</strong> <?= esc(substr($assignment['submission']['submission_text'], 0, 100)) ?><?= strlen($assignment['submission']['submission_text']) > 100 ? '...' : '' ?></small>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($assignment['submission']['status'] === 'Graded'): ?>
                                        <hr class="border-light my-2">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-star"></i> Grade: <?= esc($assignment['submission']['grade']) ?>/100
                                        </h6>
                                        <?php if (!empty($assignment['submission']['feedback'])): ?>
                                            <div class="alert alert-info mb-0">
                                                <strong>Teacher Feedback:</strong><br>
                                                <?= nl2br(esc($assignment['submission']['feedback'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="mb-0"><small class="text-muted">Waiting for teacher to grade...</small></p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Show submit button -->
                                <?php
                                $canSubmit = true;
                                if (!empty($assignment['due_date'])) {
                                    $canSubmit = time() <= strtotime($assignment['due_date']);
                                }
                                ?>
                                <?php if ($canSubmit): ?>
                                    <a href="<?= base_url('assignment/submit-form/' . $assignment['id']) ?>" class="btn btn-primary w-100">
                                        <i class="fas fa-upload"></i> Submit Assignment
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-lock"></i> Submission Closed (Past Due)
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card bg-dark text-light border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                <p class="text-muted">No assignments available for this course yet</p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

