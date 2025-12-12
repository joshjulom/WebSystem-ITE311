<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="<?= base_url('/course') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Courses
                </a>
            </div>

            <!-- Course Information -->
            <div class="card bg-dark border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="text-white mb-3">
                        <i class="fas fa-graduation-cap text-primary"></i>
                        <?= esc($course['title']) ?>
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-chalkboard-teacher"></i> Your Course
                        </span>
                    </h2>
                    <p class="text-muted mb-4">
                        <?= esc($course['description']) ?>
                    </p>
                    
                    <!-- Quick Stats -->
                    <div class="row text-center mt-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-secondary rounded">
                                <h3 class="text-success mb-1">
                                    <i class="fas fa-user-check"></i>
                                    <?= isset($enrolledStudents) ? count($enrolledStudents) : 0 ?>
                                </h3>
                                <small class="text-muted">Enrolled Students</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-secondary rounded">
                                <h3 class="text-warning mb-1">
                                    <i class="fas fa-user-clock"></i>
                                    <?= isset($pendingRequests) ? count($pendingRequests) : 0 ?>
                                </h3>
                                <small class="text-muted">Pending Requests</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-secondary rounded">
                                <h3 class="text-info mb-1">
                                    <i class="fas fa-users"></i>
                                    <?= (isset($enrolledStudents) ? count($enrolledStudents) : 0) + (isset($pendingRequests) ? count($pendingRequests) : 0) ?>
                                </h3>
                                <small class="text-muted">Total Interest</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (session()->has('user_id') && in_array(session()->get('role'), ['admin', 'teacher'])): ?>
                
                <!-- Pending Enrollment Requests -->
                <?php if (isset($pendingRequests) && count($pendingRequests) > 0): ?>
                <div class="card bg-dark border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-user-clock"></i>
                            Pending Enrollment Requests (<?= count($pendingRequests) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRequests as $request): ?>
                                    <tr id="enrollment-<?= $request['id'] ?>">
                                        <td><?= esc($request['student_name']) ?></td>
                                        <td><?= esc($request['student_email']) ?></td>
                                        <td><?= date('M d, Y', strtotime($request['enrollment_date'])) ?></td>
                                        <td>
                                            <button class="btn btn-success btn-sm approve-btn" 
                                                    data-enrollment-id="<?= $request['id'] ?>">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm reject-btn" 
                                                    data-enrollment-id="<?= $request['id'] ?>">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Enrolled Students -->
                <div class="card bg-dark border-0 shadow-sm">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-check"></i>
                            Enrolled Students (<?= isset($enrolledStudents) ? count($enrolledStudents) : 0 ?>)
                        </h5>
                        <?php if (isset($enrolledStudents) && count($enrolledStudents) > 0): ?>
                            <button class="btn btn-light btn-sm" onclick="exportToCSV()">
                                <i class="fas fa-download"></i> Export List
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (isset($enrolledStudents) && count($enrolledStudents) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Enrollment Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; ?>
                                        <?php foreach ($enrolledStudents as $student): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td>
                                                <i class="fas fa-user text-primary"></i>
                                                <?= esc($student['student_name']) ?>
                                            </td>
                                            <td><?= esc($student['student_email']) ?></td>
                                            <td><?= date('M d, Y', strtotime($student['enrollment_date'])) ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Enrolled
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h5 class="text-white">No Students Enrolled Yet</h5>
                                <p class="text-muted">Students will appear here once they enroll in this course.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Approve enrollment
    $('.approve-btn').on('click', function() {
        var enrollmentId = $(this).data('enrollment-id');
        var row = $('#enrollment-' + enrollmentId);
        
        if (confirm('Are you sure you want to approve this enrollment request?')) {
            $.ajax({
                url: '<?= site_url('course/approve-enrollment/') ?>' + enrollmentId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        row.fadeOut(300, function() {
                            $(this).remove();
                            // Reload page to update counts
                            location.reload();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error occurred while approving enrollment.');
                }
            });
        }
    });
    
    // Reject enrollment
    $('.reject-btn').on('click', function() {
        var enrollmentId = $(this).data('enrollment-id');
        var row = $('#enrollment-' + enrollmentId);
        
        if (confirm('Are you sure you want to reject this enrollment request?')) {
            $.ajax({
                url: '<?= site_url('course/reject-enrollment/') ?>' + enrollmentId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error occurred while rejecting enrollment.');
                }
            });
        }
    });
    
    // Export to CSV function
    window.exportToCSV = function() {
        var courseTitle = '<?= addslashes($course['title']) ?>';
        var csv = 'No.,Student Name,Email,Enrollment Date,Status\n';
        
        <?php if (isset($enrolledStudents) && count($enrolledStudents) > 0): ?>
        var students = <?= json_encode($enrolledStudents) ?>;
        students.forEach(function(student, index) {
            csv += (index + 1) + ',';
            csv += '"' + student.student_name + '",';
            csv += '"' + student.student_email + '",';
            csv += '"' + student.enrollment_date + '",';
            csv += '"Enrolled"\n';
        });
        <?php endif; ?>
        
        // Create download link
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', courseTitle.replace(/[^a-z0-9]/gi, '_') + '_students.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
});
</script>

<?= $this->endSection() ?>

