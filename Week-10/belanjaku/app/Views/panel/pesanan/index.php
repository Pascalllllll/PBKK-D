<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap gap-1 mb-3">
    <a class="chip-kategori<?= $filter['status'] === '' ? ' active' : '' ?>" href="<?= url_to('panel.pesanan') ?>">Semua (<?= array_sum($perStatus) ?>)</a>
    <?php foreach (\App\Models\PesananModel::STATUS as $s): ?>
        <a class="chip-kategori<?= $filter['status'] === $s ? ' active' : '' ?>" href="<?= url_to('panel.pesanan') ?>?status=<?= $s ?>"><?= label_status($s)[0] ?> (<?= $perStatus[$s] ?>)</a>
    <?php endforeach ?>
</div>
<div class="stat p-0">
    <form class="p-3 d-flex gap-2" method="get">
        <input type="hidden" name="status" value="<?= esc($filter['status']) ?>">
        <input class="form-control" style="max-width:22rem" name="q" value="<?= esc($filter['q']) ?>" placeholder="Kode pesanan atau nama pelanggan" aria-label="Cari pesanan">
        <button class="btn btn-outline-primary">Cari</button>
    </form>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Kode</th><th>Pelanggan</th><th>Tanggal</th><th class="text-center">Barang</th><th class="text-end">Total</th><th>Pembayaran</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td class="font-monospace fw-semibold"><?= esc($r['kode']) ?></td>
                    <td><?= esc($r['pelanggan']) ?><div class="small text-secondary"><?= esc($r['email']) ?></div></td>
                    <td class="small"><?= tanggal($r['created_at']) ?></td>
                    <td class="text-center"><?= $r['jumlah_barang'] ?></td>
                    <td class="text-end"><?= rupiah($r['total']) ?></td>
                    <td><?= badge_status($r['status_bayar']) ?></td>
                    <td><?= badge_status($r['status']) ?></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url_to('panel.pesanan.detail', $r['id']) ?>">Detail</a></td>
                </tr>
            <?php endforeach ?>
            <?php if ($rows === []): ?><tr><td colspan="8" class="text-center text-secondary py-4">Tidak ada pesanan.</td></tr><?php endif ?>
            </tbody>
        </table>
    </div>
    <div class="p-3"><?= $pager->links('default', 'bs5') ?></div>
</div>
<?= $this->endSection() ?>
