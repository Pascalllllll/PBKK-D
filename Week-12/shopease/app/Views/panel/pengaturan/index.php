<?= $this->extend('layouts/panel') ?>

<?= $this->section('subjudul') ?><div class="small text-secondary">Dipakai di keranjang, checkout, halaman pesanan, dan halaman kontak.</div><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $siap = model(\App\Models\PengaturanModel::class)->checkoutSiap(); ?>
<div class="alert <?= $siap ? 'alert-success' : 'alert-warning' ?> d-flex gap-2 align-items-center">
    <i class="bi <?= $siap ? 'bi-check-circle' : 'bi-exclamation-triangle' ?>"></i>
    <?= $siap ? 'Checkout terbuka untuk pelanggan.' : 'Checkout ditutup: isi minimal satu rekening tujuan.' ?>
</div>
<?= form_open(url_to('panel.pengaturan.simpan'), ['novalidate' => true]) ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="stat mb-4">
            <h2 class="h6 fw-bold mb-3">Pembayaran</h2>
            <div class="mb-3">
                <label class="form-label" for="rekening">Rekening tujuan</label>
                <textarea class="form-control font-monospace<?= invalid('rekening') ?>" id="rekening" name="rekening" rows="4" placeholder="BCA | 1234567890 | PT ShopEase Indonesia"><?= old('rekening', $nilai['rekening']) ?></textarea>
                <?= field_error('rekening') ?: '<div class="form-text">Satu rekening per baris, format <code>Bank | Nomor | Atas nama</code>. Kosongkan untuk menutup checkout.</div>' ?>
            </div>
            <div>
                <label class="form-label" for="instruksi">Instruksi tambahan</label>
                <textarea class="form-control<?= invalid('instruksi') ?>" id="instruksi" name="instruksi" rows="3"><?= old('instruksi', $nilai['instruksi']) ?></textarea>
                <?= field_error('instruksi') ?: '<div class="form-text">Tampil di halaman pesanan, di bawah daftar rekening.</div>' ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="stat mb-4">
            <h2 class="h6 fw-bold mb-3">Ongkos kirim</h2>
            <div class="mb-3">
                <label class="form-label" for="ongkir">Tarif tetap per pesanan</label>
                <div class="input-group"><span class="input-group-text">Rp</span>
                    <input class="form-control<?= invalid('ongkir') ?>" type="number" min="0" id="ongkir" name="ongkir" value="<?= old('ongkir', $nilai['ongkir']) ?>">
                    <?= field_error('ongkir') ?>
                </div>
            </div>
            <label class="form-label" for="gratis_ongkir">Gratis ongkir mulai belanja</label>
            <div class="input-group"><span class="input-group-text">Rp</span>
                <input class="form-control<?= invalid('gratis_ongkir') ?>" type="number" min="0" id="gratis_ongkir" name="gratis_ongkir" value="<?= old('gratis_ongkir', $nilai['gratis_ongkir']) ?>">
                <?= field_error('gratis_ongkir') ?>
            </div>
            <div class="form-text">Isi 0 jika tidak ada gratis ongkir.</div>
        </div>
        <div class="stat mb-4">
            <h2 class="h6 fw-bold mb-3">Kontak toko</h2>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control<?= invalid('email') ?>" type="email" id="email" name="email" value="<?= old('email', $nilai['email']) ?>">
                <?= field_error('email') ?>
            </div>
            <label class="form-label" for="whatsapp">WhatsApp</label>
            <input class="form-control<?= invalid('whatsapp') ?>" id="whatsapp" name="whatsapp" value="<?= old('whatsapp', $nilai['whatsapp']) ?>" placeholder="08...">
            <?= field_error('whatsapp') ?>
        </div>
    </div>
</div>
<button class="btn btn-primary px-4">Simpan pengaturan</button>
<?= form_close() ?>
<?= $this->endSection() ?>
