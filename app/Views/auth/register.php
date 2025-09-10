<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Register</h2>

    <?php if (isset($validation)) : ?>
        <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('register') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="<?= set_value('name') ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="<?= set_value('email') ?>" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
        <a href="<?= base_url('login') ?>" class="btn btn-link">Login</a>
    </form>
</body>
</html>
