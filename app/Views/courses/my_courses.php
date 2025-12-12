<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-white mb-2">
                <i class="fas fa-graduation-cap"></i> My Enrolled Courses
            </h2>
            <p class="text-muted">Courses you are officially enrolled in.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Student Dashboard Cards -->
    <?php if (!empty($enrolledCourses)): ?>
    <div class="row mb-4">
        <!-- Upcoming Deadlines -->
        <div class="col-lg-4 col-md-4 mb-3">
            <div class="card bg-secondary text-light border-0 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                    <h3 class="text-white mb-2"><?= isset($stats['upcomingDeadlines']) ? $stats['upcomingDeadlines'] : '0' ?></h3>
                    <h6 class="text-white mb-2">Upcoming Deadlines</h6>
                    <p class="text-muted small mb-0">Assignments due this week</p>
                </div>
            </div>
        </div>

        <!-- Recent Grades -->
        <div class="col-lg-4 col-md-4 mb-3">
            <div class="card bg-secondary text-light border-0 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-3x text-success mb-3"></i>
                    <h3 class="text-white mb-2"><?= isset($stats['recentGrade']) ? $stats['recentGrade'] : 'N/A' ?></h3>
                    <h6 class="text-white mb-2">Recent Grades</h6>
                    <p class="text-muted small mb-0">Latest assignment grade</p>
                </div>
            </div>
        </div>

        <!-- Overall Progress -->
        <div class="col-lg-4 col-md-4 mb-3">
            <div class="card bg-secondary text-light border-0 h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                    <h3 class="text-white mb-2"><?= isset($stats['overallProgress']) ? $stats['overallProgress'] . '%' : '0%' ?></h3>
                    <h6 class="text-white mb-2">Overall Progress</h6>
                    <p class="text-muted small mb-0">Assignment completion</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>



    <div class="row" id="enrolledCoursesList">
        <?php
        if (!empty($enrolledCourses)):
            foreach ($enrolledCourses as $course):
        ?>
            <div class="col-md-6 mb-3">
                <div class="card bg-secondary text-light border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title text-white mb-0">
                                <i class="fas fa-graduation-cap text-primary"></i>
                                <?= esc($course['title'] ?? 'Untitled Course') ?>
                            </h6>
                            <?php if (isset($course['course_code']) && $course['course_code']): ?>
                                <span class="badge bg-info"><?= esc($course['course_code']) ?></span>
                            <?php endif; ?>
                        </div>

                        <p class="card-text text-muted mb-3 small">
                            <?= esc(substr($course['description'] ?? 'No description available', 0, 100)) ?>
                            <?= (strlen($course['description'] ?? '') > 100) ? '...' : '' ?>
                        </p>

                        <?php if (isset($course['teacher_name']) && $course['teacher_name']): ?>
                        <div class="mb-2">
                            <small class="text-white">
                                <i class="fas fa-chalkboard-teacher text-warning"></i>
                                <strong>Instructor:</strong> <?= esc($course['teacher_name']) ?>
                            </small>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($course['schedule']) && $course['schedule']): ?>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-clock text-info"></i>
                                <strong>Schedule:</strong> <?= esc($course['schedule']) ?>
                            </small>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($course['school_year']) && $course['school_year']): ?>
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt text-success"></i>
                                <?= esc($course['school_year']) ?>
                                <?php if (isset($course['semester']) && $course['semester']): ?>
                                    - <?= esc($course['semester']) ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <?php endif; ?>

                        <small class="text-muted d-block mb-3 pb-3 border-bottom border-dark">
                            <i class="fas fa-calendar-check"></i>
                            Enrolled: <?= isset($course['enrollment_date']) ? date('M j, Y', strtotime($course['enrollment_date'])) : 'N/A' ?>
                        </small>

                        <div class="mt-auto">
                            <a href="<?= base_url('assignment/student-view/' . $course['course_id']) ?>" class="btn btn-sm btn-primary w-100 mb-2">
                                <i class="fas fa-clipboard-list"></i> View Assignments
                            </a>
                        </div>

                        <?php if (!empty($course['materials'])): ?>
                            <div class="mt-3">
                                <h6 class="text-white">Course Materials:</h6>
                                <ul class="list-unstyled">
                                    <?php foreach ($course['materials'] as $material): ?>
                                        <li class="mb-2">
                                            <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-success" target="_blank">
                                                <i class="fas fa-download"></i> Download <?= esc($material['file_name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="mt-3">
                                <small class="text-muted">No materials available for this course.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
            endforeach;
        else:
        ?>
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-graduation-cap fa-4x mb-3 d-block"></i>
                    <h3 class="text-white">No Enrolled Courses Yet</h3>
                    <p>You haven't been approved for any courses yet.</p>
                    <p>Check your notifications or contact your teacher for enrollment updates.</p>
                    <a href="<?= base_url('courses') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-search"></i> Search
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
