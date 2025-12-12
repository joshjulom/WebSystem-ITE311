<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-9">
            <h2 class="text-white mb-2">Manage Announcements</h2>
            <?php if (session('role') === 'admin'): ?>
                <p class="text-muted">Create and manage system-wide announcements</p>
            <?php else: ?>
                <p class="text-muted">Create and manage your announcements for students</p>
            <?php endif; ?>
        </div>
        <div class="col-md-3 text-end">
            <a href="<?= base_url('announcement/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Announcement
            </a>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
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
            <?php if (!empty($announcements)): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width: 20%;">Title</th>
                                <th style="width: 30%;">Content</th>
                                <th style="width: 10%;">Audience</th>
                                <th style="width: 8%;">Priority</th>
                                <th style="width: 10%;">Created By</th>
                                <th style="width: 12%;">Created</th>
                                <th style="width: 5%;">Status</th>
                                <th style="width: 5%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $announcement): ?>
                                <tr>
                                    <td><strong><?= esc($announcement['title']) ?></strong></td>
                                    <td><?= esc(substr($announcement['content'], 0, 100)) ?><?= strlen($announcement['content']) > 100 ? '...' : '' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $announcement['target_audience'] === 'all' ? 'primary' : 'info' ?>">
                                            <?= esc(ucfirst($announcement['target_audience'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $priorityColors = [
                                            'low' => 'secondary',
                                            'normal' => 'info',
                                            'high' => 'warning',
                                            'urgent' => 'danger'
                                        ];
                                        $color = $priorityColors[$announcement['priority']] ?? 'info';
                                        ?>
                                        <span class="badge bg-<?= $color ?>">
                                            <?= esc(ucfirst($announcement['priority'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($announcement['creator_name'] ?? 'System') ?></td>
                                    <td><small><?= date('M d, Y', strtotime($announcement['created_at'])) ?></small></td>
                                    <td>
                                        <button class="btn btn-sm toggle-status-btn <?= $announcement['is_active'] ? 'btn-success' : 'btn-secondary' ?>" 
                                                data-id="<?= $announcement['id'] ?>">
                                            <?= $announcement['is_active'] ? 'Active' : 'Inactive' ?>
                                        </button>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('announcement/edit/' . $announcement['id']) ?>" class="btn btn-sm btn-outline-light">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="<?= $announcement['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bullhorn fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No announcements yet</p>
                    <a href="<?= base_url('announcement/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create First Announcement
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle status
    $('.toggle-status-btn').on('click', function() {
        const id = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: '<?= base_url('announcement/toggle-status') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to update status');
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    });

    // Delete announcement
    $('.delete-btn').on('click', function() {
        if (!confirm('Are you sure you want to delete this announcement?')) {
            return;
        }

        const id = $(this).data('id');

        $.ajax({
            url: '<?= base_url('announcement/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete announcement');
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

