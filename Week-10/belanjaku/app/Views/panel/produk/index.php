<?= $this->extend('layouts/panel') ?>

<?= $this->section('aksi') ?><a href="<?= url_to('panel.produk.baru') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah</a><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stat p-0">
    <form class="p-3 row g-2" method="get">
        <div class="col-md-4"><input class="form-control" name="q" value="<?= esc($filter['q']) ?>" placeholder="Cari nama produk..." aria-label="Cari produk"></div>
        <div class="col-md-3">
            <select class="form-select" name="kategori" aria-label="Kategori">
                <option value="">Semua kategori</option>
                <?php foreach ($kategori as $id => $nama): ?><option value="<?= $id ?>" <?= $filter['kategori'] === $id ? 'selected' : '' ?>><?= esc($nama) ?></option><?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" name="status" aria-label="Status">
                <option value="">Aktif dan nonaktif</option>
                <option value="aktif" <?= $filter['status'] === 'aktif' ? 'selected' : '' ?>>Hanya aktif</option>
                <option value="nonaktif" <?= $filter['status'] === 'nonaktif' ? 'selected' : '' ?>>Hanya nonaktif</option>
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-outline-primary">Filter</button></div>
    </form>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Produk</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr class="<?= $r['aktif'] ? '' : 'opacity-50' ?>">
                    <td><div class="d-flex align-items-center gap-2"><img class="thumb" src="<?= gambar_produk($r['gambar']) ?>" alt=""><div><div class="fw-semibold"><?= esc($r['nama']) ?></div><code class="small"><?= esc($r['slug']) ?></code></div></div></td>
                    <td><?= esc($r['kategori']) ?></td>
                    <td class="text-end"><?= rupiah($r['harga']) ?></td>
                    <td class="text-center"><span class="<?= $r['stok'] < 5 ? 'text-danger fw-bold' : '' ?>"><?= $r['stok'] ?></span></td>
                    <td><?= $r['aktif'] ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>' ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url_to('panel.produk.ubah', $r['id']) ?>"><i class="bi bi-pencil"></i></a>
                        <?= form_open(url_to('panel.produk.hapus', $r['id']), ['class' => 'd-inline', 'data-confirm' => "Hapus produk {$r['nama']}?"]) ?>
                            <button class="btn btn-sm btn-outline-danger" aria-label="Hapus"><i class="bi bi-trash"></i></button>
                        <?= form_close() ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if ($rows === []): ?><tr><td colspan="6" class="text-center text-secondary py-4">Tidak ada data.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
    <div class="p-3 d-flex justify-content-between align-items-center">
        <span class="small text-secondary"><?= $pager->getTotal() ?> produk</span>
        <?= $pager->links('default', 'bs5') ?>
    </div>
</div>
<?= $this->endSection() ?>
