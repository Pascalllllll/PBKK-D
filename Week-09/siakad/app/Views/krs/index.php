<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-xl-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">KRS <?= esc(tahun_ajaran()) ?></h6>
                <span class="badge badge-<?= $totalSks > 20 ? 'warning' : 'primary' ?> p-2">Total <?= $totalSks ?> / <?= \App\Models\KrsModel::MAX_SKS ?> SKS</span>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th class="pl-4">Kode</th><th>Matakuliah</th><th>SKS</th><th>Dosen</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($krs as $k): ?>
                        <tr>
                            <td class="pl-4"><?= esc($k['kode']) ?></td>
                            <td><?= esc($k['nama']) ?></td>
                            <td><?= $k['sks'] ?></td>
                            <td class="small"><?= esc($k['dosen'] ?? '-') ?></td>
                            <td class="text-right pr-3">
                                <?php if ($k['nilai'] === null): ?>
                                    <?= form_open("krs/{$k['id']}/delete", ['data-confirm' => "Batalkan {$k['nama']}?"]) ?>
                                        <button class="btn btn-sm btn-outline-danger">Batal</button>
                                    <?= form_close() ?>
                                <?php else: ?>
                                    <span class="badge badge-success">Nilai <?= esc($k['nilai']) ?></span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    <?php if (! $krs): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada matakuliah. Pilih dari daftar di samping.</td></tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Matakuliah Tersedia</h6></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($tersedia as $mk): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <b><?= esc($mk['nama']) ?></b><br>
                            <small class="text-muted"><?= esc($mk['kode']) ?> &middot; Semester <?= $mk['semester'] ?> &middot; <?= $mk['sks'] ?> SKS</small>
                        </div>
                        <?= form_open('krs') ?>
                            <input type="hidden" name="matakuliah_id" value="<?= $mk['id'] ?>">
                            <button class="btn btn-sm btn-primary"><i class="fas fa-plus fa-sm mr-1"></i>Ambil</button>
                        <?= form_close() ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
