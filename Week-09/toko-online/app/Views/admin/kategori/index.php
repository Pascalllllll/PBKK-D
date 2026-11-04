<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/kategori/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm mr-1"></i>Tambah Kategori</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kategori (<?= $pager->getTotal() ?>)</h6>
        <?= view('admin/_search', ['q' => $q, 'placeholder' => 'Cari kategori']) ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light"><tr><th style="width: 70px">Ikon</th><th>Nama</th><th>Jumlah Produk</th><th style="width: 100px">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="text-center"><i class="fas <?= esc($r['ikon']) ?> fa-lg text-primary"></i></td>
                        <td><?= esc($r['nama']) ?></td>
                        <td><?= $r['jumlah_produk'] ?></td>
                        <td class="text-nowrap"><?= view('admin/_actions', ['uri' => 'kategori', 'id' => $r['id'], 'label' => $r['nama']]) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $rows): ?>
                    <tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3"><?= $pager->links('default', 'bootstrap') ?></div>
    </div>
</div>
<?= $this->endSection() ?>
