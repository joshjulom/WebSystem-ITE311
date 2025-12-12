<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-white mb-2">
                        <i class="fas fa-user-tie"></i> Assign Teacher
                    </h1>
                    <p class="text-muted mb-0">Assign a teacher to this course</p>
                </div>
                <a href="<?= base_url('/course') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Courses
                </a>
            </div>
        </div>
    </div>

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
                        <div class="col-md-6">
                            <small class="text-white">
                                <i class="fas fa-tag text-success"></i>
                                <strong>Course Code:</strong> <?= esc($course['course_code'] ?? 'N/A') ?>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-white">
                                <i class="fas fa-clock text-info"></i>
                                <strong>Schedule:</strong> <?= esc($course['schedule'] ?? 'N/A') ?>
                            </small>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>Current Teacher:</strong>
                            <?php
                            $currentTeacher = null;
                            if (!empty($course['instructor_id'])) {
                                foreach ($teachers as $teacher) {
                                    if ($teacher['id'] == $course['instructor_id']) {
                                        $currentTeacher = $teacher;
                                        break;
                                    }
                                }
                            }
                            if ($currentTeacher) {
                                echo esc($currentTeacher['name']);
                            } else {
                                echo '<span class="text-danger">Unassigned</span>';
                            }
                            ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Assign Teacher Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 bg-dark text-light">
                <div class="card-header border-secondary">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus text-primary"></i>
                        Assign Teacher
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('/course/assign-teacher/' . $course['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Course Display -->
                        <div class="mb-3">
                            <label for="course_display" class="form-label">
                                <i class="fas fa-graduation-cap text-primary"></i>
                                Course
                            </label>
                            <input type="text" id="course_display" class="form-control bg-secondary text-light border-secondary" value="<?= esc($course['title']) ?>" readonly>
                            <div class="form-text text-muted">
                                Course title (readonly)
                            </div>
                        </div>

                        <!-- Select Teacher -->
                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">
                                <i class="fas fa-chalkboard-teacher text-warning"></i>
                                Select Teacher
                            </label>
                            <select name="teacher_id" id="teacher_id" class="form-select bg-secondary text-light border-secondary">
                                <option value="">No teacher assigned</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= esc($teacher['id']) ?>"
                                            <?= (isset($course['instructor_id']) && $course['instructor_id'] == $teacher['id']) ? 'selected' : '' ?>>
                                        <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">
                                Select the teacher who will instruct this course. Leave empty to unassign.
                            </div>
                        </div>

                        <!-- Semester/Term -->
                        <div class="mb-3">
                            <label for="semester" class="form-label">
                                <i class="fas fa-calendar-alt text-info"></i>
                                Semester/Term
                            </label>
                            <select name="semester" id="semester" class="form-select bg-secondary text-light border-secondary">
                                <option value="" <?= (!isset($course['semester']) || empty($course['semester'])) ? 'selected' : '' ?>>Select Semester</option>
                                <option value="1st Semester" <?= (isset($course['semester']) && $course['semester'] == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
                                <option value="2nd Semester" <?= (isset($course['semester']) && $course['semester'] == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
                                <option value="Summer" <?= (isset($course['semester']) && $course['semester'] == 'Summer') ? 'selected' : '' ?>>Summer</option>
                            </select>
                            <div class="form-text text-muted">
                                Select the academic period for this course.
                            </div>
                        </div>

                        <!-- Academic Year -->
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">
                                <i class="fas fa-calendar text-success"></i>
                                Academic Year
                            </label>
                            <input type="text" name="academic_year" id="academic_year" class="form-control bg-secondary text-light border-secondary"
                                   value="<?= esc($course['school_year'] ?? date('Y') . '-' . (date('Y')+1)) ?>" placeholder="e.g., 2024-2025">
                            <div class="form-text text-muted">
                                Enter the academic year (e.g., 2024-2025).
                            </div>
                        </div>

                        <!-- Maximum Students -->
                        <div class="mb-3">
                            <label for="max_students" class="form-label">
                                <i class="fas fa-users text-primary"></i>
                                Maximum Students
                            </label>
                            <input type="number" name="max_students" id="max_students" class="form-control bg-secondary text-light border-secondary"
                                   value="<?= esc($course['max_students'] ?? 30) ?>" min="1" max="100">
                            <div class="form-text text-muted">
                                Set the maximum number of students for this course.
                            </div>
                        </div>

                        <!-- Course Schedule (Multiple Days) -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-clock text-info"></i>
                                Course Schedule (Select Days)
                            </label>
                            <div class="row g-2">
                                <?php
                                $daysMap = [
                                    'Monday' => 'Mon',
                                    'Tuesday' => 'Tue',
                                    'Wednesday' => 'Wed',
                                    'Thursday' => 'Thu',
                                    'Friday' => 'Fri'
                                ];
                                $selectedDays = isset($course['schedule']) ? explode(',', $course['schedule']) : [];
                                foreach ($daysMap as $fullDay => $shortDay):
                                ?>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="schedule[]" value="<?= esc($shortDay) ?>" id="day_<?= esc($shortDay) ?>"
                                                   <?= in_array($shortDay, $selectedDays) ? 'checked' : '' ?>>
                                            <label class="form-check-label text-light" for="day_<?= esc($shortDay) ?>">
                                                <?= esc($fullDay) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text text-muted">
                                Select the days of the week when this course meets.
                            </div>
                        </div>

                        <!-- Start Time and End Time -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">
                                    <i class="fas fa-hourglass-start text-success"></i>
                                    Start Time
                                </label>
                                <input type="time" name="start_time" id="start_time" class="form-control bg-secondary text-light border-secondary"
                                       value="<?= esc($course['start_time'] ?? '08:00') ?>">
                                <div class="form-text text-muted">
                                    Course start time.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">
                                    <i class="fas fa-hourglass-end text-danger"></i>
                                    End Time
                                </label>
                                <input type="time" name="end_time" id="end_time" class="form-control bg-secondary text-light border-secondary"
                                       value="<?= esc($course['end_time'] ?? '09:00') ?>">
                                <div class="form-text text-muted">
                                    Course end time.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="javascript:void(0)" id="removeTeacherBtn" class="btn btn-warning">
                                <i class="fas fa-user-minus"></i> Remove Teacher
                            </a>
                            <a href="<?= base_url('/course') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Available Teachers List -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-dark text-light">
                <div class="card-header border-secondary">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-users text-info"></i>
                        Available Teachers
                    </h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($teachers)): ?>
                        <p class="text-muted small">No teachers available.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush bg-transparent">
                            <?php foreach ($teachers as $teacher): ?>
                                <div class="list-group-item bg-transparent border-secondary px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-user-tie text-white small"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-bold small">
                                                <?= esc($teacher['name']) ?>
                                                <?php if (isset($course['instructor_id']) && $course['instructor_id'] == $teacher['id']): ?>
                                                    <span class="badge bg-success ms-1 small">Assigned</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small">
                                                <?= esc($teacher['email']) ?>
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
    </div>
</div>

<script>
// Highlight selected teacher in the dropdown when form loads
$(document).ready(function() {
    const currentTeacherId = <?= json_encode($course['instructor_id'] ?? null) ?>;

    if (currentTeacherId) {
        $('#teacher_id').val(currentTeacherId);

        // Also highlight in the list
        $(`.list-group-item[data-teacher-id="${currentTeacherId}"]`).addClass('bg-primary bg-opacity-25');
    }

    // Handle remove teacher button
    $('#removeTeacherBtn').on('click', function() {
        if (confirm('Are you sure you want to remove the assigned teacher from this course?')) {
            $('#teacher_id').val('');
            $('form').submit();
        }
    });
});
</script>

<?= $this->endSection() ?>
