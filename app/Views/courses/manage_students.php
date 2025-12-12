<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-white mb-2">
                        <i class="fas fa-users-cog"></i> Manage Students
                    </h1>
                    <p class="text-muted mb-0">Manage enrolled students for this course</p>
                </div>
                <a href="<?= base_url('/course') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Courses
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Course Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-dark text-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-graduation-cap text-primary"></i>
                        <?= esc($course['title'] ?? 'Unknown Course') ?>
                    </h5>
                    <p class="card-text text-muted mb-3">
                        <?= esc($course['description'] ?? 'No description') ?>
                    </p>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-white">
                                <i class="fas fa-tag text-success"></i>
                                <strong>Course Code:</strong> <?= esc($course['course_code'] ?? 'N/A') ?>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-white">
                                <i class="fas fa-clock text-info"></i>
                                <strong>Schedule:</strong> <?= esc($course['schedule'] ?? 'N/A') ?>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-white">
                                <i class="fas fa-chalkboard-teacher text-warning"></i>
                                <strong>Teacher:</strong>
                                <?php
                                $teacherName = 'Unassigned';
                                if (!empty($course['instructor_id'])) {
                                    // This would need to be passed from controller
                                    // For now, we'll show the instructor_id
                                    $teacherName = 'Teacher ID: ' . $course['instructor_id'];
                                }
                                echo esc($teacherName);
                                ?>
                            </small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Management Sections -->
    <div class="row">
        <!-- Enrolled Students -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 bg-dark text-light mb-4">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-check text-success"></i>
                        Enrolled Students
                    </h5>
                    <span class="badge bg-success">
                        <?= count($enrolledStudents ?? []) ?> enrolled
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($enrolledStudents ?? [])): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No students enrolled in this course yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="py-2">Student</th>
                                        <th class="py-2">Email</th>
                                        <th class="py-2 text-center">Enrollment Date</th>
                                        <th class="py-2 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrolledStudents as $enrollment): ?>
                                        <tr data-enrollment-id="<?= esc($enrollment['id']) ?>">
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-user text-white small"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            <?= esc($enrollment['student_name']) ?>
                                                        </div>
                                                        <div class="small text-muted">ID: <?= esc($enrollment['user_id']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-envelope"></i>
                                                    <?= esc($enrollment['student_email']) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 text-center">
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 text-center">
                                                <button class="btn btn-sm btn-outline-danger remove-student-btn"
                                                        data-enrollment-id="<?= esc($enrollment['id']) ?>"
                                                        data-student-name="<?= esc($enrollment['student_name']) ?>">
                                                    <i class="fas fa-user-minus"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="card shadow-sm border-0 bg-dark text-light">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-clock text-warning"></i>
                        Pending Enrollment Requests
                    </h5>
                    <span class="badge bg-warning">
                        <?= count($pendingRequests ?? []) ?> pending
                    </span>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingRequests ?? [])): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0 small">No pending enrollment requests.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($pendingRequests as $request): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-secondary border-warning">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong class="text-white"><?= esc($request['student_name']) ?></strong>
                                                    <br><small class="text-muted"><?= esc($request['student_email']) ?></small>
                                                </div>
                                                <small class="badge bg-warning">
                                                    <?= date('M d', strtotime($request['enrollment_date'])) ?>
                                                </small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-success approve-enrollment-btn"
                                                        data-enrollment-id="<?= esc($request['id']) ?>"
                                                        data-student-name="<?= esc($request['student_name']) ?>">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-enrollment-btn"
                                                        data-enrollment-id="<?= esc($request['id']) ?>"
                                                        data-student-name="<?= esc($request['student_name']) ?>">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add Students Section -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-dark text-light">
                <div class="card-header border-secondary">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user-plus text-primary"></i>
                        Add Students
                    </h6>
                </div>
                <div class="card-body">
                    <form id="addStudentForm">
                        <div class="mb-3">
                            <label for="student_id" class="form-label small">
                                <i class="fas fa-users"></i>
                                Select Student
                            </label>
                            <select id="student_id" class="form-select form-select-sm bg-secondary text-light border-secondary">
                                <option value="">Choose student...</option>
                                <?php foreach ($allStudents ?? [] as $student): ?>
                                    <option value="<?= esc($student['id']) ?>">
                                        <?= esc($student['name']) ?> (<?= esc($student['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100" id="addStudentBtn">
                            <i class="fas fa-plus"></i> Add to Course
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm border-0 bg-dark text-light mt-4">
                <div class="card-header border-secondary">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-info"></i>
                        Enrollment Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Enrolled Students</small>
                            <span class="badge bg-success"><?= count($enrolledStudents ?? []) ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Pending Requests</small>
                            <span class="badge bg-warning"><?= count($pendingRequests ?? []) ?></span>
                        </div>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted fw-bold">Total Students</small>
                        <span class="badge bg-primary"><?= (count($enrolledStudents ?? []) + count($pendingRequests ?? [])) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Remove student from course
    $('.remove-student-btn').on('click', function() {
        const enrollmentId = $(this).data('enrollment-id');
        const studentName = $(this).data('student-name');
        const button = $(this);
        const row = button.closest('tr');

        if (confirm(`Are you sure you want to remove "${studentName}" from this course? This will cancel their enrollment.`)) {
            $.ajax({
                url: '<?= base_url('/course/remove-student/') ?>' + enrollmentId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        row.fadeOut(300, function() {
                            $(this).remove();
                            // Update stats
                            updateEnrollmentStats();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error removing student from course.');
                }
            });
        }
    });

    // Approve enrollment request
    $('.approve-enrollment-btn').on('click', function() {
        const enrollmentId = $(this).data('enrollment-id');
        const studentName = $(this).data('student-name');
        const button = $(this);
        const card = button.closest('.col-md-6');

        if (confirm(`Approve enrollment request from "${studentName}"?`)) {
            $.ajax({
                url: '<?= site_url('course/approve-enrollment/') ?>' + enrollmentId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        card.fadeOut(300, function() {
                            $(this).remove();
                            updateEnrollmentStats();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error approving enrollment.');
                }
            });
        }
    });

    // Reject enrollment request
    $('.reject-enrollment-btn').on('click', function() {
        const enrollmentId = $(this).data('enrollment-id');
        const studentName = $(this).data('student-name');
        const button = $(this);
        const card = button.closest('.col-md-6');

        if (confirm(`Reject enrollment request from "${studentName}"?`)) {
            $.ajax({
                url: '<?= site_url('course/reject-enrollment/') ?>' + enrollmentId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        card.fadeOut(300, function() {
                            $(this).remove();
                            updateEnrollmentStats();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error rejecting enrollment.');
                }
            });
        }
    });

    // Add student to course
    $('#addStudentForm').on('submit', function(e) {
        e.preventDefault();
        const studentId = $('#student_id').val();

        if (!studentId) {
            alert('Please select a student to add.');
            return;
        }

        const button = $('#addStudentBtn');
        const originalText = button.html();

        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Adding...');

        $.ajax({
            url: '<?= base_url('/course/enroll') ?>',
            method: 'POST',
            data: { course_id: <?= $course['id'] ?>, user_id: studentId },
            dataType: 'json',
            success: function(response) {
                button.prop('disabled', false).html(originalText);

                if (response.success) {
                    alert(response.message);
                    $('#student_id').val('');
                    // Reload page to update the lists
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                button.prop('disabled', false).html(originalText);
                alert('Error adding student to course.');
            }
        });
    });

    function updateEnrollmentStats() {
        // Refresh the page to update all stats
        setTimeout(function() {
            location.reload();
        }, 1000);
    }
});
</script>

<?= $this->endSection() ?>
