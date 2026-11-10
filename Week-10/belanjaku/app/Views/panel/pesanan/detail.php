<?= $this->extend('layouts/panel') ?>

<?php $p = $pesanan; $admin = session('role') === 'admin'; ?>
<?= $this->section('subjudul') ?><div class="small text-secondary"><?= esc($p['pelanggan']) ?> &middot; <?= tanggal($p['created_at']) ?> &middot; <?= badge_status($p['status']) ?></div><?= $this->endSection() ?>
<?= $this->section('aksi') ?><a class="btn btn-sm btn-outline-secondary" href="<?= url_to('panel.pesanan') ?>"><i class="bi bi-arrow-left"></i> Daftar</a><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stat mb-3"><?= view('komponen/langkah', ['status' => $p['status']]) ?></div>
<div class="row g-3">
    <div class="col-xl-7">
        <div class="stat p-2 mb-3"><?= view('komponen/item_pesanan', ['items' => $items, 'pesanan' => $p]) ?></div>
        <div class="stat">
            <h2 class="h6 fw-bold">Alamat pengiriman</h2>
            <div><strong><?= esc($p['penerima']) ?></strong> &middot; <?= esc($p['telepon']) ?></div>
            <div class="text-secondary"><?= esc($p['alamat']) ?></div>
            <?php if ($p['catatan']): ?><div class="alert alert-light border small mt-2 mb-0"><i class="bi bi-chat-left-text me-1"></i>Catatan: <?= esc($p['catatan']) ?></div><?php endif ?>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="stat mb-3">
            <h2 class="h6 fw-bold mb-3">Pembayaran <?= badge_status($pembayaran['status']) ?></h2>
            <?php if ($pembayaran['bukti']): ?>
                <dl class="row small mb-2">
                    <dt class="col-5 fw-normal text-secondary">Bank / atas nama</dt><dd class="col-7"><?= esc($pembayaran['bank_pengirim']) ?> / <?= esc($pembayaran['nama_rekening']) ?></dd>
                    <dt class="col-5 fw-normal text-secondary">Diunggah</dt><dd class="col-7"><?= tanggal($pembayaran['diunggah_at']) ?></dd>
                    <?php if ($pembayaran['verifikator']): ?><dt class="col-5 fw-normal text-secondary">Diverifikasi</dt><dd class="col-7"><?= esc($pembayaran['verifikator']) ?>, <?= tanggal($pembayaran['diverifikasi_at']) ?></dd><?php endif ?>
                    <?php if ($pembayaran['alasan_tolak']): ?><dt class="col-5 fw-normal text-secondary">Alasan tolak</dt><dd class="col-7 text-danger"><?= esc($pembayaran['alasan_tolak']) ?></dd><?php endif ?>
                </dl>
                <a href="<?= url_to('bukti', $pembayaran['bukti']) ?>" target="_blank"><img class="bukti-img" src="<?= url_to('bukti', $pembayaran['bukti']) ?>" alt="Bukti transfer"></a>
                <?php if ($admin && $pembayaran['status'] === 'menunggu'): ?>
                    <div class="mt-2"><a class="btn btn-sm btn-primary" href="<?= url_to('panel.pembayaran') ?>">Buka antrean verifikasi</a></div>
                <?php endif ?>
            <?php else: ?>
                <p class="small text-secondary mb-0">Pelanggan belum mengunggah bukti transfer.</p>
            <?php endif ?>
        </div>

        <div class="stat mb-3">
            <h2 class="h6 fw-bold mb-3">Pengiriman</h2>
            <?php if ($pengiriman): ?>
                <dl class="row small mb-0">
                    <dt class="col-5 fw-normal text-secondary">Kurir</dt><dd class="col-7"><?= esc($pengiriman['kurir']) ?></dd>
                    <dt class="col-5 fw-normal text-secondary">Nomor resi</dt><dd class="col-7 font-monospace fw-semibold"><?= esc($pengiriman['no_resi']) ?></dd>
                    <dt class="col-5 fw-normal text-secondary">Diinput oleh</dt><dd class="col-7"><?= esc($pengiriman['petugas'] ?? '-') ?>, <?= tanggal($pengiriman['dikirim_at']) ?></dd>
                    <dt class="col-5 fw-normal text-secondary">Diterima</dt><dd class="col-7"><?= tanggal($pengiriman['diterima_at']) ?></dd>
                </dl>
            <?php elseif ($p['status'] === 'diproses'): ?>
                <?= form_open(url_to('panel.pesanan.kirim', $p['id'])) ?>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label small" for="kurir">Kurir</label>
                            <select class="form-select<?= invalid('kurir') ?>" id="kurir" name="kurir">
                                <?php foreach (\App\Models\PengirimanModel::KURIR as $k): ?><option <?= old('kurir') === $k ? 'selected' : '' ?>><?= $k ?></option><?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small" for="no_resi">Nomor resi</label>
                            <input class="form-control font-monospace<?= invalid('no_resi') ?>" id="no_resi" name="no_resi" value="<?= old('no_resi') ?>">
                            <?= field_error('no_resi') ?>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-3"><i class="bi bi-truck me-1"></i>Simpan resi &amp; tandai dikirim</button>
                <?= form_close() ?>
            <?php else: ?>
                <p class="small text-secondary mb-0">Form resi muncul setelah pembayaran diterima (status Diproses).</p>
            <?php endif ?>
        </div>

        <?php if ($p['status'] === 'dikirim'): ?>
            <?= form_open(url_to('panel.pesanan.selesai', $p['id']), ['class' => 'mb-2', 'data-confirm' => 'Tandai pesanan sudah diterima pelanggan?']) ?>
                <button class="btn btn-success w-100"><i class="bi bi-check2-circle me-1"></i>Tandai selesai (barang sudah sampai)</button>
            <?= form_close() ?>
        <?php endif ?>
        <?php if ($admin && in_array('batal', \App\Libraries\AlurPesanan::ALUR[$p['status']], true)): ?>
            <?= form_open(url_to('panel.pesanan.batal', $p['id']), ['data-confirm' => 'Batalkan pesanan? Stok akan dikembalikan.']) ?>
                <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle me-1"></i>Batalkan pesanan</button>
            <?= form_close() ?>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
