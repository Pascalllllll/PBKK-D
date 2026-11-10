<?php /** @var array $p produk dari ProdukModel::cari() */ ?>
<a href="<?= url_to('produk.detail', $p['slug']) ?>" class="kartu-produk d-block h-100 text-decoration-none text-body">
    <img src="<?= gambar_produk($p['gambar']) ?>" alt="<?= esc($p['nama']) ?>" loading="lazy">
    <div class="p-3">
        <div class="small text-secondary mb-1"><?= esc($p['kategori']) ?></div>
        <div class="nama mb-2"><?= esc($p['nama']) ?></div>
        <div class="d-flex justify-content-between align-items-end">
            <span class="harga"><?= rupiah($p['harga']) ?></span>
            <?php if (isset($p['terjual'])): ?>
                <span class="small text-secondary"><?= (int) $p['terjual'] ?> terjual</span>
            <?php elseif ($p['stok'] < 1): ?>
                <span class="badge text-bg-secondary">Habis</span>
            <?php elseif ($p['stok'] < 5): ?>
                <span class="small text-danger">Sisa <?= $p['stok'] ?></span>
            <?php endif ?>
        </div>
    </div>
</a>
