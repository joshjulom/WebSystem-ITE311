<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="text-white mb-2"><?= esc($course['title']) ?> - Assignments</h2>
            <p class="text-muted">Manage assignments for this course</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('assignment/create/' . $course['id']) ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Assignment
            </a>
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

    <div class="card bg-dark text-light border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-white mb-3">Assignments List</h5>
            
            <?php if (!empty($assignments)): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Due Date</th>
                                <th>Attachment</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><strong><?= esc($assignment['title']) ?></strong></td>
                                    <td><?= esc(substr($assignment['description'] ?? '', 0, 80)) ?><?= strlen($assignment['description'] ?? '') > 80 ? '...' : '' ?></td>
                                    <td>
                                        <?php if (!empty($assignment['due_date'])): ?>
                                            <span class="badge bg-warning text-dark">
                                                <?= date('M d, Y h:i A', strtotime($assignment['due_date'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No due date</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($assignment['file_attachment'])): ?>
                                            <a href="<?= base_url('assignment/download-assignment/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M d, Y', strtotime($assignment['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('assignment/view-submissions/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-list"></i> View Submissions
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-assignment-id="<?= $assignment['id'] ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No assignments created yet</p>
                    <a href="<?= base_url('assignment/create/' . $course['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Your First Assignment
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.delete-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
            return;
        }

        const assignmentId = $(this).data('assignment-id');
        const button = $(this);
        
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '<?= base_url('assignment/delete') ?>/' + assignmentId,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete assignment');
                    button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                }
            },
            error: function() {
                alert('An error occurred while deleting the assignment');
                button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

