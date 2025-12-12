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
                <?php if (session()->has('user_id') && in_array(session()->get('role'), ['admin', 'teacher'])): ?>
                    <a href="<?= base_url('/course/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Create Course
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Form -->
            <div class="card bg-dark border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form id="searchForm" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-secondary border-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control border-0" 
                                       placeholder="Search courses by title or description..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                    
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
<div id="courseList" class="row g-4">
    <?php if (isset($courses) && is_array($courses)): ?>
        <?php foreach ($courses as $course): ?>
        <div class="col-md-6 col-lg-4 course-item" 
             data-title="<?= strtolower(esc($course['title'])) ?>" 
             data-description="<?= strtolower(esc($course['description'])) ?>"
             data-instructor-id="<?= isset($course['instructor_id']) ? $course['instructor_id'] : '' ?>"
             data-is-my-course="<?= (session()->has('user_id') && session()->get('role') === 'teacher' && isset($course['instructor_id']) && $course['instructor_id'] == session()->get('user_id')) ? 'true' : 'false' ?>">
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
                    
                    <?php if (isset($course['school_year']) && $course['school_year']): ?>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt"></i>
                                <?= esc($course['school_year']) ?>
                                <?php if (isset($course['semester']) && $course['semester']): ?>
                                    - <?= esc($course['semester']) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php endif; ?>
                    
                    <p class="card-text text-muted mb-3 flex-grow-1">
                        <?= esc($course['description']) ?>
                    </p>
                    
                    <div class="border-top border-bottom border-secondary py-2 mb-3">
                        <?php if (isset($course['teacher_name']) && $course['teacher_name']): ?>
                            <small class="text-white d-block mb-1">
                                <i class="fas fa-chalkboard-teacher text-warning"></i>
                                <strong>Instructor:</strong> <?= esc($course['teacher_name']) ?>
                            </small>
                        <?php endif; ?>
                        
                        <?php if (isset($course['schedule']) && $course['schedule']): ?>
                            <small class="text-muted d-block">
                                <i class="fas fa-clock text-info"></i>
                                <strong>Schedule:</strong> <?= esc($course['schedule']) ?>
                            </small>
                        <?php endif; ?>
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
                    <?php 
                    // Check if current user is the course instructor or admin
                    $isInstructor = false;
                    $isAdmin = false;
                    if (session()->has('user_id')) {
                        $isInstructor = (session()->get('role') === 'teacher' && isset($course['instructor_id']) && $course['instructor_id'] == session()->get('user_id'));
                        $isAdmin = (session()->get('role') === 'admin');
                    }
                    ?>
                    
                    <div class="mt-auto">
                        <?php if (session()->has('user_id')): ?>
                            <?php if ($isInstructor || $isAdmin): ?>
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('/course/show/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-users"></i> View Students
                                    </a>
                                    <a href="<?= base_url('/course/edit/' . $course['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit Course
                                    </a>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('/admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-upload"></i> Materials
                                        </a>
                                        <button class="btn btn-danger btn-sm delete-course-btn" data-course-id="<?= $course['id'] ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
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
        applyFilters();
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

    // AJAX server-side search on form submit
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();

        $.ajax({
            url: '<?= site_url('course/search') ?>',
            method: 'GET',
            data: { query: searchTerm },
            dataType: 'json',
            success: function(data) {
                $('#courseList').html('');

                if (data.courses && data.courses.length > 0) {
                    data.courses.forEach(function(course) {
                        var courseCard = '<div class="card mb-3 course-item" data-title="' + course.title.toLowerCase() + '" data-description="' + course.description.toLowerCase() + '">' +
                            '<div class="card-body">' +
                            '<h5 class="card-title">' + course.title + '</h5>' +
                            '<p class="card-text">' + course.description + '</p>';
                        <?php if (session()->has('user_id')): ?>
                        <?php if (in_array(session()->get('role'), ['admin', 'teacher'])): ?>
                        courseCard += '<div class="btn-group" role="group">' +
                            '<a href="<?= base_url('/course/edit/') ?>' + course.id + '" class="btn btn-warning btn-sm">' +
                            '<i class="fas fa-edit"></i> Edit</a>' +
                            '<a href="<?= base_url('/admin/course/') ?>' + course.id + '/upload" class="btn btn-info btn-sm">' +
                            '<i class="fas fa-upload"></i> Materials</a>' +
                            '<button class="btn btn-danger btn-sm delete-course-btn" data-course-id="' + course.id + '">' +
                            '<i class="fas fa-trash"></i> Delete</button>' +
                            '</div>';
                        <?php else: ?>
                        courseCard += '<button class="btn btn-primary btn-sm enroll-btn" data-course-id="' + course.id + '">Enroll</button>';
                        <?php endif; ?>
                        <?php endif; ?>
                        courseCard += '</div></div>';

                        $('#courseList').append(courseCard);
                    });
                } else {
                    $('#courseList').html('<p>No courses found.</p>');
                }
            },
            error: function() {
                alert('Error occurred while searching.');
            }
        });
    });

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
                    button.removeClass('btn-primary').addClass('btn-success').text('Enrolled').prop('disabled', true);
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
});
</script>

<?= $this->endSection() ?></content>
