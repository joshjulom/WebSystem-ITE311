<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-white mb-2">Submit Assignment</h2>
            <p class="text-muted">Course: <?= esc($assignment['course_title']) ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card bg-dark text-light border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-white mb-3"><?= esc($assignment['title']) ?></h5>
                    
                    <div class="mb-3">
                        <strong>Instructions:</strong>
                        <p class="text-muted mt-2"><?= nl2br(esc($assignment['description'])) ?></p>
                    </div>

                    <?php if (!empty($assignment['due_date'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i>
                            <strong>Due Date:</strong> <?= date('M d, Y h:i A', strtotime($assignment['due_date'])) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($assignment['file_attachment'])): ?>
                        <div class="mb-3">
                            <i class="fas fa-paperclip text-info"></i>
                            <strong>Assignment Attachment:</strong>
                            <a href="<?= base_url('assignment/download-assignment/' . $assignment['id']) ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-download"></i> Download File
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($existingSubmission): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>You have already submitted this assignment.</strong>
                    <p class="mb-0">Submitted on: <?= date('M d, Y h:i A', strtotime($existingSubmission['submission_date'])) ?></p>
                    <a href="<?= base_url('assignment/student-view/' . $assignment['course_id']) ?>" class="btn btn-sm btn-primary mt-2">
                        Back to Assignments
                    </a>
                </div>
            <?php else: ?>
                <div class="card bg-dark text-light border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-white mb-3">Your Submission</h5>
                        
                        <form id="submitAssignmentForm" enctype="multipart/form-data">
                            <input type="hidden" name="assignment_id" value="<?= esc($assignment['id']) ?>">
                            
                            <div class="mb-3">
                                <label for="submission_file" class="form-label">Upload File</label>
                                <input type="file" class="form-control" id="submission_file" name="submission_file" 
                                       accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">
                                    Allowed formats: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP, JPG, PNG (Max 10MB)
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="submission_text" class="form-label">Additional Notes (Optional)</label>
                                <textarea class="form-control" id="submission_text" name="submission_text" rows="5" 
                                          placeholder="Add any notes or comments about your submission..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('assignment/student-view/' . $assignment['course_id']) ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-upload"></i> Submit Assignment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card bg-dark text-light border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-white mb-3">
                        <i class="fas fa-info-circle"></i> Submission Guidelines
                    </h5>
                    
                    <ul class="text-muted">
                        <li class="mb-2">Make sure your file is in the correct format</li>
                        <li class="mb-2">Check your file before submitting</li>
                        <li class="mb-2">You can only submit once</li>
                        <li class="mb-2">Late submissions may not be accepted</li>
                        <li class="mb-2">Add notes if you need to explain anything</li>
                    </ul>

                    <?php if (!empty($assignment['due_date'])): ?>
                        <?php
                        $dueTime = strtotime($assignment['due_date']);
                        $timeLeft = $dueTime - time();
                        $isPastDue = $timeLeft < 0;
                        ?>
                        
                        <div class="alert <?= $isPastDue ? 'alert-danger' : 'alert-warning' ?> mt-3">
                            <?php if ($isPastDue): ?>
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Past Due!</strong>
                                <p class="mb-0">This assignment is past its due date.</p>
                            <?php else: ?>
                                <i class="fas fa-clock"></i>
                                <strong>Time Remaining:</strong>
                                <p class="mb-0">
                                    <?php
                                    $days = floor($timeLeft / 86400);
                                    $hours = floor(($timeLeft % 86400) / 3600);
                                    $minutes = floor(($timeLeft % 3600) / 60);
                                    
                                    if ($days > 0) {
                                        echo "$days day(s), $hours hour(s)";
                                    } elseif ($hours > 0) {
                                        echo "$hours hour(s), $minutes minute(s)";
                                    } else {
                                        echo "$minutes minute(s)";
                                    }
                                    ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#submitAssignmentForm').on('submit', function(e) {
        e.preventDefault();

        const file = $('#submission_file')[0].files[0];
        const text = $('#submission_text').val();

        if (!file && !text.trim()) {
            alert('Please upload a file or add submission notes');
            return;
        }

        if (!confirm('Are you sure you want to submit this assignment? You cannot resubmit after submission.')) {
            return;
        }

        const formData = new FormData(this);
        const submitBtn = $('#submitBtn');
        
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        $.ajax({
            url: '<?= base_url('assignment/submit') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('assignment/student-view/' . $assignment['course_id']) ?>';
                } else {
                    let errorMsg = response.message || 'Failed to submit assignment';
                    if (response.errors) {
                        errorMsg += '\n' + Object.values(response.errors).join('\n');
                    }
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> Submit Assignment');
                }
            },
            error: function() {
                alert('An error occurred while submitting the assignment');
                submitBtn.prop('disabled', false).html('<i class="fas fa-upload"></i> Submit Assignment');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

