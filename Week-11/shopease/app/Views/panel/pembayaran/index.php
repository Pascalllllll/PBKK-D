<?= $this->extend('layouts/panel') ?>

<?= $this->section('subjudul') ?><div class="small text-secondary"><?= count($antrean) ?> bukti transfer menunggu, urut dari yang paling lama</div><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if ($antrean === []): ?>
    <div class="stat text-center text-secondary py-5 mb-4"><i class="bi bi-check2-all fs-1 d-block mb-2 text-success"></i>Tidak ada pembayaran yang perlu diverifikasi.</div>
<?php endif ?>
<div class="row g-3 mb-4">
    <?php foreach ($antrean as $b): $cocok = (int) $b['jumlah'] === (int) $b['total']; ?>
        <div class="col-xl-6">
            <div class="stat h-100">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <a class="font-monospace fw-bold" href="<?= url_to('panel.pesanan.detail', $b['pesanan_id']) ?>"><?= esc($b['kode']) ?></a>
                        <div class="small text-secondary"><?= esc($b['pelanggan']) ?> &middot; diunggah <?= tanggal($b['diunggah_at']) ?></div>
                    </div>
                    <div class="text-end"><div class="harga fs-5"><?= rupiah($b['total']) ?></div><div class="small text-secondary"><?= esc($b['bank_pengirim']) ?> a.n. <?= esc($b['nama_rekening']) ?></div></div>
                </div>
                <div class="row g-3">
                    <div class="col-sm-5"><a href="<?= url_to('bukti', $b['bukti']) ?>" target="_blank"><img class="bukti-img w-100" style="object-fit:cover;max-height:220px" src="<?= url_to('bukti', $b['bukti']) ?>" alt="Bukti <?= esc($b['kode']) ?>"></a></div>
                    <div class="col-sm-7 d-flex flex-column gap-2">
                        <?= form_open(url_to('panel.pembayaran.terima', $b['id']), ['data-confirm' => "Terima pembayaran {$b['kode']}?"]) ?>
                            <button class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Terima</button>
                        <?= form_close() ?>
                        <?= form_open(url_to('panel.pembayaran.tolak', $b['id'])) ?>
                            <label class="form-label small mb-1" for="alasan<?= $b['id'] ?>">Alasan jika ditolak</label>
                            <textarea class="form-control form-control-sm mb-2" id="alasan<?= $b['id'] ?>" name="alasan" rows="2" placeholder="mis. nominal tidak sesuai, gambar buram"></textarea>
                            <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<div class="stat p-0">
    <h2 class="h6 fw-bold p-3 mb-0">Riwayat verifikasi</h2>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Kode</th><th>Jumlah</th><th>Hasil</th><th>Oleh</th><th>Waktu</th><th>Catatan</th></tr></thead>
            <tbody>
            <?php foreach ($riwayat as $r): ?>
                <tr>
                    <td class="font-monospace"><a href="<?= url_to('panel.pesanan.detail', $r['pesanan_id']) ?>"><?= esc($r['kode']) ?></a></td>
                    <td><?= rupiah($r['jumlah']) ?></td>
                    <td><?= badge_status($r['status']) ?></td>
                    <td><?= esc($r['verifikator']) ?></td>
                    <td class="small"><?= tanggal($r['diverifikasi_at']) ?></td>
                    <td class="small text-secondary"><?= esc($r['alasan_tolak'] ?? '') ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
