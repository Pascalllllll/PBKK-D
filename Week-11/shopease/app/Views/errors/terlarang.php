<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="kotak p-5 text-center my-4">
    <div class="display-1 fw-bold text-danger">403</div>
    <h1 class="h4 fw-bold">Akses ditolak</h1>
    <p class="text-secondary mb-1">
        Halaman <code><?= esc($path) ?></code> hanya untuk role
        <strong><?= esc(implode(' / ', array_map('label_role', $diizinkan))) ?></strong>,
        sedangkan Anda masuk sebagai <strong><?= label_role(user('role')) ?></strong>.
    </p>
    <a href="<?= halaman_awal(user('role')) ?>" class="btn btn-primary mt-3">Kembali ke halaman saya</a>
</div>
<?= $this->endSection() ?>
