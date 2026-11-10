<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="kotak p-4 p-md-5">
            <h1 class="h3 fw-bold mb-3">Tentang BelanjaKu</h1>
            <p>BelanjaKu adalah toko online sederhana yang menjual perlengkapan kuliah, elektronik kecil, pakaian, dan kebutuhan harian. Aplikasi ini dibuat sebagai tugas Week 10 mata kuliah Pemrograman Berbasis Kerangka Kerja (D) untuk mempraktikkan Router, Controller, dan View pada CodeIgniter 4.</p>
            <h2 class="h6 fw-bold mt-4">Cara belanja</h2>
            <ol class="mb-4">
                <li>Pilih produk di katalog, lalu masukkan ke keranjang (perlu akun pelanggan).</li>
                <li>Checkout dengan memilih alamat pengiriman.</li>
                <li>Transfer sesuai total ke rekening toko dan unggah bukti transfer.</li>
                <li>Admin memverifikasi pembayaran, staf gudang mengemas dan mengisi nomor resi.</li>
                <li>Setelah barang sampai, tekan <em>Pesanan Diterima</em>.</li>
            </ol>
            <div class="row g-3">
                <?php foreach ([['Pelanggan', 'Belanja, checkout, unggah bukti, pantau pesanan', 'bi-person'], ['Admin', 'Kelola katalog, verifikasi pembayaran, laporan', 'bi-person-gear'], ['Staf gudang', 'Proses pesanan dan isi nomor resi', 'bi-box-seam']] as [$r, $d, $i]): ?>
                    <div class="col-md-4"><div class="border rounded-3 p-3 h-100"><i class="bi <?= $i ?> fs-4 text-success"></i><div class="fw-semibold mt-1"><?= $r ?></div><div class="small text-secondary"><?= $d ?></div></div></div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
