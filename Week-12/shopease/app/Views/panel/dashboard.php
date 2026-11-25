<?= $this->extend('layouts/panel') ?>

<?= $this->section('subjudul') ?><div class="small text-secondary"><?= tanggal(date('Y-m-d H:i:s')) ?></div><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Omzet bulan ini', rupiah($omzetBulan), 'bi-wallet2', '#ccfbf1', '#0f766e', 'Total semua: ' . rupiah($omzet)],
        ['Perlu diverifikasi', $antrean, 'bi-hourglass-split', '#fef3c7', '#b45309', 'bukti transfer menunggu'],
        ['Siap dikirim', $perStatus['diproses'], 'bi-box-seam', '#dbeafe', '#1d4ed8', 'pesanan berstatus Diproses'],
        ['Pelanggan', $pelanggan, 'bi-people', '#ede9fe', '#6d28d9', 'akun terdaftar'],
    ] as [$label, $angka, $ikon, $bg, $fg, $ket]): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="stat h-100 d-flex gap-3">
                <div class="ikon" style="background:<?= $bg ?>;color:<?= $fg ?>"><i class="bi <?= $ikon ?>"></i></div>
                <div><div class="small text-secondary"><?= $label ?></div><div class="angka"><?= $angka ?></div><div class="small text-secondary"><?= $ket ?></div></div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="stat h-100">
            <h2 class="h6 fw-bold">Omzet 7 hari terakhir</h2>
            <canvas id="grafik" height="110"></canvas>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="stat h-100">
            <h2 class="h6 fw-bold mb-3">Pesanan per status</h2>
            <?php $max = max(1, max($perStatus)); foreach (\App\Models\PesananModel::STATUS as $s): [$label, $warna] = label_status($s); ?>
                <a class="d-block text-decoration-none text-body mb-2" href="<?= url_to('panel.pesanan') ?>?status=<?= $s ?>">
                    <div class="d-flex justify-content-between small"><span><?= $label ?></span><strong><?= $perStatus[$s] ?></strong></div>
                    <div class="progress" style="height:.45rem"><div class="progress-bar bg-<?= $warna === 'indigo' ? 'primary' : $warna ?>" style="width:<?= $perStatus[$s] / $max * 100 ?>%"></div></div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="stat p-0">
            <div class="d-flex justify-content-between align-items-center p-3"><h2 class="h6 fw-bold mb-0">Pesanan terbaru</h2><a class="small" href="<?= url_to('panel.pesanan') ?>">Semua</a></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Kode</th><th>Pelanggan</th><th>Tanggal</th><th class="text-end">Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($terbaru as $r): ?>
                        <tr>
                            <td><a class="font-monospace fw-semibold" href="<?= url_to('panel.pesanan.detail', $r['id']) ?>"><?= esc($r['kode']) ?></a></td>
                            <td><?= esc($r['pelanggan']) ?></td>
                            <td class="small"><?= tanggal($r['created_at']) ?></td>
                            <td class="text-end"><?= rupiah($r['total']) ?></td>
                            <td><?= badge_status($r['status']) ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="stat mb-3">
            <h2 class="h6 fw-bold mb-3">Terlaris</h2>
            <?php foreach ($terlaris as $i => $p): ?>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill text-bg-light border"><?= $i + 1 ?></span>
                    <span class="small flex-grow-1"><?= esc($p['nama']) ?></span>
                    <span class="small fw-semibold"><?= $p['terjual'] ?> pcs</span>
                </div>
            <?php endforeach ?>
        </div>
        <div class="stat">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-exclamation-triangle text-warning me-1"></i>Stok menipis</h2>
            <?php foreach ($stokTipis as $p): ?>
                <div class="d-flex justify-content-between small mb-1"><span><?= esc($p['nama']) ?></span><span class="badge <?= $p['stok'] ? 'text-bg-warning' : 'text-bg-danger' ?>"><?= $p['stok'] ?></span></div>
            <?php endforeach ?>
            <?php if ($stokTipis === []): ?><div class="small text-secondary">Semua stok aman.</div><?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const data = <?= json_encode($grafik) ?>;
    new Chart(document.getElementById('grafik'), {
        type: 'bar',
        data: { labels: data.label, datasets: [{ label: 'Omzet', data: data.nilai, backgroundColor: '#0f766e', borderRadius: 6 }] },
        options: {
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => 'Rp' + c.parsed.y.toLocaleString('id-ID') } } },
            scales: { y: { ticks: { callback: (v) => 'Rp' + (v / 1000).toLocaleString('id-ID') + 'rb' } } },
            animation: false,
        },
    });
</script>
<?= $this->endSection() ?>
