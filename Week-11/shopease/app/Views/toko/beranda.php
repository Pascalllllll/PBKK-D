<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<section class="hero p-4 p-md-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge rounded-pill text-bg-light text-success mb-3"><i class="bi bi-truck me-1"></i>Gratis ongkir mulai <?= rupiah(\App\Libraries\AlurPesanan::GRATIS_ONGKIR) ?></span>
            <h1 class="display-6 fw-bold mb-3">Perlengkapan kuliah dan kebutuhan harian, sekali checkout.</h1>
            <p class="lead mb-4">Pilih barang, transfer, unggah bukti. Pesanan diproses setelah pembayaran diverifikasi dan nomor resi bisa dipantau dari halaman pesanan.</p>
            <a href="<?= url_to('katalog') ?>" class="btn btn-light btn-lg fw-semibold px-4">Lihat Katalog <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="col-lg-5">
            <div class="row g-2 text-center">
                <?php foreach ([['bi-shield-check', 'Bayar aman', 'Diverifikasi manual'], ['bi-box-seam', 'Stok real-time', 'Dikunci saat checkout'], ['bi-geo-alt', 'Lacak resi', 'Dari halaman pesanan'], ['bi-arrow-counterclockwise', 'Bisa batal', 'Sebelum membayar']] as [$ikon, $judul, $ket]): ?>
                    <div class="col-6"><div class="p-3 rounded-4 h-100" style="background:rgba(255,255,255,.1)"><i class="bi <?= $ikon ?> fs-3"></i><div class="fw-semibold mt-1"><?= $judul ?></div><div class="small" style="color:#ccfbf1"><?= $ket ?></div></div></div>
                <?php endforeach ?>
            </div>
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
