<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-7 col-lg-6">

        <div class="text-center mb-4">
            <h1 class="fw-bold text-light">Create Your Account</h1>
            <p class="text-muted small">Join us and start your journey today</p>
        </div>

        <?php if (session()->getFlashdata('register_error')): ?>
            <div class="alert alert-danger shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= esc(session()->getFlashdata('register_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0 rounded-4 bg-gradient text-light" 
             style="background: linear-gradient(135deg, #283e51, #485563);">
            <div class="card-body p-5">
                <form action="<?= base_url('register') ?>" method="post">
                    
                    <div class="mb-4">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-light border-0">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" class="form-control border-0 shadow-sm"
                                   id="name" name="name" required
                                   placeholder="John Doe"
                                   value="<?= esc(old('name')) ?>">
                        </div>

					<div class="mb-4">
						<label for="role" class="form-label">Role</label>
						<div class="input-group">
							<span class="input-group-text bg-dark text-light border-0">
								<i class="bi bi-people-fill"></i>
							</span>
							<select class="form-control border-0 shadow-sm" id="role" name="role" required>
								<option value="student" <?= old('role') === 'student' ? 'selected' : '' ?>>Student</option>
								<option value="teacher" <?= old('role') === 'teacher' ? 'selected' : '' ?>>Instructor</option>
								<option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
							</select>
						</div>
					</div>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-light border-0">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                            <input type="email" class="form-control border-0 shadow-sm"
                                   id="email" name="email" required
                                   placeholder="you@example.com"
                                   value="<?= esc(old('email')) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-light border-0">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" class="form-control border-0 shadow-sm"
                                   id="password" name="password" required
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-light border-0">
                                <i class="bi bi-shield-lock-fill"></i>
                            </span>
                            <input type="password" class="form-control border-0 shadow-sm"
                                   id="password_confirm" name="password_confirm" required
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-light w-100 fw-bold py-2 shadow-sm rounded-3">
                        <i class="bi bi-person-plus-fill me-2"></i> Create Account
                    </button>
                </form>

                <!-- Already have an account? -->
                <div class="text-center mt-4">
                    <p class="mb-0 text-muted small">
                        Already have an account?
                        <a href="<?= base_url('login') ?>" class="fw-bold text-decoration-none">Log in here</a>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
