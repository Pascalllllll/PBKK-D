<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent px-0">
        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('/') ?>?kategori=<?= $produk['kategori_id'] ?>"><?= esc($produk['kategori']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= esc($produk['nama']) ?></li>
    </ol>
</nav>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5 mb-3">
                <?= view('toko/_gambar', ['p' => $produk, 'class' => 'produk-img rounded', 'size' => 'fa-7x']) ?>
            </div>
            <div class="col-md-7">
                <span class="badge badge-light"><i class="fas <?= esc($produk['ikon']) ?> mr-1"></i><?= esc($produk['kategori']) ?></span>
                <h1 class="h3 mt-2"><?= esc($produk['nama']) ?></h1>
                <div class="harga h3 my-3"><?= rupiah($produk['harga']) ?></div>
                <p><?= nl2br(esc($produk['deskripsi'])) ?></p>
                <p class="<?= $produk['stok'] > 0 ? 'text-success' : 'text-danger' ?> font-weight-bold">
                    <?= $produk['stok'] > 0 ? "Stok tersedia: {$produk['stok']}" : 'Stok habis' ?>
                </p>
                <?php if ($produk['stok'] > 0): ?>
                    <?= form_open('keranjang/tambah', ['class' => 'form-inline']) ?>
                        <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
                        <label class="mr-2" for="qty">Jumlah</label>
                        <input type="number" class="form-control mr-2" id="qty" name="qty" value="1" min="1" max="<?= $produk['stok'] ?>" style="width: 90px">
                        <button class="btn btn-primary"><i class="fas fa-cart-plus mr-1"></i>Tambah ke Keranjang</button>
                    <?= form_close() ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<?php if ($lain): ?>
    <h2 class="h5 mb-3">Produk lain di <?= esc($produk['kategori']) ?></h2>
    <div class="row">
        <?php foreach ($lain as $p): ?>
            <div class="col-6 col-md-3 mb-4">
                <a href="<?= site_url('produk/' . $p['id']) ?>" class="card h-100 border-0 shadow-sm produk-card text-reset text-decoration-none">
                    <?= view('toko/_gambar', ['p' => $p, 'class' => 'card-img-top produk-img', 'size' => 'fa-3x']) ?>
                    <div class="card-body p-2">
                        <div class="small font-weight-bold"><?= esc($p['nama']) ?></div>
                        <div class="harga small"><?= rupiah($p['harga']) ?></div>
                    </div>
                </a>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
