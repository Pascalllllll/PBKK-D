<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<?php $p = $pesanan; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <a href="<?= url_to('pesanan') ?>" class="small"><i class="bi bi-arrow-left"></i> Pesanan saya</a>
        <h1 class="h4 fw-bold mb-0 mt-1">Pesanan <span class="font-monospace"><?= esc($p['kode']) ?></span></h1>
        <div class="small text-secondary">Dibuat <?= tanggal($p['created_at']) ?></div>
    </div>
    <div class="fs-5"><?= badge_status($p['status']) ?></div>
</div>

<div class="kotak p-4 mb-4"><?= view('komponen/langkah', ['status' => $p['status']]) ?></div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="kotak p-3 mb-4"><?= view('komponen/item_pesanan', ['items' => $items, 'pesanan' => $p]) ?></div>
        <div class="kotak p-4">
            <h2 class="h6 fw-bold">Dikirim ke</h2>
            <div><strong><?= esc($p['penerima']) ?></strong> &middot; <?= esc($p['telepon']) ?></div>
            <div class="text-secondary"><?= esc($p['alamat']) ?></div>
            <?php if ($p['catatan']): ?><div class="small mt-2"><i class="bi bi-chat-left-text me-1"></i><?= esc($p['catatan']) ?></div><?php endif ?>
            <?php if ($pengiriman): ?>
                <hr>
                <h2 class="h6 fw-bold">Pengiriman</h2>
                <div class="d-flex flex-wrap gap-4">
                    <div><div class="small text-secondary">Kurir</div><div class="fw-semibold"><?= esc($pengiriman['kurir']) ?></div></div>
                    <div><div class="small text-secondary">Nomor resi</div><div class="fw-semibold font-monospace"><?= esc($pengiriman['no_resi']) ?></div></div>
                    <div><div class="small text-secondary">Dikirim</div><div><?= tanggal($pengiriman['dikirim_at']) ?></div></div>
                    <?php if ($pengiriman['diterima_at']): ?><div><div class="small text-secondary">Diterima</div><div><?= tanggal($pengiriman['diterima_at']) ?></div></div><?php endif ?>
                </div>
            <?php endif ?>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="kotak p-4">
            <h2 class="h6 fw-bold mb-3">Pembayaran <?= badge_status($pembayaran['status']) ?></h2>

            <?php if ($pembayaran['status'] === 'ditolak'): ?>
                <div class="alert alert-danger small"><strong>Bukti sebelumnya ditolak:</strong> <?= esc($pembayaran['alasan_tolak']) ?></div>
            <?php endif ?>

            <?php if ($p['status'] === 'menunggu_bayar'): ?>
                <p class="small mb-2">Transfer tepat <strong class="harga"><?= rupiah($p['total']) ?></strong> ke salah satu rekening:</p>
                <ul class="small mb-3">
                    <li>BCA <span class="font-monospace">123 456 7890</span></li>
                    <li>Mandiri <span class="font-monospace">140 00 1234567 8</span></li>
                    <li>BNI <span class="font-monospace">098 765 4321</span></li>
                </ul>
                <?= form_open_multipart(url_to('pesanan.bayar', $p['kode'])) ?>
                    <div class="row g-2 mb-2">
                        <div class="col-md-5">
                            <label class="form-label small" for="bank">Bank pengirim</label>
                            <select class="form-select form-select-sm<?= invalid('bank_pengirim') ?>" id="bank" name="bank_pengirim">
                                <?php foreach (['BCA', 'Mandiri', 'BNI', 'BRI', 'BSI', 'Lainnya'] as $b): ?><option <?= old('bank_pengirim') === $b ? 'selected' : '' ?>><?= $b ?></option><?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small" for="nama_rekening">Nama pemilik rekening</label>
                            <input class="form-control form-control-sm<?= invalid('nama_rekening') ?>" id="nama_rekening" name="nama_rekening" value="<?= old('nama_rekening', user('nama')) ?>">
                            <?= field_error('nama_rekening') ?>
                        </div>
                    </div>
                    <label class="form-label small" for="bukti">Bukti transfer (JPG/PNG/WEBP, maks. 2 MB)</label>
                    <input class="form-control form-control-sm mb-2<?= invalid('bukti') ?>" type="file" id="bukti" name="bukti" accept="image/jpeg,image/png,image/webp">
                    <?= field_error('bukti') ?>
                    <img id="pratinjau" class="bukti-img d-none mb-2" alt="Pratinjau bukti">
                    <button class="btn btn-primary w-100"><i class="bi bi-upload me-1"></i>Kirim Bukti Pembayaran</button>
                <?= form_close() ?>
                <?= form_open(url_to('pesanan.batal', $p['kode']), ['class' => 'mt-2', 'data-confirm' => 'Batalkan pesanan ini?']) ?>
                    <button class="btn btn-link text-danger btn-sm w-100">Batalkan pesanan</button>
                <?= form_close() ?>
            <?php elseif ($pembayaran['bukti']): ?>
                <dl class="row small mb-2">
                    <dt class="col-5 fw-normal text-secondary">Bank pengirim</dt><dd class="col-7"><?= esc($pembayaran['bank_pengirim']) ?></dd>
                    <dt class="col-5 fw-normal text-secondary">Atas nama</dt><dd class="col-7"><?= esc($pembayaran['nama_rekening']) ?></dd>
                    <dt class="col-5 fw-normal text-secondary">Diunggah</dt><dd class="col-7"><?= tanggal($pembayaran['diunggah_at']) ?></dd>
                    <?php if ($pembayaran['diverifikasi_at']): ?>
                        <dt class="col-5 fw-normal text-secondary">Diverifikasi</dt><dd class="col-7"><?= tanggal($pembayaran['diverifikasi_at']) ?></dd>
                    <?php endif ?>
                </dl>
                <a href="<?= url_to('bukti', $pembayaran['bukti']) ?>" target="_blank"><img class="bukti-img" src="<?= url_to('bukti', $pembayaran['bukti']) ?>" alt="Bukti transfer"></a>
                <?php if ($p['status'] === 'menunggu_verifikasi'): ?>
                    <p class="small text-secondary mt-2 mb-0">Bukti sedang diperiksa admin. Biasanya kurang dari 1×24 jam.</p>
                <?php endif ?>
            <?php else: ?>
                <p class="small text-secondary mb-0">Pesanan dibatalkan sebelum dibayar.</p>
            <?php endif ?>

            <?php if ($p['status'] === 'dikirim'): ?>
                <hr>
                <?= form_open(url_to('pesanan.diterima', $p['kode']), ['data-confirm' => 'Pastikan barang sudah Anda terima. Lanjutkan?']) ?>
                    <button class="btn btn-success w-100"><i class="bi bi-check2-circle me-1"></i>Pesanan Diterima</button>
                <?= form_close() ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const input = document.getElementById('bukti');
    if (input) input.addEventListener('change', function () {
        const img = document.getElementById('pratinjau');
        if (this.files[0]) { img.src = URL.createObjectURL(this.files[0]); img.classList.remove('d-none'); }
    });
</script>
<?= $this->endSection() ?>
