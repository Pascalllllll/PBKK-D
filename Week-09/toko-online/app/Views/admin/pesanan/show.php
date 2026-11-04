<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/pesanan') ?>" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left fa-sm mr-1"></i>Kembali</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $berikut = \App\Models\OrderModel::ALUR[$order['status']]; ?>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Item Pesanan</h6></div>
            <?= view('pesanan/_items', ['items' => $items, 'order' => $order]) ?>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Status: <?= status_badge($order['status']) ?></h6></div>
            <div class="card-body">
                <?php if ($berikut): ?>
                    <?= form_open("admin/pesanan/{$order['id']}/status") ?>
                        <div class="form-group">
                            <label for="status">Ubah status ke</label>
                            <select name="status" id="status" class="form-control">
                                <?php foreach ($berikut as $s): ?><option value="<?= $s ?>" class="text-capitalize"><?= ucfirst($s) ?></option><?php endforeach ?>
                            </select>
                        </div>
                        <button class="btn btn-primary btn-block">Simpan Status</button>
                    <?= form_close() ?>
                    <small class="text-muted d-block mt-2">Alur: pending &rarr; dibayar &rarr; dikirim &rarr; selesai. Batal mengembalikan stok.</small>
                <?php else: ?>
                    <p class="mb-0 text-muted">Status <b><?= esc($order['status']) ?></b> sudah final dan tidak bisa diubah.</p>
                <?php endif ?>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Pengiriman</h6></div>
            <div class="card-body">
                <p class="mb-1"><b><?= esc($order['nama_penerima']) ?></b> (<?= esc($order['telepon']) ?>)</p>
                <p class="small text-muted"><?= nl2br(esc($order['alamat'])) ?></p>
                <p class="small mb-0">Akun: <?= esc($order['pelanggan']) ?><br>Dipesan <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
