<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Courses</h1>
                <?php if (session()->has('user_id') && in_array(session()->get('role'), ['admin', 'teacher'])): ?>
                    <a href="<?= base_url('/course/create') ?>" class="btn btn-success">
                        <i class="fas fa-plus"></i> Create Course
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Form -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form id="searchForm" class="d-flex">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search courses..." autocomplete="off">
                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                    </form>
                </div>
            </div>

<!-- Course List -->
<div id="courseList">
    <?php if (isset($courses) && is_array($courses)): ?>
        <?php foreach ($courses as $course): ?>
        <div class="card mb-3 course-item" data-title="<?= strtolower(esc($course['title'])) ?>" data-description="<?= strtolower(esc($course['description'])) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                <p class="card-text"><?= esc($course['description']) ?></p>
                <?php if (session()->has('user_id')): ?>
                    <?php if (in_array(session()->get('role'), ['admin', 'teacher'])): ?>
                        <div class="btn-group" role="group">
                            <a href="<?= base_url('/course/edit/' . $course['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url('/admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-upload"></i> Materials
                            </a>
                            <button class="btn btn-danger btn-sm delete-course-btn" data-course-id="<?= $course['id'] ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-primary btn-sm enroll-btn" data-course-id="<?= $course['id'] ?>">
                            Enroll
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No courses available.</p>
    <?php endif; ?>
</div>

<script>
// Client-side filtering
$(document).ready(function() {
    $('#searchInput').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();

        $('.course-item').each(function() {
            var title = $(this).data('title');
            var description = $(this).data('description');

            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

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
