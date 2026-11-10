<?php $admin = session('role') === 'admin'; ?>
<!doctype html>
<html lang="id">
<head>
    <?= $this->include('layouts/_head') ?>
</head>
<body>
<div class="panel-wrap">
<aside class="panel-sidebar p-3"><div class="isi">
    <a class="brand fs-4 text-decoration-none px-2 mb-3 d-block" href="<?= url_to('panel') ?>" style="color:#5eead4!important"><i class="bi bi-bag-heart-fill me-1"></i>Belanja<span>Ku</span></a>
    <nav class="nav flex-column gap-1">
        <a class="nav-link<?= menu_aktif('/panel', true) ?>" href="<?= url_to('panel') ?>"><i class="bi bi-speedometer2"></i>Dashboard</a>

        <div class="judul-menu">Transaksi</div>
        <a class="nav-link<?= menu_aktif('/panel/pesanan') ?>" href="<?= url_to('panel.pesanan') ?>"><i class="bi bi-receipt"></i>Pesanan</a>
        <?php if ($admin): ?>
            <a class="nav-link<?= menu_aktif('/panel/pembayaran') ?>" href="<?= url_to('panel.pembayaran') ?>"><i class="bi bi-cash-coin"></i>Verifikasi Bayar
                <?php if ($n = menunggu_verifikasi()): ?><span class="badge rounded-pill text-bg-warning ms-auto"><?= $n ?></span><?php endif ?>
            </a>
            <a class="nav-link<?= menu_aktif('/panel/laporan') ?>" href="<?= url_to('panel.laporan') ?>"><i class="bi bi-graph-up"></i>Laporan</a>

            <div class="judul-menu">Katalog</div>
            <a class="nav-link<?= menu_aktif('/panel/kategori') ?>" href="<?= url_to('panel.kategori') ?>"><i class="bi bi-tags"></i>Kategori</a>
            <a class="nav-link<?= menu_aktif('/panel/produk') ?>" href="<?= url_to('panel.produk') ?>"><i class="bi bi-box-seam"></i>Produk</a>
        <?php endif ?>

        <div class="judul-menu">Toko</div>
        <a class="nav-link" href="<?= url_to('beranda') ?>" target="_blank"><i class="bi bi-shop"></i>Lihat toko</a>
    </nav>
</div></aside>

<div class="panel-main d-flex flex-column">
    <header class="panel-topbar px-4 py-3 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h5 fw-bold mb-0"><?= esc($title) ?></h1>
            <?= $this->renderSection('subjudul') ?>
        </div>
        <div class="d-flex align-items-center gap-3">
            <?= $this->renderSection('aksi') ?>
            <div class="text-end small lh-sm">
                <div class="fw-semibold"><?= esc(user('nama')) ?></div>
                <span class="badge <?= $admin ? 'text-bg-primary' : 'text-bg-warning' ?>"><?= $admin ? 'Admin' : 'Staf Gudang' ?></span>
            </div>
            <?= form_open(url_to('keluar')) ?><button class="btn btn-sm btn-outline-danger" title="Keluar" aria-label="Keluar"><i class="bi bi-box-arrow-right"></i></button><?= form_close() ?>
        </div>
    </header>
    <main class="p-4 flex-grow-1">
        <?= $this->include('layouts/_alerts') ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
</div>
<?= $this->include('layouts/_scripts') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
