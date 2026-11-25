<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="kotak p-5 text-center my-4">
    <div class="display-1 fw-bold text-success">404</div>
    <h1 class="h4 fw-bold">Halaman tidak ditemukan</h1>
    <p class="text-secondary mb-1">Tidak ada halaman di alamat berikut, atau datanya bukan milik Anda:</p>
    <p><code><?= esc($path) ?></code></p>
    <a href="<?= url_to('beranda') ?>" class="btn btn-primary me-2">Ke beranda</a>
    <a href="<?= url_to('katalog') ?>" class="btn btn-outline-primary">Lihat katalog</a>
</div>
<?= $this->endSection() ?>
