<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SIAKAD') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <style>
        body { background: #f4f6fb; }
        .hero { background: linear-gradient(135deg, #224abe, #4e73df); color: #fff; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-md navbar-dark" style="background: #224abe">
    <div class="container">
        <a class="navbar-brand font-weight-bold" href="<?= site_url('/') ?>"><i class="fas fa-university mr-2"></i>SIAKAD</a>
        <div class="ml-auto">
            <?php if (session('logged_in')): ?>
                <a class="btn btn-light btn-sm" href="<?= site_url('dashboard') ?>">Dashboard</a>
            <?php else: ?>
                <a class="btn btn-light btn-sm" href="<?= site_url('login') ?>"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
            <?php endif ?>
        </div>
    </div>
</nav>

<main class="flex-grow-1"><?= $this->renderSection('content') ?></main>

<footer class="py-3 bg-white border-top text-center text-muted small">
    SIAKAD &middot; Pemrograman Berbasis Kerangka Kerja (D) &middot; CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>
</footer>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
