<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center mt-4">
    <div class="col-md-6 col-lg-5">
        <h1 class="text-center mb-4 text-light">Sign In</h1>

        <?php if (session()->getFlashdata('register_success')): ?>
            <div class="alert alert-success" role="alert">
                <?= esc(session()->getFlashdata('register_success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('login_error')): ?>
            <div class="alert alert-danger" role="alert">
                <?= esc(session()->getFlashdata('login_error')) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0" style="background-color: #202225;">
            <div class="card-body p-4">
                <form action="<?= base_url('login') ?>" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">Email</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               required 
                               value="<?= esc(old('email')) ?>"
                               style="background-color: #2f3136; color: #fff; border: 1px solid #444;">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-light">Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required
                               style="background-color: #2f3136; color: #fff; border: 1px solid #444;">
                    </div>
                    <button type="submit" class="btn w-100" style="background-color: #5865F2; color: #fff;">Login</button>
                </form>
            </div>
        </div>

        <p class="text-center mt-3 text-muted small">
            Don't have an account? 
            <a href="<?= base_url('register') ?>" style="color: #5865F2;">Register</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
