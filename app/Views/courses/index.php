<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-white mb-2">
                        <i class="fas fa-book"></i> Courses
                    </h1>
                    <p class="text-muted mb-0">Browse and manage available courses</p>
                </div>
                <?php if (session()->has('user_id') && session()->get('role') === 'admin'): ?>
                    <a href="<?= base_url('/course/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Create Course
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Bar -->
            <div class="card bg-dark border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-text bg-secondary border-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-0"
                                       placeholder="Search courses by title or description..." autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <?php if (session()->has('user_id') && session()->get('role') === 'teacher'): ?>
                        <div class="mt-3">
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="filterAll">
                                    <i class="fas fa-th"></i> All Courses
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="filterMyCourses">
                                    <i class="fas fa-chalkboard-teacher"></i> My Courses Only
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

<!-- Course List -->
<div id="courseList">
<?php if (session()->has('user_id') && session()->get('role') === 'admin'): ?>
<!-- Admin Course Management Table -->
<div class="card shadow-sm border-0 bg-dark text-light">
    <div class="card-body">
        <h5 class="card-title text-white mb-3">All Courses</h5>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th class="text-center py-3" style="width: 8%;">Course ID</th>
                        <th class="py-3" style="width: 18%;">Course Title</th>
                        <th class="py-3" style="width: 19%;">Current Teacher</th>
                        <th class="py-3" style="width: 15%;">Schedule</th>
                        <th class="text-center py-3" style="width: 10%;">Status</th>
                        <th class="text-center py-3" style="width: 30%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($courses) && is_array($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <tr data-course-id="<?= esc($course['id']) ?>">
                                <td><span class="badge bg-info">#<?= esc($course['id']) ?></span></td>
                                <td><strong><?= esc($course['title'] ?? 'Untitled') ?></strong></td>
                                <td>
                                    <?php if (!empty($course['teacher_name'])): ?>
                                        <strong><?= esc($course['teacher_name']) ?></strong><br>
                                    <?php else: ?>
                                        <span class="text-danger">Unassigned</span><br>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        Semester: <?= esc($course['semester'] ?? 'Not set') ?><br>
                                        Year: <?= esc($course['school_year'] ?? 'Not set') ?><br>
                                        Max Students: <?= esc($course['max_students'] ?? 30) ?>
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        Days: <?= esc($course['schedule'] ?? 'Not set') ?><br>
                                        Start: <?= esc($course['start_time'] ?? 'Not set') ?><br>
                                        End: <?= esc($course['end_time'] ?? 'Not set') ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-success">Published</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('/course/assign-teacher/' . $course['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-user-tie"></i> Assign Teacher
                                        </a>
                                        <a href="<?= base_url('/course/manage-students/' . $course['id']) ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-users-cog"></i> Manage Students
                                        </a>
                                        <button class="btn btn-sm btn-outline-light edit-course-btn" data-course-id="<?= esc($course['id']) ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Current Card Display for Students/Teachers -->
