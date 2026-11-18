<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 fw-bold mb-0">Pesanan Saya</h1>
    <div class="d-flex flex-wrap gap-1">
        <a class="chip-kategori<?= $status === '' ? ' active' : '' ?>" href="<?= url_to('pesanan') ?>">Semua</a>
        <?php foreach (\App\Models\PesananModel::STATUS as $s): ?>
            <a class="chip-kategori<?= $status === $s ? ' active' : '' ?>" href="<?= url_to('pesanan') ?>?status=<?= $s ?>"><?= label_status($s)[0] ?></a>
        <?php endforeach ?>
    </div>
</div>

<?php if ($rows === []): ?>
    <div class="kotak p-5 text-center text-secondary"><i class="bi bi-receipt fs-1 d-block mb-2"></i>Belum ada pesanan.</div>
<?php else: ?>
    <div class="kotak">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Kode</th><th>Tanggal</th><th class="text-center">Barang</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="font-monospace fw-semibold"><?= esc($r['kode']) ?></td>
                        <td><?= tanggal($r['created_at']) ?></td>
                        <td class="text-center"><?= $r['jumlah_barang'] ?></td>
                        <td class="text-end"><?= rupiah($r['total']) ?></td>
                        <td><?= badge_status($r['status']) ?></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url_to('pesanan.detail', $r['kode']) ?>">Detail</a></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3"><?= $pager->links('default', 'bs5') ?></div>
<?php endif ?>
<?= $this->endSection() ?>
