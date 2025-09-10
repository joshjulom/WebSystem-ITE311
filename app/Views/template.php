<!DOCTYPE html>
<html>
<head>
    <title>My WebSystem</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid"> <!-- Full width -->
      <a class="navbar-brand" href="<?= base_url('/') ?>">MyCI</a>

      <!-- Toggler for mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/about') ?>">About</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a></li>
        </ul>
      </div>
    </div>
</nav>


    <div class="container">
    <?= $this->renderSection('content') ?>
  </div>

</body>
</html>
