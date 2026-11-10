<nav class="navbar navbar-expand-lg navbar-toko sticky-top py-2">
    <div class="container">
        <a class="navbar-brand brand fs-4" href="<?= url_to('beranda') ?>"><i class="bi bi-bag-heart-fill me-1"></i>Belanja<span>Ku</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navToko" aria-controls="navToko" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navToko">
            <ul class="navbar-nav me-3">
                <li class="nav-item"><a class="nav-link<?= menu_aktif('/', true) ?>" href="<?= url_to('beranda') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link<?= menu_aktif('/katalog') . menu_aktif('/produk') ?>" href="<?= url_to('katalog') ?>">Katalog</a></li>
                <li class="nav-item"><a class="nav-link<?= menu_aktif('/tentang') ?>" href="<?= url_to('tentang') ?>">Tentang</a></li>
                <li class="nav-item"><a class="nav-link<?= menu_aktif('/kontak') ?>" href="<?= url_to('kontak') ?>">Kontak</a></li>
            </ul>
            <form class="flex-grow-1 me-lg-3 my-2 my-lg-0" action="<?= url_to('katalog') ?>" method="get" role="search">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input class="form-control border-start-0" type="search" name="q" placeholder="Cari produk..." value="<?= esc(service('request')->getGet('q') ?? '') ?>" aria-label="Cari produk">
                </div>
            </form>
            <ul class="navbar-nav align-items-lg-center gap-lg-1">
                <?php if (session('role') === 'customer'): ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative<?= menu_aktif('/keranjang') ?>" href="<?= url_to('keranjang') ?>"><i class="bi bi-cart3 fs-5"></i>
                            <?php if ($n = jumlah_keranjang()): ?><span class="badge rounded-pill text-bg-danger position-absolute top-0 start-100 translate-middle-x"><?= $n ?></span><?php endif ?>
                            <span class="d-lg-none ms-1">Keranjang</span>
                        </a>
                    </li>
                <?php endif ?>
                <?php if (session('logged_in')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle me-1"></i><?= esc(user('nama')) ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (session('role') === 'customer'): ?>
                                <li><a class="dropdown-item" href="<?= url_to('pesanan') ?>"><i class="bi bi-receipt me-2"></i>Pesanan Saya</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="<?= url_to('panel') ?>"><i class="bi bi-speedometer2 me-2"></i>Panel</a></li>
                            <?php endif ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><?= form_open(url_to('keluar')) ?><button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button><?= form_close() ?></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url_to('masuk') ?>">Masuk</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm px-3" href="<?= url_to('daftar') ?>">Daftar</a></li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>
