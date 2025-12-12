<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-10">
            <h2 class="text-white mb-2"><?= esc($assignment['title']) ?></h2>
            <p class="text-muted mb-1">Course: <?= esc($assignment['course_title']) ?></p>
            <p class="text-muted mb-1">Description: <?= esc($assignment['description']) ?></p>
            <?php if (!empty($assignment['due_date'])): ?>
                <p class="text-muted mb-0">Due Date: <?= date('M d, Y h:i A', strtotime($assignment['due_date'])) ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-2 text-end">
            <a href="<?= base_url('assignment/teacher-view/' . $assignment['course_id']) ?>" class="btn btn-secondary">
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

    <div class="card bg-dark text-light border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-white mb-3">Student Submissions</h5>
            
            <?php if (!empty($submissions)): ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Submission Date</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><strong><?= esc($submission['student_name']) ?></strong></td>
                                    <td><?= esc($submission['student_email']) ?></td>
                                    <td>
                                        <?php if (!empty($submission['submission_date'])): ?>
                                            <small><?= date('M d, Y h:i A', strtotime($submission['submission_date'])) ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($submission['file_path'])): ?>
                                            <a href="<?= base_url('assignment/download-submission/' . $submission['submission_id']) ?>" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        <?php else: ?>
                                            <?php if (!empty($submission['submission_id'])): ?>
                                                <span class="text-muted">No file</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (empty($submission['submission_id'])): ?>
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        <?php elseif ($submission['status'] === 'Graded'): ?>
                                            <span class="badge bg-success">Graded</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Submitted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($submission['grade'])): ?>
                                            <strong class="text-success"><?= esc($submission['grade']) ?>/100</strong>
                                        <?php else: ?>
                                            <span class="text-muted">Not graded</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($submission['submission_id'])): ?>
                                            <button class="btn btn-sm btn-primary grade-btn" 
                                                    data-submission-id="<?= $submission['submission_id'] ?>"
                                                    data-student-name="<?= esc($submission['student_name']) ?>"
                                                    data-grade="<?= esc($submission['grade'] ?? '') ?>"
                                                    data-feedback="<?= esc($submission['feedback'] ?? '') ?>"
                                                    data-submission-text="<?= esc($submission['submission_text'] ?? '') ?>">
                                                <i class="fas fa-star"></i> <?= !empty($submission['grade']) ? 'Edit Grade' : 'Grade' ?>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No students enrolled in this course</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Grading Modal -->
<div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="gradeModalLabel">Grade Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="gradeForm">
                    <input type="hidden" id="submissionId">
                    
                    <div class="mb-3">
                        <label class="form-label">Student:</label>
                        <p id="studentName" class="text-white"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Submission Text:</label>
                        <div id="submissionText" class="p-3 bg-secondary rounded" style="min-height: 60px;">
                            <em class="text-muted">No text submission</em>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade (0-100) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="grade" name="grade" min="0" max="100" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback (Optional)</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitGradeBtn">Submit Grade</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Open grading modal
    $('.grade-btn').on('click', function() {
        const submissionId = $(this).data('submission-id');
        const studentName = $(this).data('student-name');
        const grade = $(this).data('grade');
        const feedback = $(this).data('feedback');
        const submissionText = $(this).data('submission-text');

        $('#submissionId').val(submissionId);
        $('#studentName').text(studentName);
        $('#grade').val(grade);
        $('#feedback').val(feedback);
        
        if (submissionText && submissionText.trim() !== '') {
            $('#submissionText').html('<p class="mb-0">' + submissionText + '</p>');
        } else {
            $('#submissionText').html('<em class="text-muted">No text submission</em>');
        }

        $('#gradeModal').modal('show');
    });

    // Submit grade
    $('#submitGradeBtn').on('click', function() {
        const submissionId = $('#submissionId').val();
        const grade = $('#grade').val();
        const feedback = $('#feedback').val();

        if (!grade || grade < 0 || grade > 100) {
            alert('Please enter a valid grade between 0 and 100');
            return;
        }

        const button = $(this);
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        $.ajax({
            url: '<?= base_url('assignment/grade') ?>',
            method: 'POST',
            data: {
                submission_id: submissionId,
                grade: grade,
                feedback: feedback
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Failed to grade submission');
                    button.prop('disabled', false).html('Submit Grade');
                }
            },
            error: function() {
                alert('An error occurred while grading the submission');
                button.prop('disabled', false).html('Submit Grade');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

