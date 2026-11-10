<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= url_to('katalog') ?>">Katalog</a></li>
        <li class="breadcrumb-item"><a href="<?= url_to('katalog') ?>?kategori=<?= $produk['kategori_id'] ?>"><?= esc($produk['kategori']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($produk['nama']) ?></li>
    </ol>
</nav>

<div class="kotak p-3 p-md-4 mb-4">
    <div class="row g-4">
        <div class="col-md-5"><img class="detail-img" src="<?= gambar_produk($produk['gambar']) ?>" alt="<?= esc($produk['nama']) ?>"></div>
        <div class="col-md-7">
            <span class="badge text-bg-light border mb-2"><?= esc($produk['kategori']) ?></span>
            <h1 class="h3 fw-bold"><?= esc($produk['nama']) ?></h1>
            <div class="harga fs-2 mb-3"><?= rupiah($produk['harga']) ?></div>
            <dl class="row small mb-3">
                <dt class="col-4 col-lg-3 text-secondary fw-normal">Stok</dt>
                <dd class="col-8 col-lg-9"><?= $produk['stok'] > 0 ? $produk['stok'] . ' tersedia' : '<span class="text-danger">Habis</span>' ?></dd>
                <dt class="col-4 col-lg-3 text-secondary fw-normal">Berat</dt>
                <dd class="col-8 col-lg-9"><?= number_format($produk['berat'], 0, ',', '.') ?> gram</dd>
            </dl>
            <p class="text-secondary" style="white-space:pre-line"><?= esc($produk['deskripsi']) ?></p>

            <?php if (session('role') === 'customer'): ?>
                <?php if ($produk['stok'] > 0): ?>
                    <?= form_open(url_to('keranjang.tambah'), ['class' => 'd-flex gap-2 align-items-center']) ?>
                        <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
                        <label class="visually-hidden" for="qty">Jumlah</label>
                        <input class="form-control" style="width:6rem" type="number" id="qty" name="qty" value="1" min="1" max="<?= $produk['stok'] ?>">
                        <button class="btn btn-primary px-4"><i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang</button>
                    <?= form_close() ?>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>Stok habis</button>
                <?php endif ?>
            <?php elseif (! session('logged_in')): ?>
                <a class="btn btn-primary px-4" href="<?= url_to('masuk') ?>"><i class="bi bi-box-arrow-in-right me-1"></i>Masuk untuk membeli</a>
            <?php else: ?>
                <div class="alert alert-info small mb-0">Anda masuk sebagai <strong><?= esc(session('role')) ?></strong>. Keranjang hanya untuk akun pelanggan.</div>
            <?php endif ?>
        </div>
    </div>
</div>

<?php if ($serupa): ?>
    <h2 class="h5 fw-bold mb-3">Produk serupa</h2>
    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php foreach ($serupa as $p): ?><div class="col"><?= view('komponen/kartu_produk', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
