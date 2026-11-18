<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Checkout</h1>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="kotak p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 fw-bold mb-0">1. Alamat pengiriman</h2>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#alamatBaru"><i class="bi bi-plus-lg"></i> Alamat baru</button>
            </div>

            <div class="collapse<?= session('form_alamat') || $alamat === [] ? ' show' : '' ?>" id="alamatBaru">
                <?= form_open(url_to('checkout.alamat'), ['class' => 'border rounded-3 p-3 mb-3 bg-light']) ?>
                    <div class="row g-2">
                        <div class="col-md-4"><label class="form-label small" for="label">Label</label><input class="form-control form-control-sm<?= invalid('label') ?>" id="label" name="label" value="<?= old('label') ?>" placeholder="Rumah / Kos"><?= field_error('label') ?></div>
                        <div class="col-md-4"><label class="form-label small" for="penerima">Penerima</label><input class="form-control form-control-sm<?= invalid('penerima') ?>" id="penerima" name="penerima" value="<?= old('penerima', user('nama')) ?>"><?= field_error('penerima') ?></div>
                        <div class="col-md-4"><label class="form-label small" for="telepon">Nomor HP</label><input class="form-control form-control-sm<?= invalid('telepon') ?>" id="telepon" name="telepon" value="<?= old('telepon') ?>"><?= field_error('telepon') ?></div>
                        <div class="col-12"><label class="form-label small" for="alamat">Alamat lengkap</label><input class="form-control form-control-sm<?= invalid('alamat') ?>" id="alamat" name="alamat" value="<?= old('alamat') ?>" placeholder="Jalan, nomor, RT/RW, kelurahan"><?= field_error('alamat') ?></div>
                        <div class="col-md-8"><label class="form-label small" for="kota">Kota</label><input class="form-control form-control-sm<?= invalid('kota') ?>" id="kota" name="kota" value="<?= old('kota') ?>"><?= field_error('kota') ?></div>
                        <div class="col-md-4"><label class="form-label small" for="kode_pos">Kode pos</label><input class="form-control form-control-sm<?= invalid('kode_pos') ?>" id="kode_pos" name="kode_pos" value="<?= old('kode_pos') ?>" inputmode="numeric"><?= field_error('kode_pos') ?></div>
                    </div>
                    <button class="btn btn-sm btn-primary mt-3">Simpan alamat</button>
                <?= form_close() ?>
            </div>

            <?= form_open(url_to('checkout.proses'), ['id' => 'formCheckout']) ?>
                <?php foreach ($alamat as $a): ?>
                    <label class="d-flex gap-3 border rounded-3 p-3 mb-2" style="cursor:pointer">
                        <input class="form-check-input mt-1" type="radio" name="alamat_id" value="<?= $a['id'] ?>" <?= (int) $a['id'] === $dipilih ? 'checked' : '' ?>>
                        <span>
                            <span class="badge text-bg-light border"><?= esc($a['label']) ?></span>
                            <strong class="ms-1"><?= esc($a['penerima']) ?></strong> <span class="text-secondary small"><?= esc($a['telepon']) ?></span>
                            <span class="d-block small text-secondary mt-1"><?= esc(\App\Models\AlamatModel::format($a)) ?></span>
                        </span>
                    </label>
                <?php endforeach ?>

                <h2 class="h6 fw-bold mt-4 mb-2">2. Catatan untuk penjual <span class="text-secondary fw-normal small">(opsional)</span></h2>
                <textarea class="form-control" name="catatan" rows="2" maxlength="255" placeholder="mis. warna hitam, titip di pos satpam"><?= old('catatan') ?></textarea>
            <?= form_close() ?>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="kotak p-4">
            <h2 class="h6 fw-bold mb-3">3. Ringkasan pesanan</h2>
            <?php foreach ($items as $it): ?>
                <div class="d-flex gap-2 mb-2 align-items-center">
                    <img class="thumb" src="<?= gambar_produk($it['gambar']) ?>" alt="">
                    <div class="flex-grow-1 small"><?= esc($it['nama']) ?><div class="text-secondary"><?= $it['qty'] ?> × <?= rupiah($it['harga']) ?></div></div>
                    <div class="small fw-semibold"><?= rupiah($it['subtotal']) ?></div>
                </div>
            <?php endforeach ?>
            <hr>
            <div class="d-flex justify-content-between mb-1"><span class="text-secondary">Subtotal</span><span><?= rupiah($subtotal) ?></span></div>
            <div class="d-flex justify-content-between mb-1"><span class="text-secondary">Ongkos kirim</span><span><?= $ongkir ? rupiah($ongkir) : '<span class="text-success">Gratis</span>' ?></span></div>
            <div class="d-flex justify-content-between fw-bold fs-5 my-3"><span>Total bayar</span><span class="harga"><?= rupiah($subtotal + $ongkir) ?></span></div>
            <button form="formCheckout" class="btn btn-primary w-100 py-2" <?= $alamat === [] ? 'disabled' : '' ?>><i class="bi bi-bag-check me-1"></i>Buat Pesanan</button>
            <p class="small text-secondary mt-2 mb-0">Harga dan stok diperiksa ulang saat pesanan dibuat. Pembayaran lewat transfer bank.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
