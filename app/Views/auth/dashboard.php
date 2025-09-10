<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Welcome, <?= session()->get('name') ?>!</h2>
    <p>Email: <?= session()->get('email') ?></p>
    <p>Role: <?= session()->get('role') ?></p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <a href="<?= base_url('logout') ?>" class="btn btn-danger">Logout</a>
</body>
</html>
