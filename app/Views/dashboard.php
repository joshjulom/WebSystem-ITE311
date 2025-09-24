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
