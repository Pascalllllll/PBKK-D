<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Pesanan <?= kode_pesanan($order['id']) ?> <?= status_badge($order['status']) ?></h1>
    <a href="<?= site_url('pesanan') ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i>Semua Pesanan</a>
</div>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm"><?= view('pesanan/_items', ['items' => $items, 'order' => $order]) ?></div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 font-weight-bold">Pengiriman</h2>
                <p class="mb-1"><?= esc($order['nama_penerima']) ?> (<?= esc($order['telepon']) ?>)</p>
                <p class="text-muted small"><?= nl2br(esc($order['alamat'])) ?></p>
                <p class="small mb-0">Dipesan <?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                <?php if ($order['status'] === 'pending'): ?>
                    <hr>
                    <?= form_open("pesanan/{$order['id']}/batal", ['data-confirm' => 'Batalkan pesanan ini?']) ?>
                        <button class="btn btn-outline-danger btn-block btn-sm">Batalkan Pesanan</button>
                    <?= form_close() ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
