<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('nilai') ?>" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left fa-sm mr-1"></i>Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?= esc($mk['kode']) ?> &middot; <?= esc(tahun_ajaran()) ?> &middot; <?= count($peserta) ?> peserta</h6>
    </div>
    <div class="card-body">
        <?php if ($peserta): ?>
            <?= form_open('nilai/' . $mk['id']) ?>
            <table class="table table-bordered">
                <thead class="thead-light"><tr><th style="width: 50px">No</th><th>NRP</th><th>Nama</th><th style="width: 180px">Nilai</th></tr></thead>
                <?php foreach ($peserta as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($p['nrp']) ?></td>
                        <td><?= esc($p['nama']) ?></td>
                        <td>
                            <?= form_dropdown("nilai[{$p['id']}]", ['' => '- Belum -'] + array_combine(\App\Models\KrsModel::NILAI, \App\Models\KrsModel::NILAI), $p['nilai'] ?? '', ['class' => 'form-control form-control-sm', 'aria-label' => 'Nilai ' . $p['nama']]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan Nilai</button>
            <?= form_close() ?>
        <?php else: ?>
            <p class="text-muted mb-0">Belum ada mahasiswa yang mengambil matakuliah ini.</p>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
