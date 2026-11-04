<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row">
    <?= view('dashboard/_card', ['color' => 'primary', 'label' => 'IPK', 'value' => number_format($ipk['ip'], 2), 'icon' => 'fa-graduation-cap']) ?>
    <?= view('dashboard/_card', ['color' => 'success', 'label' => 'Total SKS Dinilai', 'value' => $ipk['sks'], 'icon' => 'fa-check-circle']) ?>
</div>

<?php foreach (array_reverse($semester, true) as $ta => $rows): ?>
    <?php $ip = hitung_ip($rows); ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><?= esc($ta) ?></h6>
            <span>IP Semester: <b><?= $ip['sks'] ? number_format($ip['ip'], 2) : '-' ?></b></span>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="thead-light"><tr><th class="pl-4">Kode</th><th>Matakuliah</th><th>SKS</th><th>Nilai</th><th>Bobot</th><th>Mutu</th></tr></thead>
                <?php foreach ($rows as $r): ?>
                    <?php $bobot = bobot_nilai($r['nilai']); ?>
                    <tr>
                        <td class="pl-4"><?= esc($r['kode']) ?></td>
                        <td><?= esc($r['nama']) ?></td>
                        <td><?= $r['sks'] ?></td>
                        <td><?= $r['nilai'] ? '<b>' . esc($r['nilai']) . '</b>' : '<span class="text-muted">Belum dinilai</span>' ?></td>
                        <td><?= $bobot === null ? '-' : number_format($bobot, 1) ?></td>
                        <td><?= $bobot === null ? '-' : number_format($bobot * $r['sks'], 1) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
<?php endforeach ?>
<?php if (! $semester): ?>
    <div class="alert alert-info">Belum ada riwayat studi.</div>
<?php endif ?>
<p class="small text-muted">Bobot: A=4, AB=3,5, B=3, BC=2,5, C=2, D=1, E=0. Matakuliah yang belum dinilai tidak ikut dihitung.</p>
<?= $this->endSection() ?>
