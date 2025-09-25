<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WebSystem</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Discord-Like Style -->
    <style>
        body {
            background-color: #2c2f33;
            font-family: 'Poppins', sans-serif;
            color: #dcddde;
        }

        .navbar {
            background-color: #23272a;
            padding: 1rem 2rem;
            border-bottom: 1px solid #1e2124;
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
            font-size: 1.4rem;
        }

        .nav-link {
            color: #b9bbbe !important;
            font-weight: 500;
            margin-right: 1rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #ffffff !important;
        }

        .nav-link.active {
            color: #5865F2 !important;
            font-weight: bold;
        }

        .container {
            background-color: #36393f;
            border-radius: 8px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        h1, h2, h3, h4, h5 {
            color: #ffffff;
        }

        .btn-primary {
            background-color: #5865F2;
            border-color: #5865F2;
        }

        .btn-primary:hover {
            background-color: #4752c4;
            border-color: #4752c4;
        }

        .form-control,
        .form-select {
            background-color: #202225;
            border: 1px solid #2f3136;
            color: #ffffff;
        }

        .form-control:focus {
            background-color: #202225;
            color: #ffffff;
            border-color: #5865F2;
            box-shadow: 0 0 0 0.2rem rgba(88, 101, 242, 0.25);
        }

        .text-muted {
            color: #b9bbbe !important;
        }

        a {
            color: #5865F2;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: #4752c4;
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<?= $this->include('templates/header') ?>

<!-- Page Content -->
<div class="container">
    <?= $this->renderSection('content') ?>
</div>

</body>
</html>
