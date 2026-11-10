<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="kotak p-4 p-md-5">
            <h1 class="h4 fw-bold mb-1">Masuk</h1>
            <p class="text-secondary small mb-4">Belum punya akun? <a href="<?= url_to('daftar') ?>">Daftar di sini</a>.</p>
            <?= form_open(url_to('masuk.proses')) ?>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control<?= invalid('email') ?>" type="email" id="email" name="email" value="<?= old('email') ?>" autocomplete="email" required autofocus>
                    <?= field_error('email') ?>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Kata sandi</label>
                    <input class="form-control<?= invalid('password') ?>" type="password" id="password" name="password" autocomplete="current-password" required>
                    <?= field_error('password') ?>
                </div>
                <button class="btn btn-primary w-100 py-2">Masuk</button>
            <?= form_close() ?>
        </div>
        <div class="small text-secondary mt-3">
            Akun contoh: <code>budi@mail.test</code> / <code>budi1234</code> (pelanggan),
            <code>admin@shopease.test</code> / <code>admin123</code>,
            <code>gudang@shopease.test</code> / <code>gudang123</code>.
        </div>
    </div>
</div>
<?= $this->endSection() ?>
