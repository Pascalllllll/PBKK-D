<?= $this->extend('layouts/panel') ?>

<?= $this->section('aksi') ?><a href="<?= url_to('panel.log') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-clock-history me-1"></i>Riwayat masuk</a><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <?php foreach (['customer' => ['bi-people', '#ede9fe', '#6d28d9'], 'gudang' => ['bi-box-seam', '#fef3c7', '#b45309'], 'admin' => ['bi-shield-lock', '#ccfbf1', '#0f766e']] as $role => [$ikon, $bg, $fg]): ?>
        <?php $n = $ringkasan[$role] ?? []; ?>
        <div class="col-md-4">
            <div class="stat h-100 d-flex gap-3">
                <div class="ikon" style="background:<?= $bg ?>;color:<?= $fg ?>"><i class="bi <?= $ikon ?>"></i></div>
                <div>
                    <div class="small text-secondary"><?= label_role($role) ?></div>
                    <div class="angka"><?= ($n[1] ?? 0) ?></div>
                    <div class="small text-secondary"><?= ($n[0] ?? 0) ? ($n[0] . ' dibekukan') : 'semua aktif' ?></div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<div class="stat p-0">
    <form class="p-3 d-flex flex-wrap gap-2" method="get">
        <input class="form-control" style="max-width:18rem" name="q" value="<?= esc($filter['q']) ?>" placeholder="Cari nama atau email..." aria-label="Cari pengguna">
        <select class="form-select" style="max-width:11rem" name="role" aria-label="Role">
            <option value="">Semua role</option>
            <?php foreach (config('Akun')->role as $kode => $teks): ?>
                <option value="<?= $kode ?>" <?= $filter['role'] === $kode ? 'selected' : '' ?>><?= $teks ?></option>
            <?php endforeach ?>
        </select>
        <select class="form-select" style="max-width:10rem" name="status" aria-label="Status">
            <option value="">Semua status</option>
            <option value="1" <?= $filter['status'] === '1' ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= $filter['status'] === '0' ? 'selected' : '' ?>>Dibekukan</option>
        </select>
        <button class="btn btn-outline-primary">Terapkan</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Pengguna</th><th>Role</th><th>Status</th><th>Login terakhir</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <?php $diriSendiri = (int) $r['id'] === user('id'); ?>
                <tr class="<?= $r['aktif'] ? '' : 'baris-beku' ?>">
                    <td>
                        <div class="fw-semibold"><?= esc($r['nama']) ?><?= $diriSendiri ? ' <span class="small text-secondary fw-normal">(Anda)</span>' : '' ?></div>
                        <div class="small text-secondary"><?= esc($r['email']) ?></div>
                    </td>
                    <td>
                        <?php if ($diriSendiri): ?>
                            <?= label_role($r['role']) ?>
                        <?php else: ?>
                            <?= form_open(url_to('panel.pengguna.role', $r['id']), ['class' => 'd-flex gap-1']) ?>
                                <select class="form-select form-select-sm" name="role" aria-label="Role <?= esc($r['nama']) ?>" style="width:9rem">
                                    <?php foreach (config('Akun')->role as $kode => $teks): ?>
                                        <option value="<?= $kode ?>" <?= $r['role'] === $kode ? 'selected' : '' ?>><?= $teks ?></option>
                                    <?php endforeach ?>
                                </select>
                                <button class="btn btn-sm btn-outline-secondary" title="Simpan role"><i class="bi bi-check-lg"></i></button>
                            <?= form_close() ?>
                        <?php endif ?>
                    </td>
                    <td><?= $r['aktif'] ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Dibekukan</span>' ?></td>
                    <td class="small text-secondary text-nowrap"><?= tanggal($r['login_terakhir']) ?></td>
                    <td class="text-end text-nowrap">
                        <?php if (! $diriSendiri): ?>
                            <?= form_open(url_to('panel.pengguna.cabut', $r['id']), ['class' => 'd-inline', 'data-confirm' => "Keluarkan {$r['nama']} dari semua perangkat?"]) ?>
                                <button class="btn btn-sm btn-outline-secondary" title="Keluarkan dari semua perangkat"><i class="bi bi-door-open"></i></button>
                            <?= form_close() ?>
                            <?= form_open(url_to('panel.pengguna.status', $r['id']), ['class' => 'd-inline', 'data-confirm' => ($r['aktif'] ? 'Bekukan' : 'Aktifkan lagi') . " akun {$r['nama']}?"]) ?>
                                <?php if ($r['aktif']): ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-snow"></i> Bekukan</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-unlock"></i> Aktifkan</button>
                                <?php endif ?>
                            <?= form_close() ?>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if ($rows === []): ?><tr><td colspan="5" class="text-center text-secondary py-4">Tidak ada pengguna yang cocok.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
    <div class="p-3"><?= $pager->links('default', 'bs5') ?></div>
</div>
<?= $this->endSection() ?>
