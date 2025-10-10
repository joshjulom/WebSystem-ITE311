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
		<!-- Admin Overview -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Admin Overview</h5>
				<p class="mb-2">Quick stats and recent activity.</p>
				<div class="row g-3">
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Total Users</div>
							<div class="h4 mb-0"><?= isset($admin['totalUsers']) ? esc($admin['totalUsers']) : '0' ?></div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted mb-2">Latest Users</div>
							<ul class="mb-0">
								<?php if (!empty($admin['latestUsers'])): ?>
									<?php foreach ($admin['latestUsers'] as $u): ?>
										<li><?= esc($u['name'] ?? 'Unnamed') ?> (<?= esc($u['email'] ?? '-') ?>)</li>
									<?php endforeach; ?>
								<?php else: ?>
									<li class="text-muted">No recent users.</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Login Monitoring -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Login Monitoring (Last 7 Days)</h5>
				<p class="mb-2">Track user login activity and patterns.</p>
				<div class="row g-3">
					<div class="col-md-12">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted mb-2">Login Statistics by Role</div>
							<div class="row">
								<?php if (!empty($admin['loginStatsByRole'])): ?>
									<?php foreach ($admin['loginStatsByRole'] as $stat): ?>
										<div class="col-md-4 mb-2">
											<div class="d-flex justify-content-between">
												<span class="text-capitalize"><?= esc($stat['user_role']) ?>:</span>
												<span class="fw-bold"><?= esc($stat['login_count']) ?></span>
											</div>
										</div>
									<?php endforeach; ?>
								<?php else: ?>
									<div class="col-12">
										<span class="text-muted">No login data available.</span>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Recent Logins -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Recent Logins</h5>
				<p class="mb-2">Latest user login activity.</p>
				<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr>
								<th>User</th>
								<th>Role</th>
								<th>IP Address</th>
								<th>Login Time</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($admin['recentLogins'])): ?>
								<?php foreach ($admin['recentLogins'] as $login): ?>
									<tr>
										<td>
											<div class="fw-bold"><?= esc($login['user_name'] ?? 'Unknown') ?></div>
											<small class="text-muted"><?= esc($login['user_email'] ?? '') ?></small>
										</td>
										<td>
											<span class="badge bg-<?= $login['user_role'] === 'admin' ? 'danger' : ($login['user_role'] === 'teacher' ? 'warning' : 'info') ?>">
												<?= esc(ucfirst($login['user_role'] ?? 'Unknown')) ?>
											</span>
										</td>
										<td><?= esc($login['ip_address'] ?? 'Unknown') ?></td>
										<td><?= esc($login['login_time'] ?? 'Unknown') ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="4" class="text-center text-muted">No recent logins found.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Recent Active Users -->
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Recent Active Users (Last 7 Days)</h5>
				<p class="mb-2">Users who have logged in recently.</p>
				<div class="table-responsive">
					<table class="table table-dark table-sm">
						<thead>
							<tr>
								<th>User</th>
								<th>Role</th>
								<th>Last Login</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($admin['recentUniqueUsers'])): ?>
								<?php foreach ($admin['recentUniqueUsers'] as $user): ?>
									<tr>
										<td>
											<div class="fw-bold"><?= esc($user['user_name'] ?? 'Unknown') ?></div>
											<small class="text-muted"><?= esc($user['user_email'] ?? '') ?></small>
										</td>
										<td>
											<span class="badge bg-<?= $user['user_role'] === 'admin' ? 'danger' : ($user['user_role'] === 'teacher' ? 'warning' : 'info') ?>">
												<?= esc(ucfirst($user['user_role'] ?? 'Unknown')) ?>
											</span>
										</td>
										<td><?= esc($user['last_login'] ?? 'Unknown') ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="3" class="text-center text-muted">No active users found.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php elseif (isset($role) && $role === 'teacher'): ?>
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
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Total Students</div>
							<div class="h4 mb-0"><?= isset($teacher['totalStudents']) ? esc($teacher['totalStudents']) : '0' ?></div>
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
	<?php elseif (isset($role) && $role === 'student'): ?>
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
								$enrollmentModel = new \App\Models\EnrollmentModel();
								$user_id = session('user_id');
								$enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
								echo count($enrolledCourses);
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
					if (!empty($enrolledCourses)):
						foreach ($enrolledCourses as $course):
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
									<small class="text-muted">
										Enrolled: <?= isset($course['enrollment_date']) ? date('M j, Y', strtotime($course['enrollment_date'])) : 'N/A' ?>
									</small>
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

						// Add course to enrolled courses section with captured data
						addCourseToEnrolled(courseId, courseTitle, courseDescription);

						// Update enrolled courses count
						updateEnrolledCoursesCount();
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

		function addCourseToEnrolled(courseId, courseTitle, courseDescription) {
			// Create enrolled course card with provided course data
			var enrolledCardHtml = `
				<div class="col-md-6 mb-3">
					<div class="card bg-secondary text-light border-0 h-100">
						<div class="card-body">
							<h6 class="card-title text-white mb-2">${courseTitle}</h6>
							<p class="card-text text-muted mb-3">${courseDescription}</p>
							<small class="text-muted">
								Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
							</small>
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
<?= $this->endSection() ?>
