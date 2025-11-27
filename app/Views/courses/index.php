<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Courses</h1>

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
                    <button class="btn btn-primary btn-sm enroll-btn" data-course-id="<?= $course['id'] ?>">
                        Enroll
                    </button>
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
                        courseCard += '<button class="btn btn-primary btn-sm enroll-btn" data-course-id="' + course.id + '">Enroll</button>';
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
});
</script>

<?= $this->endSection() ?></content>
