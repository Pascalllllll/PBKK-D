<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row">
    <?= view('dashboard/_card', ['color' => 'primary', 'label' => 'Mahasiswa', 'value' => $mahasiswa, 'icon' => 'fa-user-graduate']) ?>
    <?= view('dashboard/_card', ['color' => 'success', 'label' => 'Dosen', 'value' => $dosen, 'icon' => 'fa-chalkboard-teacher']) ?>
    <?= view('dashboard/_card', ['color' => 'info', 'label' => 'Matakuliah', 'value' => $matakuliah, 'icon' => 'fa-book']) ?>
    <?= view('dashboard/_card', ['color' => 'warning', 'label' => 'KRS ' . tahun_ajaran(), 'value' => $krs, 'icon' => 'fa-list-alt']) ?>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Mahasiswa per Angkatan</h6></div>
            <div class="card-body">
                <?php foreach ($angkatan as $a): ?>
                    <?php $persen = $mahasiswa ? round($a['jumlah'] / $mahasiswa * 100) : 0 ?>
                    <h4 class="small font-weight-bold">Angkatan <?= esc($a['angkatan']) ?> <span class="float-right"><?= $a['jumlah'] ?> mahasiswa</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $persen ?>%" aria-valuenow="<?= $persen ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Mahasiswa Terbaru</h6>
                <a href="<?= site_url('admin/mahasiswa') ?>" class="small">Lihat semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <?php foreach ($terbaru as $m): ?>
                        <tr>
                            <td class="pl-4" style="width: 60px"><img src="<?= foto_mahasiswa($m['foto']) ?>" class="rounded-circle foto-thumb" alt=""></td>
                            <td><b><?= esc($m['nama']) ?></b><br><small class="text-muted"><?= esc($m['nrp']) ?></small></td>
                            <td class="text-right pr-4 align-middle"><?= esc($m['angkatan']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
