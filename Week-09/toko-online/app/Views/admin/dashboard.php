<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row">
    <?= view('admin/_card', ['color' => 'primary', 'label' => 'Produk', 'value' => $produk, 'icon' => 'fa-box']) ?>
    <?= view('admin/_card', ['color' => 'info', 'label' => 'Pesanan', 'value' => $pesanan, 'icon' => 'fa-receipt']) ?>
    <?= view('admin/_card', ['color' => 'warning', 'label' => 'Pelanggan', 'value' => $pelanggan, 'icon' => 'fa-users']) ?>
    <?= view('admin/_card', ['color' => 'success', 'label' => 'Pendapatan', 'value' => rupiah($pendapatan), 'icon' => 'fa-money-bill-wave']) ?>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pesanan Terbaru</h6>
                <a href="<?= site_url('admin/pesanan') ?>" class="small">Lihat semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light"><tr><th class="pl-4">Kode</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
                    <?php foreach ($terbaru as $o): ?>
                        <tr>
                            <td class="pl-4"><a href="<?= site_url('admin/pesanan/' . $o['id']) ?>"><?= kode_pesanan($o['id']) ?></a></td>
                            <td><?= esc($o['pelanggan']) ?></td>
                            <td><?= rupiah($o['total']) ?></td>
                            <td><?= status_badge($o['status']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Pesanan per Status</h6></div>
            <ul class="list-group list-group-flush">
                <?php foreach (\App\Models\OrderModel::STATUS as $s): ?>
                    <li class="list-group-item d-flex justify-content-between"><?= status_badge($s) ?><b><?= $perStatus[$s] ?? 0 ?></b></li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-danger">Stok Menipis (&lt; 5)</h6></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($stokTipis as $p): ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="<?= site_url("admin/produk/{$p['id']}/edit") ?>"><?= esc($p['nama']) ?></a>
                        <span class="badge badge-<?= $p['stok'] ? 'warning' : 'danger' ?>"><?= $p['stok'] ?></span>
                    </li>
                <?php endforeach ?>
                <?php if (! $stokTipis): ?><li class="list-group-item text-muted">Semua stok aman.</li><?php endif ?>
            </ul>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
