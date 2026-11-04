<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Checkout</h1>
<?= form_open('checkout') ?>
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 font-weight-bold mb-3">Alamat Pengiriman</h2>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama_penerima">Nama Penerima</label>
                        <input type="text" class="form-control<?= invalid('nama_penerima') ?>" id="nama_penerima" name="nama_penerima" value="<?= esc(old('nama_penerima', user('nama'))) ?>" required>
                        <?= field_error('nama_penerima') ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="telepon">No. HP</label>
                        <input type="tel" class="form-control<?= invalid('telepon') ?>" id="telepon" name="telepon" value="<?= esc(old('telepon')) ?>" placeholder="081234567890" required>
                        <?= field_error('telepon') ?>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea class="form-control<?= invalid('alamat') ?>" id="alamat" name="alamat" rows="3" required><?= esc(old('alamat')) ?></textarea>
                    <?= field_error('alamat') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 font-weight-bold mb-3">Pesanan</h2>
                <?php foreach ($items as $it): ?>
                    <div class="d-flex justify-content-between small mb-2">
                        <span><?= esc($it['nama']) ?> &times; <?= $it['qty'] ?></span>
                        <span><?= rupiah($it['subtotal']) ?></span>
                    </div>
                <?php endforeach ?>
                <hr>
                <div class="d-flex justify-content-between h5"><span>Total</span><span class="harga"><?= rupiah($total) ?></span></div>
                <button class="btn btn-primary btn-block mt-3"><i class="fas fa-check mr-1"></i>Buat Pesanan</button>
                <a href="<?= site_url('keranjang') ?>" class="btn btn-link btn-block btn-sm">Kembali ke keranjang</a>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>
<?= $this->endSection() ?>
