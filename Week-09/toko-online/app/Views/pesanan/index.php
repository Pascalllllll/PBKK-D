<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Pesanan Saya</h1>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-light"><tr><th>Kode</th><th>Tanggal</th><th>Jumlah Barang</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($pesanan as $o): ?>
                <tr>
                    <td class="font-weight-bold"><?= kode_pesanan($o['id']) ?></td>
                    <td><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
                    <td><?= $o['jumlah_item'] ?></td>
                    <td><?= rupiah($o['total']) ?></td>
                    <td><?= status_badge($o['status']) ?></td>
                    <td class="text-right"><a href="<?= site_url('pesanan/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            <?php endforeach ?>
            <?php if (! $pesanan): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
