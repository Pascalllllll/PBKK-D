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
                    <input class="form-control<?= invalid('email') ?>" type="email" id="email" name="email" value="<?= old('email') ?>" autocomplete="username" required autofocus>
                    <?= field_error('email') ?>
                </div>
                <div class="mb-3">
                    <?= input_sandi('password', 'Kata sandi', 'current-password') ?>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="ingat" name="ingat" value="1" <?= old('ingat') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="ingat">Ingat saya selama <?= config('Akun')->hariIngat ?> hari</label>
                    <div class="form-text">Jangan dicentang di komputer umum.</div>
                </div>
                <button class="btn btn-primary w-100 py-2">Masuk</button>
            <?= form_close() ?>
        </div>
        <details class="small text-secondary mt-3">
            <summary>Akun contoh</summary>
            <table class="table table-sm small mt-2 mb-0">
                <tr><td><code>budi@mail.test</code></td><td><code>budi1234</code></td><td>Pelanggan</td></tr>
                <tr><td><code>admin@shopease.test</code></td><td><code>admin123</code></td><td>Admin</td></tr>
                <tr><td><code>gudang@shopease.test</code></td><td><code>gudang123</code></td><td>Staf gudang</td></tr>
                <tr><td><code>dewi@mail.test</code></td><td><code>dewi1234</code></td><td>Pelanggan, dibekukan</td></tr>
            </table>
        </details>
    </div>
</div>
<?= $this->endSection() ?>
