<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-5">
        <div class="kotak p-4 h-100">
            <h1 class="h3 fw-bold mb-3">Hubungi Kami</h1>
            <p class="text-secondary">Ada pertanyaan soal pesanan atau produk? Sertakan kode pesanan (contoh <code>SE-261110-7Q3F</code>) supaya kami cepat mengecek.</p>
            <ul class="list-unstyled mb-0">
                <li class="mb-3"><i class="bi bi-geo-alt text-success me-2"></i>Jl. Teknik Kimia, Keputih, Sukolilo, Surabaya 60111</li>
                <?php if ($wa = toko('whatsapp')): ?><li class="mb-3"><i class="bi bi-whatsapp text-success me-2"></i><?= esc($wa) ?> (chat saja)</li><?php endif ?>
                <?php if ($email = toko('email')): ?><li class="mb-3"><i class="bi bi-envelope text-success me-2"></i><?= esc($email) ?></li><?php endif ?>
                <li><i class="bi bi-clock text-success me-2"></i>Senin–Sabtu, 08.00–17.00 WIB</li>
            </ul>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="kotak p-4 h-100">
            <h2 class="h5 fw-bold mb-3">Rekening pembayaran</h2>
            <div class="row g-3">
                <?php foreach (toko()->rekening() as $r): ?>
                    <div class="col-md-4"><div class="border rounded-3 p-3"><div class="fw-bold"><?= esc($r['bank']) ?></div><div class="font-monospace"><?= esc($r['nomor']) ?></div><div class="small text-secondary">a.n. <?= esc($r['nama']) ?></div></div></div>
                <?php endforeach ?>
                <?php if (! toko()->checkoutSiap()): ?><div class="col-12 small text-secondary">Rekening pembayaran belum diatur.</div><?php endif ?>
            </div>
            <h2 class="h5 fw-bold mt-4 mb-2">Pertanyaan umum</h2>
            <div class="accordion" id="faq">
                <?php foreach ([
                    ['Berapa ongkos kirimnya?', info_ongkir() . '. Tarif berlaku untuk seluruh Indonesia.'],
                    ['Bukti transfer saya ditolak, bagaimana?', 'Alasan penolakan tampil di halaman detail pesanan. Unggah ulang bukti yang benar dari halaman yang sama.'],
                    ['Bisakah membatalkan pesanan?', 'Bisa, selama pesanan belum dibayar. Stok otomatis dikembalikan.'],
                ] as $i => [$tanya, $jawab]): ?>
                    <div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button<?= $i ? ' collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"><?= $tanya ?></button></h3>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse<?= $i ? '' : ' show' ?>" data-bs-parent="#faq"><div class="accordion-body small"><?= $jawab ?></div></div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
