<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row">
    <?= view('dashboard/_card', ['color' => 'primary', 'label' => 'IPK', 'value' => number_format($ipk['ip'], 2), 'icon' => 'fa-graduation-cap']) ?>
    <?= view('dashboard/_card', ['color' => 'success', 'label' => 'SKS Lulus', 'value' => $ipk['sks'], 'icon' => 'fa-check-circle']) ?>
    <?= view('dashboard/_card', ['color' => 'info', 'label' => 'SKS Diambil Semester Ini', 'value' => $sksKrs . ' / 24', 'icon' => 'fa-list-alt']) ?>
    <?= view('dashboard/_card', ['color' => 'warning', 'label' => 'Angkatan', 'value' => $mhs['angkatan'], 'icon' => 'fa-calendar']) ?>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-body text-center">
                <img src="<?= foto_mahasiswa($mhs['foto']) ?>" class="img-thumbnail foto-preview mb-3" alt="Foto <?= esc($mhs['nama']) ?>">
                <h5 class="font-weight-bold text-gray-800 mb-0"><?= esc($mhs['nama']) ?></h5>
                <p class="text-muted mb-2"><?= esc($mhs['nrp']) ?></p>
                <p class="small mb-0"><?= esc($mhs['prodi']) ?><br>Dosen wali: <?= esc($mhs['dosen_wali'] ?? '-') ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">KRS <?= esc(tahun_ajaran()) ?></h6>
                <a href="<?= site_url('krs') ?>" class="small">Kelola KRS &rarr;</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th class="pl-4">Kode</th><th>Matakuliah</th><th>SKS</th><th>Dosen</th></tr></thead>
                    <?php foreach ($krs as $k): ?>
                        <tr><td class="pl-4"><?= esc($k['kode']) ?></td><td><?= esc($k['nama']) ?></td><td><?= $k['sks'] ?></td><td><?= esc($k['dosen'] ?? '-') ?></td></tr>
                    <?php endforeach ?>
                    <?php if (! $krs): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum mengisi KRS.</td></tr>
                    <?php endif ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
