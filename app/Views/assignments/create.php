<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-white mb-2">Create New Assignment</h2>
            <p class="text-muted">Course: <?= esc($course['title']) ?></p>
        </div>
    </div>

    <div class="card bg-dark text-light border-0 shadow-sm">
        <div class="card-body">
            <form id="createAssignmentForm" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= esc($course['id']) ?>">
                
                <div class="mb-3">
                    <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Instructions / Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date (Optional)</label>
                    <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                    <small class="form-text text-muted">Leave blank if no due date</small>
                </div>

                <div class="mb-3">
                    <label for="assignment_file" class="form-label">Attachment (Optional)</label>
                    <input type="file" class="form-control" id="assignment_file" name="assignment_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip">
                    <small class="form-text text-muted">Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP (Max 10MB)</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('assignment/teacher-view/' . $course['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#createAssignmentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $('#submitBtn');
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

        $.ajax({
            url: '<?= base_url('assignment/store') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('assignment/teacher-view/' . $course['id']) ?>';
                } else {
                    let errorMsg = response.message || 'Failed to create assignment';
                    if (response.errors) {
                        errorMsg += '\n' + Object.values(response.errors).join('\n');
                    }
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Assignment');
                }
            },
            error: function() {
                alert('An error occurred while creating the assignment');
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Assignment');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

