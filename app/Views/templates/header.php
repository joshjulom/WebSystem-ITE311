<nav class="navbar navbar-expand-lg">
	<div class="container-fluid">
		<a class="navbar-brand" href="<?= site_url('/') ?>">WebSystem</a>
		<button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
				aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav ms-auto">
				<?php if(session()->get('isLoggedIn')): ?>
					<?php if(session()->get('role') === 'admin'): ?>
						<li class="nav-item">
							<a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Admin Dashboard</a>
						</li>
					<?php endif; ?>
					<?php if(session()->get('role') === 'teacher'): ?>
						<li class="nav-item">
							<a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Teacher Dashboard</a>
						</li>
					<?php endif; ?>
					<?php if(session()->get('role') === 'student'): ?>
						<li class="nav-item">
							<a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= site_url('dashboard') ?>">Student Dashboard</a>
						</li>
					<?php endif; ?>
					<li class="nav-item">
						<a class="btn btn-primary ms-2" href="<?= site_url('logout') ?>">Logout</a>
					</li>
				<?php else: ?>
					<!-- Show Home, About, Contact, Login when not logged in -->
					<li class="nav-item">
						<a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?= uri_string() == 'about' ? 'active' : '' ?>" href="<?= site_url('about') ?>">About</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?= uri_string() == 'contact' ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a>
					</li>
					<li class="nav-item">
						<a class="btn btn-primary ms-2" href="<?= site_url('login') ?>">Login</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>
