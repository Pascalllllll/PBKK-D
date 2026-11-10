<?= $this->extend('layouts/panel') ?>

<?= $this->section('aksi') ?>
<a class="btn btn-sm btn-outline-primary" href="<?= url_to('panel.laporan.csv') ?>?<?= http_build_query($filter) ?>"><i class="bi bi-filetype-csv me-1"></i>Unduh CSV</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form class="stat mb-3 row g-2 align-items-end mx-0" method="get">
    <div class="col-md-3"><label class="form-label small" for="dari">Dari tanggal</label><input class="form-control" type="date" id="dari" name="dari" value="<?= esc($filter['dari']) ?>"></div>
    <div class="col-md-3"><label class="form-label small" for="sampai">Sampai tanggal</label><input class="form-control" type="date" id="sampai" name="sampai" value="<?= esc($filter['sampai']) ?>"></div>
    <div class="col-md-3">
        <label class="form-label small" for="status">Status pesanan</label>
        <select class="form-select" id="status" name="status">
            <option value="">Semua status</option>
            <?php foreach (\App\Models\PesananModel::STATUS as $s): ?><option value="<?= $s ?>" <?= $filter['status'] === $s ? 'selected' : '' ?>><?= label_status($s)[0] ?></option><?php endforeach ?>
        </select>
    </div>
    <div class="col-md-3 d-grid"><button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Tampilkan</button></div>
</form>

<div class="row g-3 mb-3">
    <?php foreach ([
        ['Omzet (sudah dibayar)', rupiah($omzet)],
        ['Pesanan dibayar', $jumlahLunas . ' dari ' . count($rows)],
        ['Barang terjual', $barang . ' pcs'],
        ['Rata-rata per pesanan', rupiah($jumlahLunas ? intdiv($omzet, $jumlahLunas) : 0)],
    ] as [$label, $nilai]): ?>
        <div class="col-sm-6 col-xl-3"><div class="stat h-100"><div class="small text-secondary"><?= $label ?></div><div class="angka"><?= $nilai ?></div></div></div>
    <?php endforeach ?>
</div>
<p class="small text-secondary">Periode <?= tanggal($filter['dari'], false) ?> – <?= tanggal($filter['sampai'], false) ?>. Omzet hanya menghitung pesanan berstatus Diproses, Dikirim, dan Selesai (termasuk ongkir <?= rupiah($ongkir) ?>).</p>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="stat p-0">
            <h2 class="h6 fw-bold p-3 mb-0">Transaksi</h2>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Kode</th><th>Tanggal</th><th>Pelanggan</th><th class="text-end">Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="font-monospace"><a href="<?= url_to('panel.pesanan.detail', $r['id']) ?>"><?= esc($r['kode']) ?></a></td>
                            <td class="small"><?= tanggal($r['created_at']) ?></td>
                            <td><?= esc($r['pelanggan']) ?></td>
                            <td class="text-end"><?= rupiah($r['total']) ?></td>
                            <td><?= badge_status($r['status']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    <?php if ($rows === []): ?><tr><td colspan="5" class="text-center text-secondary py-4">Tidak ada transaksi pada periode ini.</td></tr><?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="stat">
            <h2 class="h6 fw-bold mb-3">Penjualan per kategori</h2>
            <?php $maks = max([1, ...array_map('intval', array_column($perKategori, 'omzet'))]); foreach ($perKategori as $k): ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between small"><span><?= esc($k['kategori']) ?> <span class="text-secondary">(<?= $k['qty'] ?> pcs)</span></span><strong><?= rupiah($k['omzet']) ?></strong></div>
                    <div class="progress" style="height:.45rem"><div class="progress-bar" style="width:<?= $k['omzet'] / $maks * 100 ?>%;background:#0f766e"></div></div>
                </div>
            <?php endforeach ?>
            <?php if ($perKategori === []): ?><div class="small text-secondary">Belum ada penjualan.</div><?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
