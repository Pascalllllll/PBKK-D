<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/produk/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm mr-1"></i>Tambah Produk</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Produk (<?= $pager->getTotal() ?>)</h6>
        <form method="get" class="form-inline">
            <?= form_dropdown('kategori', ['' => 'Semua kategori'] + $kategori, $kategoriId ?: '', ['class' => 'form-control form-control-sm mr-2', 'aria-label' => 'Kategori', 'onchange' => 'this.form.submit()']) ?>
            <div class="input-group input-group-sm">
                <input type="search" name="q" class="form-control" placeholder="Cari nama produk" value="<?= esc($q) ?>" aria-label="Cari">
                <div class="input-group-append"><button class="btn btn-primary"><i class="fas fa-search fa-sm"></i></button></div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light"><tr><th style="width: 70px">Gambar</th><th>Nama</th><th>Kategori</th><th class="text-right">Harga</th><th class="text-right">Stok</th><th style="width: 100px">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= view('toko/_gambar', ['p' => $r, 'class' => 'thumb', 'size' => 'fa-lg']) ?></td>
                        <td class="align-middle"><?= esc($r['nama']) ?></td>
                        <td class="align-middle"><?= esc($r['kategori']) ?></td>
                        <td class="align-middle text-right"><?= rupiah($r['harga']) ?></td>
                        <td class="align-middle text-right<?= $r['stok'] < 5 ? ' text-danger font-weight-bold' : '' ?>"><?= $r['stok'] ?></td>
                        <td class="align-middle text-nowrap"><?= view('admin/_actions', ['uri' => 'produk', 'id' => $r['id'], 'label' => $r['nama']]) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $rows): ?>
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada data.</td></tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3"><?= $pager->links('default', 'bootstrap') ?></div>
    </div>
</div>
<?= $this->endSection() ?>
