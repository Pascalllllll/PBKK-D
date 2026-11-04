<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row">
    <?php foreach ($matakuliah as $mk): ?>
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?= esc($mk['kode']) ?> &middot; <?= $mk['sks'] ?> SKS</div>
                    <h5 class="text-gray-800"><?= esc($mk['nama']) ?></h5>
                    <p class="mb-3"><?= $mk['peserta'] ?> peserta &middot;
                        <?php if ($mk['belum_dinilai'] > 0): ?>
                            <span class="text-warning"><?= $mk['belum_dinilai'] ?> belum dinilai</span>
                        <?php else: ?>
                            <span class="text-success">semua sudah dinilai</span>
                        <?php endif ?>
                    </p>
                    <a href="<?= site_url('nilai/' . $mk['id']) ?>" class="btn btn-primary btn-sm">Input Nilai</a>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <?php if (! $matakuliah): ?>
        <div class="col"><div class="alert alert-info">Anda belum mengampu matakuliah.</div></div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
