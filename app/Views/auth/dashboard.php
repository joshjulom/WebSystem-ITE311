<?= $this->extend('template') ?>

<?= $this->section('content') ?>
	<div class="mb-4">
		<h1 class="mb-0">Dashboard</h1>
	</div>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success" role="alert">
			<?= esc(session()->getFlashdata('success')) ?>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
		</div>
	<?php endif; ?>

	<div class="alert alert-info" role="alert">
		<strong>Welcome, <?= esc(session('user_name')) ?>!</strong><br>
		<small class="text-muted">Email: <?= esc(session('user_email')) ?> | Role: <?= esc(session('role')) ?></small>
	</div>

	<?php if (isset($role) && $role === 'admin'): ?>
		<!-- Course Management Dashboard -->
		<div class="row mb-4">
			<div class="col-12">
				<h2 class="text-white mb-3">Course Management</h2>
			</div>
		</div>

		<!-- Summary Cards -->
		<div class="row g-3 mb-4">
			<div class="col-md-6">
				<div class="card shadow-sm border-0 bg-dark text-light">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Total Courses</p>
								<h3 class="mb-0" id="totalCoursesCount"><?= isset($admin['totalCourses']) ? esc($admin['totalCourses']) : '0' ?></h3>
							</div>
							<div class="bg-primary rounded-circle p-3">
								<i class="fas fa-book text-white fs-4"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card shadow-sm border-0 bg-dark text-light">
					<div class="card-body">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<p class="text-muted mb-1">Active Courses</p>
								<h3 class="mb-0" id="activeCoursesCount"><?= isset($admin['activeCourses']) ? esc($admin['activeCourses']) : '0' ?></h3>
							</div>
							<div class="bg-success rounded-circle p-3">
								<i class="fas fa-check-circle text-white fs-4"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Courses Table -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white mb-3">All Courses</h5>
				<div class="table-responsive">
					<table class="table table-dark table-hover align-middle">
						<thead class="table-secondary">
							<tr>
								<th style="width: 7%;">Course Code</th>
								<th style="width: 12%;">Course Title</th>
								<th style="width: 18%;">Description</th>
								<th style="width: 9%;">School Year</th>
								<th style="width: 9%;">Semester</th>
								<th style="width: 10%;">Schedule</th>
								<th style="width: 10%;">Teacher</th>
								<th style="width: 7%;">Active Users</th>
								<th style="width: 10%;">Status</th>
								<th style="width: 8%;">Actions</th>
							</tr>
						</thead>
						<tbody id="coursesTableBody">
							<?php if (!empty($admin['courses'])): ?>
								<?php foreach ($admin['courses'] as $course): ?>
									<tr data-course-id="<?= esc($course['id']) ?>">
										<td><span class="badge bg-info"><?= esc($course['course_code'] ?? 'N/A') ?></span></td>
										<td><strong><?= esc($course['title'] ?? 'Untitled') ?></strong></td>
										<td><?= esc(substr($course['description'] ?? 'No description', 0, 60)) ?><?= (strlen($course['description'] ?? '') > 60) ? '...' : '' ?></td>
										<td><?= esc($course['school_year'] ?? 'N/A') ?></td>
										<td><?= esc($course['semester'] ?? 'N/A') ?></td>
										<td><?= esc($course['schedule'] ?? 'N/A') ?></td>
										<td><?= esc($course['teacher_name'] ?? 'Unassigned') ?></td>
										<td class="text-center">
											<span class="badge bg-success"><?= esc($course['active_users'] ?? '0') ?></span>
										</td>
										<td>
											<select class="form-select form-select-sm status-dropdown" data-course-id="<?= esc($course['id']) ?>">
												<option value="Active" <?= ($course['status'] ?? 'Active') === 'Active' ? 'selected' : '' ?>>Active</option>
												<option value="Inactive" <?= ($course['status'] ?? 'Active') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
											</select>
										</td>
										<td>
											<button class="btn btn-sm btn-outline-light edit-course-btn" data-course-id="<?= esc($course['id']) ?>">
												<i class="fas fa-edit"></i> Edit
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="10" class="text-center text-muted py-4">No courses found.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Edit Course Modal -->
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
									<input type="text" class="form-control" id="editCourseCode" readonly>
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

							<div class="row mb-3">
								<div class="col-md-6">
									<label for="editSchoolYear" class="form-label">School Year</label>
									<select class="form-select" id="editSchoolYear">
										<option value="">Select School Year</option>
										<option value="2023-2024">2023-2024</option>
										<option value="2024-2025">2024-2025</option>
										<option value="2025-2026">2025-2026</option>
										<option value="2026-2027">2026-2027</option>
									</select>
								</div>
								<div class="col-md-6">
									<label for="editSemester" class="form-label">Semester</label>
									<select class="form-select" id="editSemester">
										<option value="">Select Semester</option>
										<option value="1st Semester">1st Semester</option>
										<option value="2nd Semester">2nd Semester</option>
										<option value="Summer">Summer</option>
									</select>
								</div>
							</div>

							<div class="row mb-3">
								<div class="col-md-6">
									<label for="editStartDate" class="form-label">Start Date</label>
									<input type="date" class="form-control" id="editStartDate">
								</div>
								<div class="col-md-6">
									<label for="editEndDate" class="form-label">End Date</label>
									<input type="date" class="form-control" id="editEndDate">
								</div>
							</div>

							<div class="row mb-3">
								<div class="col-md-6">
									<label for="editTeacher" class="form-label">Teacher</label>
									<select class="form-select" id="editTeacher">
										<option value="">Select Teacher</option>
										<?php if (!empty($admin['teachers'])): ?>
											<?php foreach ($admin['teachers'] as $teacher): ?>
												<option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</div>
								<div class="col-md-6">
									<label for="editSchedule" class="form-label">Schedule</label>
									<select class="form-select" id="editSchedule">
										<option value="">Select Schedule</option>
										<option value="MWF 8:00-9:00 AM">MWF 8:00-9:00 AM</option>
										<option value="MWF 9:00-10:00 AM">MWF 9:00-10:00 AM</option>
										<option value="MWF 10:00-11:00 AM">MWF 10:00-11:00 AM</option>
										<option value="MWF 1:00-2:00 PM">MWF 1:00-2:00 PM</option>
										<option value="MWF 2:00-3:00 PM">MWF 2:00-3:00 PM</option>
										<option value="TTH 8:00-9:30 AM">TTH 8:00-9:30 AM</option>
										<option value="TTH 9:30-11:00 AM">TTH 9:30-11:00 AM</option>
										<option value="TTH 1:00-2:30 PM">TTH 1:00-2:30 PM</option>
										<option value="TTH 2:30-4:00 PM">TTH 2:30-4:00 PM</option>
									</select>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer border-secondary">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-primary" id="updateCourseBtn">Update</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Admin Course Management Scripts -->
		<script>
		$(document).ready(function() {
			// Status dropdown change handler
			$('.status-dropdown').on('change', function() {
				const courseId = $(this).data('course-id');
				const newStatus = $(this).val();
				const dropdown = $(this);
				const originalValue = dropdown.find('option:not(:selected)').val();

				if (confirm('Are you sure you want to change this course status to ' + newStatus + '?')) {
					$.ajax({
						url: '<?= base_url('/admin/course/updateStatus') ?>/' + courseId,
						method: 'POST',
						data: { status: newStatus },
						dataType: 'json',
						success: function(response) {
							if (response.success) {
								showAlert(response.message, 'success');
								updateCourseCounts(newStatus === 'Active' ? 1 : -1);
							} else {
								showAlert(response.message || 'Failed to update status', 'danger');
								dropdown.val(originalValue);
							}
						},
						error: function() {
							showAlert('An error occurred while updating status', 'danger');
							dropdown.val(originalValue);
						}
					});
				} else {
					dropdown.val(originalValue);
				}
			});

			// Edit course button handler
			$('.edit-course-btn').on('click', function() {
				const courseId = $(this).data('course-id');
				const row = $(this).closest('tr');

				// Populate modal with current data
				$('#editCourseId').val(courseId);
				$('#editCourseCode').val(row.find('td:eq(0) .badge').text());
				$('#editCourseTitle').val(row.find('td:eq(1) strong').text());
				
				// Load full course data via AJAX for description and other fields
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
								$('#editSchoolYear').val(course.school_year || '');
								$('#editSemester').val(course.semester || '');
								$('#editStartDate').val(course.start_date || '');
								$('#editEndDate').val(course.end_date || '');
								$('#editTeacher').val(course.instructor_id || '');
								$('#editSchedule').val(course.schedule || '');
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
					title: $('#editCourseTitle').val(),
					description: $('#editCourseDescription').val(),
					school_year: $('#editSchoolYear').val(),
					semester: $('#editSemester').val(),
					start_date: $('#editStartDate').val(),
					end_date: $('#editEndDate').val(),
					instructor_id: $('#editTeacher').val(),
					schedule: $('#editSchedule').val()
				};

				// Basic validation
				if (!formData.title || !formData.description) {
					showAlert('Please fill in all required fields', 'warning');
					return;
				}

				// Date validation: end date must not be before start date
				if (formData.start_date && formData.end_date) {
					const startDate = new Date(formData.start_date);
					const endDate = new Date(formData.end_date);
					
					if (endDate < startDate) {
						showAlert('End date cannot be earlier than start date. Please check your dates.', 'danger');
						return;
					}
				}

				// Disable button during request
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
							setTimeout(function() {
								location.reload();
							}, 1500);
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

			function renderCourseTable(courses) {
				const tbody = $('#coursesTableBody');
				tbody.empty();

				if (courses.length === 0) {
					tbody.append('<tr><td colspan="10" class="text-center text-muted py-4">No courses found.</td></tr>');
					return;
				}

				courses.forEach(function(course) {
					const description = course.description || 'No description';
					const truncatedDesc = description.length > 60 ? description.substring(0, 60) + '...' : description;
					
					const row = `
						<tr data-course-id="${course.id}">
							<td><span class="badge bg-info">${course.course_code || 'N/A'}</span></td>
							<td><strong>${course.title || 'Untitled'}</strong></td>
							<td>${truncatedDesc}</td>
							<td>${course.school_year || 'N/A'}</td>
							<td>${course.semester || 'N/A'}</td>
							<td>${course.schedule || 'N/A'}</td>
							<td>${course.teacher_name || 'Unassigned'}</td>
							<td class="text-center">
								<span class="badge bg-success">${course.active_users || '0'}</span>
							</td>
							<td>
								<select class="form-select form-select-sm status-dropdown" data-course-id="${course.id}">
									<option value="Active" ${course.status === 'Active' ? 'selected' : ''}>Active</option>
									<option value="Inactive" ${course.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
								</select>
							</td>
							<td>
								<button class="btn btn-sm btn-outline-light edit-course-btn" data-course-id="${course.id}">
									<i class="fas fa-edit"></i> Edit
								</button>
							</td>
						</tr>
					`;
					tbody.append(row);
				});

				// Re-attach event handlers
				$('.status-dropdown').off('change').on('change', function() {
					// Re-use the same handler as above
					location.reload(); // Simple approach for now
				});

				$('.edit-course-btn').off('click').on('click', function() {
					const courseId = $(this).data('course-id');
					const course = courses.find(c => c.id == courseId);
					
					if (course) {
						$('#editCourseId').val(courseId);
						$('#editCourseCode').val(course.course_code || '');
						$('#editCourseTitle').val(course.title || '');
						$('#editCourseDescription').val(course.description || '');
						$('#editSchoolYear').val(course.school_year || '');
						$('#editSemester').val(course.semester || '');
						$('#editStartDate').val(course.start_date || '');
						$('#editEndDate').val(course.end_date || '');
						$('#editTeacher').val(course.instructor_id || '');
						$('#editSchedule').val(course.schedule || '');
						$('#editCourseModal').modal('show');
					}
				});
			}

			function updateCourseCounts(delta) {
				const activeCount = parseInt($('#activeCoursesCount').text()) || 0;
				$('#activeCoursesCount').text(Math.max(0, activeCount + delta));
			}

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
	<?php elseif (isset($role) && $role === 'teacher'): ?>
		<!-- Pending Enrollment Requests -->
		<?php if (!empty($teacher['pendingRequests'])): ?>
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="card-title text-white mb-0">
						<i class="fas fa-user-clock text-warning"></i> Pending Enrollment Requests
					</h5>
					<span class="badge bg-warning"><?= count($teacher['pendingRequests']) ?> pending</span>
				</div>
				<div class="table-responsive">
					<table class="table table-dark table-hover align-middle mb-0">
						<thead class="table-secondary">
							<tr>
								<th class="py-3">Student</th>
								<th class="py-3">Course</th>
								<th class="py-3">Requested</th>
								<th class="py-3 text-center">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($teacher['pendingRequests'] as $request): ?>
							<tr data-enrollment-id="<?= $request['id'] ?>">
								<td class="py-3">
									<strong class="text-white"><?= esc($request['student_name']) ?></strong>
									<br><small class="text-muted"><?= esc($request['student_email']) ?></small>
								</td>
								<td class="py-3"><?= esc($request['course_title']) ?></td>
								<td class="py-3">
									<small class="text-muted">
										<?= date('M d, Y h:i A', strtotime($request['enrollment_date'])) ?>
									</small>
								</td>
								<td class="py-3 text-center">
									<div class="btn-group" role="group">
										<button class="btn btn-sm btn-success approve-btn" 
												data-enrollment-id="<?= $request['id'] ?>"
												data-student-name="<?= esc($request['student_name']) ?>">
											<i class="fas fa-check"></i> Approve
										</button>
										<button class="btn btn-sm btn-danger reject-btn" 
												data-enrollment-id="<?= $request['id'] ?>"
												data-student-name="<?= esc($request['student_name']) ?>">
											<i class="fas fa-times"></i> Reject
										</button>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Teacher Overview</h5>
				<p class="mb-2">Your courses and students at a glance.</p>
				<div class="row g-3">
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Total Courses</div>
							<div class="h4 mb-0"><?= isset($teacher['totalCourses']) ? esc($teacher['totalCourses']) : '0' ?></div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted mb-2">Recent Enrollments</div>
							<ul class="mb-0">
								<?php if (!empty($teacher['recentEnrollments'])): ?>
									<?php foreach ($teacher['recentEnrollments'] as $e): ?>
										<li><?= esc($e['name'] ?? 'Unnamed') ?> (<?= esc($e['email'] ?? '-') ?>) - <?= esc($e['title'] ?? 'Untitled Course') ?></li>
									<?php endforeach; ?>
								<?php else: ?>
									<li class="text-muted">No recent enrollments.</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Courses Table -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<div>
						<h5 class="card-title text-white mb-1">My Courses</h5>
						<p class="text-muted mb-0 small">List of all courses you are teaching.</p>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table table-dark table-hover align-middle mb-0">
						<thead class="table-secondary">
							<tr>
								<th class="py-3" style="width: 20%;">Title</th>
								<th class="py-3" style="width: 40%;">Description</th>
								<th class="py-3" style="width: 15%;">Created Date</th>
								<th class="py-3 text-center" style="width: 25%;">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($teacher['courses'])): ?>
								<?php foreach ($teacher['courses'] as $course): ?>
								<tr>
									<td class="py-3">
										<strong class="text-white"><?= esc($course['title'] ?? 'Untitled') ?></strong>
									</td>
									<td class="py-3">
										<span class="text-muted">
											<?= esc(substr($course['description'] ?? 'No description', 0, 100)) ?><?= (strlen($course['description'] ?? '') > 100) ? '...' : '' ?>
										</span>
									</td>
									<td class="py-3">
										<span class="text-muted small">
											<?= isset($course['created_at']) ? date('Y-m-d h:i:s A', strtotime($course['created_at'])) : 'N/A' ?>
										</span>
									</td>
									<td class="py-3 text-center">
										<div class="btn-group" role="group">
											<a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" 
											   class="btn btn-sm btn-primary">
												<i class="fas fa-upload"></i> Upload
											</a>
											<a href="<?= base_url('assignment/teacher-view/' . $course['id']) ?>" 
											   class="btn btn-sm btn-success">
												<i class="fas fa-tasks"></i> Assignments
											</a>
										</div>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="4" class="text-center text-muted py-5">
										<i class="fas fa-inbox fa-3x mb-3 d-block"></i>
										No courses found.
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php elseif (isset($role) && $role === 'student'): ?>
		<!-- Recent Notifications (Enrollment Status Updates) -->
		<?php
		$notificationModel = new \App\Models\NotificationModel();
		$user_id = session('user_id');
		$recentNotifications = $notificationModel->where('user_id', $user_id)
			->where('is_read', 0)
			->where('message LIKE', '%enrollment%')
			->orderBy('created_at', 'DESC')
			->limit(5)
			->findAll();
		?>
		<?php if (!empty($recentNotifications)): ?>
		<div class="card shadow-sm border-0 bg-gradient mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
			<div class="card-body">
				<h5 class="card-title text-white mb-3">
					<i class="fas fa-bell"></i> Enrollment Status Updates
				</h5>
				<?php foreach ($recentNotifications as $notification): ?>
					<div class="alert alert-light mb-2 d-flex justify-content-between align-items-center" role="alert">
						<div>
							<i class="fas fa-<?= strpos($notification['message'], 'approved') !== false ? 'check-circle text-success' : (strpos($notification['message'], 'declined') !== false ? 'times-circle text-danger' : 'info-circle text-info') ?>"></i>
							<strong><?= esc($notification['message']) ?></strong>
							<br><small class="text-muted"><?= date('M d, Y h:i A', strtotime($notification['created_at'])) ?></small>
						</div>
						<button class="btn btn-sm btn-outline-secondary mark-notif-read" data-notif-id="<?= $notification['id'] ?>">
							<i class="fas fa-check"></i> Dismiss
						</button>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Rejected Enrollments -->
		<?php
		$enrollmentModel = new \App\Models\EnrollmentModel();
		$user_id = session('user_id');
		$rejectedEnrollments = $enrollmentModel->getRejectedEnrollments($user_id);
		?>
		<?php if (!empty($rejectedEnrollments)): ?>
		<div class="card shadow-sm border-danger bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-danger">
					<i class="fas fa-times-circle"></i> Enrollment Requests Declined
				</h5>
				<p class="text-muted mb-3">The following enrollment requests were not approved by the instructor:</p>
				<?php foreach ($rejectedEnrollments as $rejected): ?>
					<div class="alert alert-danger d-flex justify-content-between align-items-center mb-2" role="alert">
						<div>
							<strong><?= esc($rejected['title']) ?></strong>
							<br><small class="text-muted">Requested on <?= date('M d, Y', strtotime($rejected['enrollment_date'])) ?></small>
						</div>
						<button class="btn btn-sm btn-outline-light remove-rejected-btn" 
								data-enrollment-id="<?= $rejected['id'] ?>"
								title="Remove from list">
							<i class="fas fa-trash"></i> Remove
						</button>
					</div>
				<?php endforeach; ?>
				<small class="text-muted"><i class="fas fa-info-circle"></i> You can request to enroll again from the Available Courses section below.</small>
			</div>
		</div>
		<?php endif; ?>
		
		<!-- Pending Enrollments -->
		<?php
		$pendingEnrollments = $enrollmentModel->getPendingEnrollments($user_id);
		?>
		<?php if (!empty($pendingEnrollments)): ?>
		<div class="alert alert-warning" role="alert">
			<h5 class="alert-heading">
				<i class="fas fa-clock"></i> Pending Enrollment Requests
			</h5>
			<p class="mb-2">You have <?= count($pendingEnrollments) ?> enrollment request(s) waiting for teacher approval:</p>
			<ul class="mb-0">
				<?php foreach ($pendingEnrollments as $pending): ?>
					<li><strong><?= esc($pending['title']) ?></strong> - Requested on <?= date('M d, Y', strtotime($pending['enrollment_date'])) ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
		
		<!-- Student Overview -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Student Overview</h5>
				<p class="mb-2">Your learning progress and recent courses.</p>
				<div class="row g-3">
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Total Enrolled</div>
							<div class="h4 mb-0" id="totalEnrolledCount">
								<?php
								$enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
								echo count($enrolledCourses);
								?>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Pending Approval</div>
							<div class="h4 mb-0 text-warning" id="pendingEnrollmentCount">
								<?php
								$pendingCount = $enrollmentModel->where('user_id', $user_id)
									->where('status', 'pending')
									->countAllResults();
								echo $pendingCount;
								?>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Available Courses</div>
							<div class="h4 mb-0" id="availableCoursesCount">
								<?php
								$availableCourses = $enrollmentModel->getAvailableCourses($user_id);
								echo count($availableCourses);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Enrolled Courses Section -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">My Enrolled Courses</h5>
				<p class="mb-3">Courses you are currently enrolled in.</p>
				<div class="row" id="enrolledCoursesList">
					<?php
					$enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
					$materialModel = new \App\Models\MaterialModel();
					if (!empty($enrolledCourses)):
						foreach ($enrolledCourses as $course):
							$materials = $materialModel->getMaterialsByCourse($course['course_id']);
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

									<?php if (!empty($materials)): ?>
										<div class="mt-3">
											<h6 class="text-white">Course Materials:</h6>
											<ul class="list-unstyled">
												<?php foreach ($materials as $material): ?>
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
							<div class="text-center text-muted py-4">
								<p>You are not enrolled in any courses yet.</p>
								<p>Browse available courses below and click "Enroll" to get started!</p>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Available Courses Section -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Available Courses</h5>
				<p class="mb-3">Courses available for enrollment.</p>
				<div class="row" id="availableCoursesList">
					<?php
					$availableCourses = $enrollmentModel->getAvailableCourses($user_id);
					if (!empty($availableCourses)):
						foreach ($availableCourses as $course):
					?>
						<div class="col-md-6 mb-3">
							<div class="card bg-secondary text-light border-0 h-100">
								<div class="card-body">
									<h6 class="card-title text-white mb-2">
										<?= esc($course['title'] ?? 'Untitled Course') ?>
									</h6>
									<p class="card-text text-muted mb-3">
										<?= esc(substr($course['description'] ?? 'No description available', 0, 100)) ?>
										<?= (strlen($course['description'] ?? '') > 100) ? '...' : '' ?>
									</p>
									<div class="d-flex justify-content-between align-items-center">
										<small class="text-muted">
											Instructor ID: <?= esc($course['instructor_id'] ?? 'N/A') ?>
										</small>
										<button class="btn btn-primary btn-sm enroll-btn"
												data-course-id="<?= esc($course['id']) ?>">
											Enroll
										</button>
									</div>
								</div>
							</div>
						</div>
					<?php
						endforeach;
					else:
					?>
						<div class="col-12">
							<div class="text-center text-muted py-4">
								<p>No courses available for enrollment at the moment.</p>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div class="card shadow-sm border-0 bg-dark text-light">
			<div class="card-body">
				<h5 class="card-title text-white">Dashboard</h5>
				<p class="mb-0">This is a protected page only visible after login.</p>
				<hr class="border-secondary">
				<p class="text-muted mb-0">You are successfully logged in as <strong><?= esc(session('role')) ?></strong>.</p>
			</div>
		</div>
	<?php endif; ?>

	<!-- AJAX Enrollment Script -->
	<?php if (isset($role) && $role === 'student'): ?>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
	$(document).ready(function() {
		// Handle notification dismiss button
		$('.mark-notif-read').on('click', function() {
			var notifId = $(this).data('notif-id');
			var alertDiv = $(this).closest('.alert');
			var card = $(this).closest('.card');
			
			$.post('<?= site_url('notifications/mark_read') ?>/' + notifId, function(response) {
				if (response.success) {
					alertDiv.fadeOut(300, function() {
						$(this).remove();
						// Hide the entire card if no more notifications
						if (card.find('.alert').length === 0) {
							card.fadeOut(300, function() {
								$(this).remove();
							});
						}
					});
					
					// Update navbar notification badge
					var currentCount = parseInt($('#notificationBadge').text()) || 0;
					if (currentCount > 0) {
						$('#notificationBadge').text(currentCount - 1).toggle(currentCount - 1 > 0);
					}
				} else {
					alert('Failed to dismiss notification');
				}
			}).fail(function() {
				alert('Error dismissing notification');
			});
		});
		
		// Handle remove rejected enrollment button
		$('.remove-rejected-btn').on('click', function() {
			var enrollmentId = $(this).data('enrollment-id');
			var alertDiv = $(this).closest('.alert');
			var card = $(this).closest('.card');
			
			if (confirm('Remove this rejected enrollment from your list?')) {
				$.ajax({
					url: '<?= site_url('course/remove-rejected-enrollment') ?>/' + enrollmentId,
					method: 'POST',
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							alertDiv.fadeOut(300, function() {
								$(this).remove();
								// Hide the entire card if no more rejected enrollments
								if (card.find('.alert').length === 0) {
									card.fadeOut(300, function() {
										$(this).remove();
									});
								}
							});
						} else {
							alert(response.message || 'Failed to remove enrollment');
						}
					},
					error: function() {
						alert('Error removing enrollment');
					}
				});
			}
		});
		
		$('.enroll-btn').on('click', function(e) {
			e.preventDefault();

			var button = $(this);
			var courseId = button.data('course-id');
			var originalText = button.text();

			// Disable button and show loading state
			button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enrolling...');

			// Send AJAX request
			$.post('<?= base_url('/course/enroll') ?>', {
				course_id: courseId
			})
			.done(function(response) {
				if (response.success) {
					// Capture course information before removing from DOM
					var courseCard = button.closest('.card');
					var courseTitle = courseCard.find('.card-title').text();
					var courseDescription = courseCard.find('.card-text').text();

					// Show success message
					showAlert(response.message, 'success');

					// Remove the course card from available courses
					button.closest('.col-md-6').fadeOut(300, function() {
						$(this).remove();

						// Update available courses count
						updateAvailableCoursesCount();

						// Update pending enrollment count (enrollment starts as pending)
						updatePendingCount();
					});
				} else {
					// Show error message
					showAlert(response.message, 'danger');

					// Re-enable button
					button.prop('disabled', false).text(originalText);
				}
			})
			.fail(function() {
				showAlert('An error occurred while processing your enrollment. Please try again.', 'danger');

				// Re-enable button
				button.prop('disabled', false).text(originalText);
			});
		});

		function showAlert(message, type) {
			var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
				message +
				'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
				'</div>';

			$('.alert').remove(); // Remove existing alerts
			$(alertHtml).insertAfter('.mb-4 h1');
		}

		function updateAvailableCoursesCount() {
			var currentCount = parseInt($('#availableCoursesCount').text());
			$('#availableCoursesCount').text(currentCount - 1);
		}

		function updateEnrolledCoursesCount() {
			var currentCount = parseInt($('#totalEnrolledCount').text());
			$('#totalEnrolledCount').text(currentCount + 1);
		}
		
		function updatePendingCount() {
			var currentCount = parseInt($('#pendingEnrollmentCount').text());
			$('#pendingEnrollmentCount').text(currentCount + 1);
		}

		function addCourseToEnrolled(courseId, courseTitle, courseDescription) {
			// Create enrolled course card without materials (materials will be available after refresh)
			var enrolledCardHtml = `
				<div class="col-md-6 mb-3">
					<div class="card bg-secondary text-light border-0 h-100">
						<div class="card-body">
							<h6 class="card-title text-white mb-2">${courseTitle}</h6>
							<p class="card-text text-muted mb-3">${courseDescription}</p>
							<small class="text-muted">
								Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
							</small>
							<div class="mt-3"><small class="text-muted">Materials will be available after refresh.</small></div>
						</div>
					</div>
				</div>
			`;

			// Remove empty state message if it exists
			$('#enrolledCoursesList .text-center').remove();

			// Add to enrolled courses section
			$('#enrolledCoursesList').append(enrolledCardHtml);

			// Animate the new card
			$('#enrolledCoursesList .col-md-6:last-child .card').hide().fadeIn(500);
		}
	});
	</script>
	<?php endif; ?>
	
	<!-- Enrollment Approval Script (Teacher) -->
	<?php if (isset($role) && $role === 'teacher'): ?>
	<script>
	$(document).ready(function() {
		// Approve enrollment
		$('.approve-btn').on('click', function() {
			const enrollmentId = $(this).data('enrollment-id');
			const studentName = $(this).data('student-name');
			const row = $(this).closest('tr');
			const button = $(this);
			
			if (confirm(`Approve enrollment request from ${studentName}?`)) {
				button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');
				
				$.ajax({
					url: '<?= site_url('course/approve-enrollment/') ?>' + enrollmentId,
					method: 'POST',
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							row.fadeOut(300, function() {
								$(this).remove();
								// Check if table is empty
								if ($('tbody tr').length === 0) {
									location.reload();
								}
							});
							showAlert(response.message, 'success');
						} else {
							showAlert(response.message, 'danger');
							button.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
						}
					},
					error: function() {
						showAlert('Error processing approval', 'danger');
						button.prop('disabled', false).html('<i class="fas fa-check"></i> Approve');
					}
				});
			}
		});
		
		// Reject enrollment
		$('.reject-btn').on('click', function() {
			const enrollmentId = $(this).data('enrollment-id');
			const studentName = $(this).data('student-name');
			const row = $(this).closest('tr');
			const button = $(this);
			
			if (confirm(`Reject enrollment request from ${studentName}?`)) {
				button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejecting...');
				
				$.ajax({
					url: '<?= site_url('course/reject-enrollment/') ?>' + enrollmentId,
					method: 'POST',
					dataType: 'json',
					success: function(response) {
						if (response.success) {
							row.fadeOut(300, function() {
								$(this).remove();
								// Check if table is empty
								if ($('tbody tr').length === 0) {
									location.reload();
								}
							});
							showAlert(response.message, 'info');
						} else {
							showAlert(response.message, 'danger');
							button.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
						}
					},
					error: function() {
						showAlert('Error processing rejection', 'danger');
						button.prop('disabled', false).html('<i class="fas fa-times"></i> Reject');
					}
				});
			}
		});
		
		function showAlert(message, type) {
			const alertHtml = `
				<div class="alert alert-${type} alert-dismissible fade show" role="alert">
					<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			`;
			$('.container, .container-fluid').first().prepend(alertHtml);
			
			setTimeout(function() {
				$('.alert').fadeOut(300, function() { $(this).remove(); });
			}, 5000);
		}
	});
	</script>
	<?php endif; ?>
<?= $this->endSection() ?>
