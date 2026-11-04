<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Keranjang Belanja</h1>
<?php if (! $items): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <p class="mb-3">Keranjang masih kosong.</p>
            <a href="<?= site_url('/') ?>" class="btn btn-primary">Mulai Belanja</a>
        </div>
    </div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <?= form_open('keranjang/update', ['id' => 'form-keranjang']) ?><?= form_close() ?>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="thead-light"><tr><th>Produk</th><th>Harga</th><th style="width: 110px">Jumlah</th><th class="text-right">Subtotal</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="mr-3"><?= view('toko/_gambar', ['p' => $it, 'class' => 'thumb', 'size' => 'fa-lg']) ?></span>
                                    <div>
                                        <a href="<?= site_url('produk/' . $it['id']) ?>" class="text-dark font-weight-bold"><?= esc($it['nama']) ?></a><br>
                                        <small class="text-muted">Stok <?= $it['stok'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle"><?= rupiah($it['harga']) ?></td>
                            <td class="align-middle">
                                <input type="number" form="form-keranjang" name="qty[<?= $it['id'] ?>]" value="<?= $it['qty'] ?>" min="0" max="<?= $it['stok'] ?>" class="form-control form-control-sm" aria-label="Jumlah <?= esc($it['nama']) ?>">
                            </td>
                            <td class="align-middle text-right"><?= rupiah($it['subtotal']) ?></td>
                            <td class="align-middle text-right">
                                <?= form_open('keranjang/hapus/' . $it['id']) ?>
                                    <button class="btn btn-sm btn-link text-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                <?= form_close() ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top d-flex justify-content-between">
                <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Lanjut Belanja</a>
                <button form="form-keranjang" class="btn btn-outline-primary btn-sm"><i class="fas fa-sync mr-1"></i>Perbarui Jumlah</button>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 font-weight-bold">Ringkasan</h2>
                <div class="d-flex justify-content-between"><span><?= array_sum(array_column($items, 'qty')) ?> barang</span><span><?= rupiah($total) ?></span></div>
                <hr>
                <div class="d-flex justify-content-between h5"><span>Total</span><span class="harga"><?= rupiah($total) ?></span></div>
                <a href="<?= site_url('checkout') ?>" class="btn btn-primary btn-block mt-3">Checkout</a>
                <?php if (! session('logged_in')): ?>
                    <small class="text-muted d-block text-center mt-2">Anda akan diminta login sebelum checkout.</small>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
<?= $this->endSection() ?>
