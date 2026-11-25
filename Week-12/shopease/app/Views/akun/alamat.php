<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <a href="<?= url_to('akun') ?>" class="small"><i class="bi bi-arrow-left"></i> Akun saya</a>
        <h1 class="h4 fw-bold mb-0 mt-1">Buku Alamat</h1>
        <div class="small text-secondary">Alamat utama otomatis terpilih saat checkout.</div>
    </div>
    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#alamatBaru"><i class="bi bi-plus-lg me-1"></i>Alamat baru</button>
</div>

<div class="collapse<?= session('form_alamat') || $alamat === [] ? ' show' : '' ?>" id="alamatBaru">
    <div class="kotak p-4 mb-4">
        <h2 class="h6 fw-bold mb-3">Alamat baru</h2>
        <?= form_open(url_to('akun.alamat.simpan'), ['novalidate' => true]) ?>
            <?= view('komponen/form_alamat', ['a' => null], ['saveData' => false]) ?>
            <button class="btn btn-primary btn-sm mt-3">Simpan alamat</button>
        <?= form_close() ?>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 g-3">
    <?php foreach ($alamat as $a): ?>
        <div class="col">
            <div class="kotak p-4 h-100 d-flex flex-column<?= $a['utama'] ? ' kartu-utama' : '' ?>">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge text-bg-light border"><?= esc($a['label']) ?></span>
                    <?php if ($a['utama']): ?><span class="badge text-bg-success"><i class="bi bi-check2 me-1"></i>Utama</span><?php endif ?>
                </div>
                <div class="fw-semibold"><?= esc($a['penerima']) ?> <span class="text-secondary fw-normal small"><?= esc($a['telepon']) ?></span></div>
                <div class="text-secondary small mb-3"><?= esc(\App\Models\AlamatModel::format($a)) ?></div>
                <div class="d-flex flex-wrap gap-2 mt-auto">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= url_to('akun.alamat.ubah', $a['id']) ?>"><i class="bi bi-pencil"></i> Ubah</a>
                    <?php if (! $a['utama']): ?>
                        <?= form_open(url_to('akun.alamat.utama', $a['id'])) ?><button class="btn btn-sm btn-outline-primary">Jadikan utama</button><?= form_close() ?>
                    <?php endif ?>
                    <?= form_open(url_to('akun.alamat.hapus', $a['id']), ['class' => 'ms-auto', 'data-confirm' => "Hapus alamat {$a['label']}?"]) ?>
                        <button class="btn btn-sm btn-link text-danger">Hapus</button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<?php if ($alamat === []): ?><p class="text-secondary">Belum ada alamat tersimpan.</p><?php endif ?>
<?= $this->endSection() ?>
