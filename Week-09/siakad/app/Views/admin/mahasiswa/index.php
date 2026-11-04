<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/mahasiswa/new') ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm mr-1"></i>Tambah Mahasiswa</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Mahasiswa (<?= $pager->getTotal() ?>)</h6>
        <?= view('admin/_search', ['q' => $q, 'placeholder' => 'Cari NRP atau nama']) ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Foto</th><th>NRP</th><th>Nama</th><th>Prodi</th><th>Angkatan</th><th>Dosen Wali</th><th style="width: 100px">Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><img src="<?= foto_mahasiswa($r['foto']) ?>" class="rounded-circle foto-thumb" alt=""></td>
                        <td><?= esc($r['nrp']) ?></td>
                        <td><?= esc($r['nama']) ?></td>
                        <td><?= esc($r['prodi']) ?></td>
                        <td><?= esc($r['angkatan']) ?></td>
                        <td><?= esc($r['dosen_wali'] ?? '-') ?></td>
                        <td class="text-nowrap"><?= view('admin/_actions', ['uri' => 'mahasiswa', 'id' => $r['id'], 'label' => $r['nama']]) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $rows): ?>
                    <tr><td colspan="7" class="text-center text-muted">Tidak ada data.</td></tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3"><?= $pager->links('default', 'bootstrap') ?></div>
    </div>
</div>
<?= $this->endSection() ?>
