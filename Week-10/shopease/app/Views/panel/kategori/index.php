<?= $this->extend('layouts/panel') ?>

<?= $this->section('aksi') ?><a href="<?= url_to('panel.kategori.baru') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah</a><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stat p-0">
    <form class="p-3 d-flex gap-2" method="get">
        <input class="form-control" style="max-width:20rem" name="q" value="<?= esc($q) ?>" placeholder="Cari kategori..." aria-label="Cari kategori">
        <button class="btn btn-outline-primary">Cari</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Nama</th><th>Slug</th><th>Deskripsi</th><th class="text-center">Produk</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($r['nama']) ?></td>
                    <td><code><?= esc($r['slug']) ?></code></td>
                    <td class="small text-secondary"><?= esc($r['deskripsi'] ?? '-') ?></td>
                    <td class="text-center"><?= $r['jumlah_produk'] ?> <span class="small text-secondary">(<?= $r['jumlah_aktif'] ?> aktif)</span></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url_to('panel.kategori.ubah', $r['id']) ?>"><i class="bi bi-pencil"></i> Ubah</a>
                        <?= form_open(url_to('panel.kategori.hapus', $r['id']), ['class' => 'd-inline', 'data-confirm' => "Hapus kategori {$r['nama']}?"]) ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                        <?= form_close() ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if ($rows === []): ?><tr><td colspan="5" class="text-center text-secondary py-4">Tidak ada data.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
    <div class="p-3"><?= $pager->links('default', 'bs5') ?></div>
</div>
<?= $this->endSection() ?>
