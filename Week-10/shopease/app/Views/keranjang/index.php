<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Keranjang</h1>

<?php if ($items === []): ?>
    <div class="kotak p-5 text-center">
        <i class="bi bi-cart-x fs-1 text-secondary d-block mb-2"></i>
        <p class="text-secondary">Keranjang masih kosong.</p>
        <a href="<?= url_to('katalog') ?>" class="btn btn-primary">Mulai belanja</a>
    </div>
<?php else: ?>
<div class="row g-4">
    <div class="col-lg-8">
        <?= form_open(url_to('keranjang.ubah'), ['id' => 'formQty']) ?><?= form_close() ?>
        <div class="kotak">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Produk</th><th class="text-end">Harga</th><th style="width:7rem">Jumlah</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr class="<?= $it['masalah'] ? 'table-warning' : '' ?>">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img class="thumb" src="<?= gambar_produk($it['gambar']) ?>" alt="">
                                    <div>
                                        <a class="text-body fw-semibold text-decoration-none" href="<?= url_to('produk.detail', $it['slug']) ?>"><?= esc($it['nama']) ?></a>
                                        <div class="small <?= $it['masalah'] ? 'text-danger' : 'text-secondary' ?>"><?= $it['masalah'] ?? 'Stok ' . $it['stok'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end"><?= rupiah($it['harga']) ?></td>
                            <td><input form="formQty" class="form-control form-control-sm" type="number" name="qty[<?= $it['id'] ?>]" value="<?= $it['qty'] ?>" min="0" max="<?= $it['stok'] ?>" aria-label="Jumlah <?= esc($it['nama']) ?>"></td>
                            <td class="text-end fw-semibold"><?= rupiah($it['subtotal']) ?></td>
                            <td class="text-end">
                                <?= form_open(url_to('keranjang.hapus', $it['id'])) ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus" aria-label="Hapus <?= esc($it['nama']) ?>"><i class="bi bi-trash"></i></button>
                                <?= form_close() ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top d-flex justify-content-between">
                <a href="<?= url_to('katalog') ?>" class="btn btn-link px-0"><i class="bi bi-arrow-left"></i> Lanjut belanja</a>
                <button form="formQty" class="btn btn-outline-primary"><i class="bi bi-arrow-repeat me-1"></i>Perbarui jumlah</button>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="kotak p-4">
            <h2 class="h6 fw-bold mb-3">Ringkasan</h2>
            <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Subtotal (<?= array_sum(array_column($items, 'qty')) ?> barang)</span><span><?= rupiah($subtotal) ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Ongkos kirim</span><span><?= $ongkir ? rupiah($ongkir) : '<span class="text-success">Gratis</span>' ?></span></div>
            <?php if ($ongkir): ?>
                <div class="small text-success mb-2"><i class="bi bi-truck"></i> Tambah <?= rupiah(\App\Libraries\AlurPesanan::GRATIS_ONGKIR - $subtotal) ?> lagi untuk gratis ongkir.</div>
            <?php endif ?>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5 mb-3"><span>Total</span><span class="harga"><?= rupiah($subtotal + $ongkir) ?></span></div>
            <?php if ($bermasalah): ?>
                <div class="alert alert-warning small">Perbaiki baris yang ditandai kuning sebelum checkout.</div>
                <button class="btn btn-primary w-100 py-2" disabled>Checkout</button>
            <?php else: ?>
                <a class="btn btn-primary w-100 py-2" href="<?= url_to('checkout') ?>">Checkout <i class="bi bi-arrow-right"></i></a>
            <?php endif ?>
        </div>
    </div>
</div>
<?php endif ?>
<?= $this->endSection() ?>
