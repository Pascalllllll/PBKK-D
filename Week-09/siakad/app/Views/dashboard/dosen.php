<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $peserta = array_sum(array_column($matakuliah, 'peserta')); $belum = array_sum(array_column($matakuliah, 'belum_dinilai')); ?>
<div class="row">
    <?= view('dashboard/_card', ['color' => 'primary', 'label' => 'Matakuliah Diampu', 'value' => count($matakuliah), 'icon' => 'fa-book']) ?>
    <?= view('dashboard/_card', ['color' => 'success', 'label' => 'Total Peserta', 'value' => $peserta, 'icon' => 'fa-users']) ?>
    <?= view('dashboard/_card', ['color' => 'warning', 'label' => 'Belum Dinilai', 'value' => $belum, 'icon' => 'fa-hourglass-half']) ?>
    <?= view('dashboard/_card', ['color' => 'info', 'label' => 'Mahasiswa Perwalian', 'value' => count($perwalian), 'icon' => 'fa-user-friends']) ?>
</div>

<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Matakuliah Semester Ini</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th class="pl-4">Matakuliah</th><th>Peserta</th><th></th></tr></thead>
                    <?php foreach ($matakuliah as $mk): ?>
                        <tr>
                            <td class="pl-4"><b><?= esc($mk['nama']) ?></b><br><small class="text-muted"><?= esc($mk['kode']) ?> &middot; <?= $mk['sks'] ?> SKS</small></td>
                            <td class="align-middle"><?= $mk['peserta'] ?></td>
                            <td class="align-middle text-right pr-4"><a href="<?= site_url('nilai/' . $mk['id']) ?>" class="btn btn-sm btn-primary">Input Nilai</a></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Mahasiswa Perwalian</h6></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($perwalian as $m): ?>
                    <li class="list-group-item d-flex align-items-center">
                        <img src="<?= foto_mahasiswa($m['foto']) ?>" class="rounded-circle foto-thumb mr-3" alt="">
                        <div><?= esc($m['nama']) ?><br><small class="text-muted"><?= esc($m['nrp']) ?></small></div>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
