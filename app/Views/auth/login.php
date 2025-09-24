<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">

        <div class="text-center mb-4">
            <h1 class="fw-bold text-light">Welcome Back</h1>
            <p class="text-muted small">Login to continue to your account</p>
        </div>

        <?php if (session()->getFlashdata('register_success')): ?>
            <div class="alert alert-success shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= esc(session()->getFlashdata('register_success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0 rounded-4 bg-gradient text-light" style="background: linear-gradient(135deg, #1d2b64, #f8cdda);">
            <div class="card-body p-5">
                <form action="<?= base_url('login') ?>" method="post">
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
                    <button type="submit" class="btn btn-light w-100 fw-bold py-2 shadow-sm rounded-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center mt-4 text-muted small">
            Don't have an account? 
            <a href="<?= base_url('register') ?>" class="fw-bold text-decoration-none">Register</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
