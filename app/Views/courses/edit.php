<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Edit Course</h2>

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
                <div class="card-body">
                    <form id="editCourseForm">
                        <input type="hidden" name="course_id" value="<?= esc($course['id']) ?>">

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= esc($course['title']) ?>" required>
                                <div class="invalid-feedback">
                                    Please provide a course title.
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="course_code" class="form-label">Course Code</label>
                                <input type="text" class="form-control" id="course_code" name="course_code" value="<?= esc($course['course_code'] ?? '') ?>" placeholder="e.g., ITE101">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Course Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= esc($course['description']) ?></textarea>
                            <div class="invalid-feedback">
                                Please provide a course description.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="school_year" class="form-label">School Year</label>
                                <input type="text" class="form-control" id="school_year" name="school_year" value="<?= esc($course['school_year'] ?? '') ?>" placeholder="e.g., 2024-2025">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="semester" class="form-label">Semester</label>
                                <select class="form-select" id="semester" name="semester">
                                    <option value="">Select Semester</option>
                                    <option value="1st Semester" <?= (isset($course['semester']) && $course['semester'] == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
                                    <option value="2nd Semester" <?= (isset($course['semester']) && $course['semester'] == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
                                    <option value="Summer" <?= (isset($course['semester']) && $course['semester'] == 'Summer') ? 'selected' : '' ?>>Summer</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Active" <?= (isset($course['status']) && $course['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= (isset($course['status']) && $course['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="schedule" class="form-label">Schedule</label>
                            <input type="text" class="form-control" id="schedule" name="schedule" value="<?= esc($course['schedule'] ?? '') ?>" placeholder="e.g., MWF 10:00 AM - 11:30 AM">
                            <small class="text-muted">Enter the class schedule (days and time)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($course['start_date'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($course['end_date'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= base_url('/course') ?>" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous validation
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();

        var formData = $(this).serialize();

        $.ajax({
            url: '<?= site_url('course/update/' . $course['id']) ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('/course') ?>';
                } else {
                    alert(response.message);
                    if (response.errors) {
                        // Show validation errors
                        for (var field in response.errors) {
                            $('#' + field).addClass('is-invalid');
                            $('#' + field).next('.invalid-feedback').text(response.errors[field]).show();
                        }
                    }
                }
            },
            error: function() {
                alert('Error occurred while updating course.');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
