<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-pills small">
            <li class="nav-item"><a class="nav-link<?= $status === '' ? ' active' : '' ?>" href="<?= site_url('admin/pesanan') ?>">Semua</a></li>
            <?php foreach (\App\Models\OrderModel::STATUS as $s): ?>
                <li class="nav-item"><a class="nav-link text-capitalize<?= $status === $s ? ' active' : '' ?>" href="?status=<?= $s ?>"><?= $s ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light"><tr><th>Kode</th><th>Tanggal</th><th>Pelanggan</th><th>Barang</th><th class="text-right">Total</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($rows as $o): ?>
                    <tr>
                        <td class="font-weight-bold"><?= kode_pesanan($o['id']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($o['created_at'])) ?></td>
                        <td><?= esc($o['pelanggan']) ?></td>
                        <td><?= $o['jumlah_item'] ?></td>
                        <td class="text-right"><?= rupiah($o['total']) ?></td>
                        <td><?= status_badge($o['status']) ?></td>
                        <td><a href="<?= site_url('admin/pesanan/' . $o['id']) ?>" class="btn btn-sm btn-primary">Detail</a></td>
                    </tr>
                <?php endforeach ?>
                <?php if (! $rows): ?>
                    <tr><td colspan="7" class="text-center text-muted">Tidak ada pesanan.</td></tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3"><?= $pager->links('default', 'bootstrap') ?></div>
    </div>
</div>
<?= $this->endSection() ?>
