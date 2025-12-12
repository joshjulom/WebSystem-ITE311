<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-white mb-2">Create Announcement</h2>
            <p class="text-muted">Create a new announcement for your target audience</p>
        </div>
    </div>

    <div class="card bg-dark text-light border-0 shadow-sm">
        <div class="card-body">
            <form id="createAnnouncementForm">
                <div class="mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="target_audience" class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select class="form-select" id="target_audience" name="target_audience" required>
                            <?php if (session('role') === 'admin'): ?>
                                <option value="all">All Users</option>
                                <option value="admin">Admins Only</option>
                                <option value="teacher">Teachers Only</option>
                                <option value="student">Students Only</option>
                            <?php else: ?>
                                <!-- Teachers can only target students or all -->
                                <option value="student">Students Only</option>
                                <option value="all">All Users</option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted">Who should see this announcement?</small>
                    </div>

                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <small class="form-text text-muted">Higher priority appears first</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                    <input type="datetime-local" class="form-control" id="expires_at" name="expires_at">
                    <small class="form-text text-muted">Leave blank for no expiration</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('announcement/manage') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Create Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#createAnnouncementForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            title: $('#title').val(),
            content: $('#content').val(),
            target_audience: $('#target_audience').val(),
            priority: $('#priority').val(),
            expires_at: $('#expires_at').val()
        };

        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

        $.ajax({
            url: '<?= base_url('announcement/store') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('announcement/manage') ?>';
                } else {
                    let errorMsg = response.message || 'Failed to create announcement';
                    if (response.errors) {
                        errorMsg += '\n' + Object.values(response.errors).join('\n');
                    }
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Announcement');
                }
            },
            error: function() {
                alert('An error occurred while creating the announcement');
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Announcement');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