<div class="row g-4">
    <?php if (isset($courses) && is_array($courses)): ?>
        <?php foreach ($courses as $course): ?>
        <div class="col-md-6 col-lg-4 course-item"
             data-title="<?= strtolower(esc($course['title'])) ?>"
             data-description="<?= strtolower(esc($course['description'])) ?>"
             data-instructor-id="<?= isset($course['instructor_id']) ? $course['instructor_id'] : '' ?>"
             data-is-my-course="<?= (isset($course['instructor_id']) && $course['instructor_id'] == session()->get('user_id')) ? 'true' : 'false' ?>">
            <div class="card bg-dark border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title text-white mb-0">
                            <i class="fas fa-graduation-cap text-primary"></i>
                            <?= esc($course['title']) ?>
                            <?php if (session()->has('user_id') && session()->get('role') === 'teacher' && isset($course['instructor_id']) && $course['instructor_id'] == session()->get('user_id')): ?>
                                <span class="badge bg-info ms-2">
                                    <i class="fas fa-chalkboard-teacher"></i> Your Course
                                </span>
                            <?php endif; ?>
                        </h5>
                        <?php if (isset($course['course_code']) && $course['course_code']): ?>
                            <span class="badge bg-success"><?= esc($course['course_code']) ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="card-text text-muted mb-3 flex-grow-1">
                        <?= esc($course['description']) ?>
                    </p>

                    <div class="border-top border-bottom border-secondary py-2 mb-3">
                        <?php if (isset($course['teacher_name']) && $course['teacher_name']): ?>
                            <small class="text-white d-block mb-2">
                                <i class="fas fa-chalkboard-teacher text-warning"></i>
                                <strong>Instructor:</strong> <?= esc($course['teacher_name']) ?>
                            </small>
                        <?php endif; ?>

                        <?php if (isset($course['school_year']) && $course['school_year']): ?>
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-calendar-alt text-success"></i>
                                <strong>Academic Year:</strong> <?= esc($course['school_year']) ?>
                            </small>
                        <?php endif; ?>

                        <?php if (isset($course['semester']) && $course['semester']): ?>
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-graduation-cap text-info"></i>
                                <strong>Semester/Term:</strong> <?= esc($course['semester']) ?>
                            </small>
                        <?php endif; ?>

                        <?php if (isset($course['schedule']) && $course['schedule']): ?>
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-clock text-warning"></i>
                                <strong>Course Schedule:</strong> <?= esc($course['schedule']) ?>
                            </small>
                        <?php endif; ?>

                        <div class="row mt-2">
                            <?php if (isset($course['start_time']) && $course['start_time']): ?>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-play text-primary"></i>
                                        <strong>Start:</strong> <?= date('h:i A', strtotime($course['start_time'])) ?>
                                    </small>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($course['end_time']) && $course['end_time']): ?>
                                <div class="col-6">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-stop text-danger"></i>
                                        <strong>End:</strong> <?= date('h:i A', strtotime($course['end_time'])) ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (session()->has('user_id') && in_array(session()->get('role'), ['admin', 'teacher']) && isset($enrollmentCounts[$course['id']])): ?>
                        <div class="border-top border-secondary pt-3 mb-3">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="text-success">
                                        <i class="fas fa-user-check"></i>
                                        <strong><?= $enrollmentCounts[$course['id']]['approved'] ?></strong>
                                    </div>
                                    <small class="text-muted">Enrolled</small>
                                </div>
                                <div class="col-6">
                                    <div class="text-warning">
                                        <i class="fas fa-user-clock"></i>
                                        <strong><?= $enrollmentCounts[$course['id']]['pending'] ?></strong>
                                    </div>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-auto">
                        <?php if (session()->has('user_id')): ?>
                            <?php if ((session()->get('role') == 'admin') || (session()->get('role') == 'teacher' && isset($course['instructor_id']) && $course['instructor_id'] == session()->get('user_id'))): ?>
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('/course/show/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-users"></i> View Students
                                    </a>
                                    <?php if (session()->get('role') == 'admin'): ?>
                                    <a href="<?= base_url('/course/edit/' . $course['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit Course
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('/assignment/teacher-view/' . $course['id']) ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-tasks"></i> Assignments
                                    </a>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('/admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-upload"></i> Materials
                                        </a>
                                        <?php if (session()->get('role') == 'admin'): ?>
                                        <button class="btn btn-danger btn-sm delete-course-btn" data-course-id="<?= $course['id'] ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php elseif (session()->get('role') === 'student'): ?>
                                <button class="btn btn-primary w-100 enroll-btn" data-course-id="<?= $course['id'] ?>">
                                    <i class="fas fa-user-plus"></i> Enroll Now
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card bg-dark border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-white">No Courses Available</h5>
                    <p class="text-muted">Check back later for new courses.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>

<!-- Edit Course Modal -->
<?php if (session()->has('user_id') && session()->get('role') === 'admin'): ?>
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm">
                    <input type="hidden" id="editCourseId">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editCourseCode" class="form-label">Course Code</label>
                            <input type="text" class="form-control" id="editCourseCode" placeholder="e.g., ITE101">
                            <div class="form-text text-muted">Editable course code. Changes will be saved to the course record.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="editCourseTitle" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editCourseTitle" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editCourseDescription" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editCourseDescription" rows="3" required></textarea>
                    </div>
                    <!-- Teacher and Schedule removed from edit modal per admin request; use Assign Teacher action for teacher changes -->
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateCourseBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

        </div>
    </div>
</div>

<script>
// Client-side filtering
$(document).ready(function() {
    // Course filter state
    var currentFilter = 'all';
    
    // Search input filtering
    $('#searchInput').on('input', function() {
        // For admin table
        if ($('#courseList table').length > 0) {
            var term = $(this).val().toLowerCase();
            $('#courseList tbody tr').each(function() {
                var title = $(this).find('td').eq(1).text().toLowerCase();
                var description = $(this).find('td').eq(2).find('small').text().toLowerCase();
                if (title.includes(term) || description.includes(term)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            applyFilters();
        }
    });
    
    // My Courses filter buttons
    $('#filterAll').on('click', function() {
        currentFilter = 'all';
        $('#filterAll').addClass('active');
        $('#filterMyCourses').removeClass('active');
        applyFilters();
    });
    
    $('#filterMyCourses').on('click', function() {
        currentFilter = 'my';
        $('#filterMyCourses').addClass('active');
        $('#filterAll').removeClass('active');
        applyFilters();
    });
    
    // Apply all filters
    function applyFilters() {
        var searchTerm = $('#searchInput').val().toLowerCase();

        $('.course-item').each(function() {
            var title = $(this).data('title');
            var description = $(this).data('description');
            var isMyCourse = $(this).data('is-my-course') === true || $(this).data('is-my-course') === 'true';

            var matchesSearch = !searchTerm || title.includes(searchTerm) || description.includes(searchTerm);
            var matchesFilter = (currentFilter === 'all') || (currentFilter === 'my' && isMyCourse);

            if (matchesSearch && matchesFilter) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }



    // Enrollment functionality
    $(document).on('click', '.enroll-btn', function() {
        var courseId = $(this).data('course-id');
        var button = $(this);

        $.ajax({
            url: '<?= site_url('course/enroll') ?>',
            method: 'POST',
            data: { course_id: courseId },
            dataType: 'json',
            success: function(response) {
                    if (response.success) {
                        // Enrollment request submitted - show pending state until teacher approves
                        button.removeClass('btn-primary').addClass('btn-warning').text('Pending Approval').prop('disabled', true);
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
            error: function() {
                alert('Error enrolling in course.');
            }
        });
    });

    // Delete course functionality
    $(document).on('click', '.delete-course-btn', function() {
        var courseId = $(this).data('course-id');
        var cardElement = $(this).closest('.course-item');

        if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
            $.ajax({
                url: '<?= site_url('course/delete/') ?>' + courseId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        cardElement.fadeOut(300, function() {
                            $(this).remove();
                        });
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error occurred while deleting course.');
                }
            });
        }
    });



    // Edit course button handler
    $('.edit-course-btn').on('click', function() {
        const courseId = $(this).data('course-id');
        const row = $(this).closest('tr');

        $('#editCourseId').val(courseId);
        $('#editCourseCode').val(row.find('td:eq(0) .badge').text().trim());
        $('#editCourseTitle').val(row.find('td:eq(1) strong').text().trim());
        $('#editCourseDescription').val(row.find('td:eq(2) small').text().trim() || '');

        // Load full course data via AJAX
        $.ajax({
            url: '<?= base_url('/admin/course/search') ?>',
            method: 'GET',
            data: { search: '' },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.courses) {
                    const course = response.courses.find(c => c.id == courseId);
                    if (course) {
                        $('#editCourseDescription').val(course.description || '');
                        // Populate course code from server (prefer authoritative value)
                        $('#editCourseCode').val(course.course_code || row.find('td:eq(0) .badge').text().trim());
                        // Teacher and schedule are managed via separate actions (Assign Teacher / Schedule)
                    }
                }
                $('#editCourseModal').modal('show');
            },
            error: function() {
                showAlert('Failed to load course details', 'danger');
            }
        });
    });

    // Update course button handler
    $('#updateCourseBtn').on('click', function() {
        const courseId = $('#editCourseId').val();
        const formData = {
            title: $('#editCourseTitle').val().trim(),
            description: $('#editCourseDescription').val().trim(),
            course_code: $('#editCourseCode').val().trim()
        };

        if (!formData.title || !formData.description) {
            showAlert('Please fill in all required fields', 'warning');
            return;
        }

        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

        $.ajax({
            url: '<?= base_url('/admin/course/updateDetails') ?>/' + courseId,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#updateCourseBtn').prop('disabled', false).html('Update');

                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#editCourseModal').modal('hide');
                    // Update badge in the table to show new course code
                    const badgeCell = $('#courseList').find('tr[data-course-id="' + courseId + '"] td').first();
                    const newCode = formData.course_code || ('#' + courseId);
                    badgeCell.find('.badge').text(newCode);
                    // Also update any other visible places if needed (e.g., cards)
                } else {
                    showAlert(response.message || 'Failed to update course', 'danger');
                }
            },
            error: function() {
                $('#updateCourseBtn').prop('disabled', false).html('Update');
                showAlert('An error occurred while updating course', 'danger');
            }
        });
    });

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('.alert').remove();
        $(alertHtml).insertAfter('.mb-4 h1');
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});
</script>

<?= $this->endSection() ?></content>
