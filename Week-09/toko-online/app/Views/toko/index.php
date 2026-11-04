<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<div class="row">
    <aside class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white font-weight-bold">Kategori</div>
            <div class="list-group list-group-flush">
                <a href="<?= site_url('/') . ($q !== '' ? '?q=' . urlencode($q) : '') ?>" class="list-group-item list-group-item-action<?= $kategoriId === 0 ? ' active' : '' ?>">Semua Produk</a>
                <?php foreach ($kategori as $k): ?>
                    <a href="?<?= http_build_query(array_filter(['q' => $q, 'kategori' => $k['id']])) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center<?= $kategoriId === (int) $k['id'] ? ' active' : '' ?>">
                        <span><i class="fas <?= esc($k['ikon']) ?> fa-fw mr-2"></i><?= esc($k['nama']) ?></span>
                        <span class="badge badge-pill badge-light"><?= $k['jumlah_produk'] ?></span>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </aside>

    <section class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">
                <?php $namaKategori = array_column($kategori, 'nama', 'id')[$kategoriId] ?? null; ?>
                <?= $q !== '' ? 'Hasil pencarian "' . esc($q) . '"' : esc($namaKategori ?? 'Semua Produk') ?>
            </h1>
            <small class="text-muted"><?= $pager->getTotal() ?> produk</small>
        </div>

        <div class="row">
            <?php foreach ($produk as $p): ?>
                <div class="col-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm produk-card">
                        <a href="<?= site_url('produk/' . $p['id']) ?>" class="text-reset">
                            <?= view('toko/_gambar', ['p' => $p, 'class' => 'card-img-top produk-img']) ?>
                        </a>
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted"><?= esc($p['kategori']) ?></small>
                            <a href="<?= site_url('produk/' . $p['id']) ?>" class="text-dark font-weight-bold"><?= esc($p['nama']) ?></a>
                            <div class="harga my-1"><?= rupiah($p['harga']) ?></div>
                            <small class="<?= $p['stok'] > 0 ? 'text-muted' : 'text-danger font-weight-bold' ?> mb-2"><?= $p['stok'] > 0 ? 'Stok ' . $p['stok'] : 'Stok habis' ?></small>
                            <?= form_open('keranjang/tambah', ['class' => 'mt-auto']) ?>
                                <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="qty" value="1">
                                <button class="btn btn-sm btn-primary btn-block"<?= $p['stok'] < 1 ? ' disabled' : '' ?>><i class="fas fa-cart-plus mr-1"></i>Keranjang</button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
            <?php if (! $produk): ?>
                <div class="col"><div class="alert alert-info">Produk tidak ditemukan.</div></div>
            <?php endif ?>
        </div>
        <?= $pager->links('default', 'bootstrap') ?>
    </section>
</div>
<?= $this->endSection() ?>
