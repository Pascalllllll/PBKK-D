<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="kotak p-4 p-md-5">
            <h1 class="h4 fw-bold mb-1">Buat akun pelanggan</h1>
            <p class="text-secondary small mb-4">Sudah punya akun? <a href="<?= url_to('masuk') ?>">Masuk</a>.</p>
            <?= form_open(url_to('daftar.proses'), ['novalidate' => true]) ?>
                <div class="mb-3">
                    <label class="form-label" for="nama">Nama lengkap</label>
                    <input class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= old('nama') ?>" autocomplete="name">
                    <?= field_error('nama') ?>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control<?= invalid('email') ?>" type="email" id="email" name="email" value="<?= old('email') ?>" autocomplete="email">
                        <?= field_error('email') ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="telepon">Nomor HP <span class="text-secondary small">(opsional)</span></label>
                        <input class="form-control<?= invalid('telepon') ?>" id="telepon" name="telepon" value="<?= old('telepon') ?>" placeholder="08..." autocomplete="tel">
                        <?= field_error('telepon') ?>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="password">Kata sandi</label>
                        <input class="form-control<?= invalid('password') ?>" type="password" id="password" name="password" autocomplete="new-password">
                        <?= field_error('password') ?: '<div class="form-text">Minimal 8 karakter.</div>' ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="konfirmasi">Ulangi kata sandi</label>
                        <input class="form-control<?= invalid('konfirmasi') ?>" type="password" id="konfirmasi" name="konfirmasi" autocomplete="new-password">
                        <?= field_error('konfirmasi') ?>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input<?= invalid('setuju') ?>" type="checkbox" id="setuju" name="setuju" value="1" <?= old('setuju') ? 'checked' : '' ?>>
                    <label class="form-check-label small" for="setuju">Saya setuju dengan syarat dan ketentuan ShopEase.</label>
                    <?= field_error('setuju') ?>
                </div>
                <button class="btn btn-primary w-100 py-2">Daftar</button>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
