<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<section class="hero p-4 p-md-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge rounded-pill text-bg-light text-success mb-3"><i class="bi bi-truck me-1"></i><?= info_ongkir() ?></span>
            <h1 class="display-6 fw-bold mb-3">Perlengkapan kuliah dan kebutuhan harian, sekali checkout.</h1>
            <p class="lead mb-4">Pilih barang, transfer, unggah bukti. Pesanan diproses setelah pembayaran diverifikasi dan nomor resi bisa dipantau dari halaman pesanan.</p>
            <a href="<?= url_to('katalog') ?>" class="btn btn-light btn-lg fw-semibold px-4">Lihat Katalog <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="col-lg-5">
            <?php if ($unggulan): ?>
                <div id="unggulan" class="carousel slide carousel-unggulan" data-bs-ride="carousel" data-bs-interval="4500">
                    <div class="carousel-indicators">
                        <?php foreach ($unggulan as $i => $u): ?>
                            <button type="button" data-bs-target="#unggulan" data-bs-slide-to="<?= $i ?>" <?= $i ? '' : 'class="active" aria-current="true"' ?> aria-label="<?= esc($u['nama']) ?>"></button>
                        <?php endforeach ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($unggulan as $i => $u): ?>
                            <div class="carousel-item<?= $i ? '' : ' active' ?>">
                                <a class="d-block text-decoration-none" href="<?= url_to('produk.detail', $u['slug']) ?>">
                                    <img src="<?= gambar_produk($u['gambar']) ?>" alt="<?= esc($u['nama']) ?>">
                                    <div class="keterangan">
                                        <span class="small text-uppercase opacity-75"><?= esc($u['kategori']) ?> · Unggulan</span>
                                        <span class="d-block fw-bold fs-5"><?= esc($u['nama']) ?></span>
                                        <span class="harga-hero"><?= rupiah($u['harga']) ?></span>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#unggulan" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Sebelumnya</span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#unggulan" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Berikutnya</span></button>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>

<section class="mb-5">
    <h2 class="h5 fw-bold mb-3">Belanja per kategori</h2>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($kategori as $k): ?>
            <a class="chip-kategori" href="<?= url_to('katalog') ?>?kategori=<?= $k['id'] ?>"><?= esc($k['nama']) ?> <span class="opacity-75">(<?= $k['jumlah_aktif'] ?>)</span></a>
        <?php endforeach ?>
    </div>
</section>

<?php if ($terlaris): ?>
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h5 fw-bold mb-0"><i class="bi bi-fire text-danger me-1"></i>Paling laris</h2>
    </div>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php foreach ($terlaris as $p): ?><div class="col"><?= view('komponen/kartu_produk', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
</section>
<?php endif ?>

<section>
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h5 fw-bold mb-0">Baru masuk</h2>
        <a href="<?= url_to('katalog') ?>" class="small fw-semibold">Semua produk <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php foreach ($terbaru as $p): ?><div class="col"><?= view('komponen/kartu_produk', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
</section>
<?= $this->endSection() ?>
