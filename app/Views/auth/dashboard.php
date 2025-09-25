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
		<div class="card shadow-sm border-0 bg-dark text-light mb-4">
			<div class="card-body">
				<h5 class="card-title text-white">Student Overview</h5>
				<p class="mb-2">Your learning progress and recent courses.</p>
				<div class="row g-3">
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Total Enrolled</div>
							<div class="h4 mb-0"><?= isset($student['totalEnrolled']) ? esc($student['totalEnrolled']) : '0' ?></div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted">Completed</div>
							<div class="h4 mb-0"><?= isset($student['totalCompleted']) ? esc($student['totalCompleted']) : '0' ?></div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="p-3 bg-secondary rounded">
							<div class="text-muted mb-2">Recent Courses</div>
							<ul class="mb-0">
								<?php if (!empty($student['myCourses'])): ?>
									<?php foreach ($student['myCourses'] as $c): ?>
										<li><?= esc($c['title'] ?? 'Untitled Course') ?></li>
									<?php endforeach; ?>
								<?php else: ?>
									<li class="text-muted">No recent courses.</li>
								<?php endif; ?>
							</ul>
						</div>
					</div>
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
<?= $this->endSection() ?>
