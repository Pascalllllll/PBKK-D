<?php $path = '/' . trim(service('request')->getUri()->getPath(), '/'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Toko Online') ?> | Toko Online</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <style>
        body { background: #f5f6f8; }
        .produk-img { aspect-ratio: 1; object-fit: cover; width: 100%; }
        .produk-ikon { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e8edfb, #d4dcf5); color: #4e73df; }
        .produk-card { transition: box-shadow .15s; }
        .produk-card:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important; }
        .thumb { width: 64px; height: 64px; object-fit: cover; border-radius: .25rem; }
        .thumb-ikon { width: 64px; height: 64px; display: inline-flex; align-items: center; justify-content: center; background: #e8edfb; color: #4e73df; border-radius: .25rem; }
        .harga { color: #e3342f; font-weight: 700; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-md navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand font-weight-bold" href="<?= site_url('/') ?>"><i class="fas fa-store mr-2"></i>Toko Online</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <form class="form-inline my-2 my-md-0 mx-md-4 flex-grow-1" action="<?= site_url('/') ?>" method="get">
                <input class="form-control form-control-sm w-100" type="search" name="q" placeholder="Cari produk..." value="<?= esc(service('request')->getGet('q') ?? '') ?>" aria-label="Cari produk">
            </form>
            <ul class="navbar-nav ml-auto align-items-md-center">
                <li class="nav-item<?= $path === '/keranjang' ? ' active' : '' ?>">
                    <a class="nav-link" href="<?= site_url('keranjang') ?>"><i class="fas fa-shopping-cart mr-1"></i>Keranjang
                        <?php if ($n = jumlah_keranjang()): ?><span class="badge badge-warning"><?= $n ?></span><?php endif ?>
                    </a>
                </li>
                <?php if (session('logged_in')): ?>
                    <?php if (session('role') === 'customer'): ?>
                        <li class="nav-item<?= str_starts_with($path, '/pesanan') ? ' active' : '' ?>"><a class="nav-link" href="<?= site_url('pesanan') ?>"><i class="fas fa-receipt mr-1"></i>Pesanan</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= site_url('admin') ?>"><i class="fas fa-cog mr-1"></i>Admin</a></li>
                    <?php endif ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="akun" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-circle mr-1"></i><?= esc(user('nama')) ?></a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="akun">
                            <?= form_open('logout', ['class' => 'px-3']) ?><button class="btn btn-sm btn-outline-danger btn-block">Logout</button><?= form_close() ?>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm ml-md-2" href="<?= site_url('register') ?>">Daftar</a></li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1 py-4">
    <div class="container">
        <?= $this->include('layouts/_alerts') ?>
        <?= $this->renderSection('content') ?>
    </div>
</main>

<footer class="py-3 bg-white border-top text-center text-muted small">
    Toko Online &middot; Pemrograman Berbasis Kerangka Kerja (D) &middot; CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>
</footer>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) { if (!confirm(form.dataset.confirm)) e.preventDefault(); });
    });
</script>
</body>
</html>
