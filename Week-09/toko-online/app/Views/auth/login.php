<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-4">Login</h1>
                <?= form_open('login') ?>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= esc(old('username')) ?>" autocomplete="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                    </div>
                    <button class="btn btn-primary btn-block">Login</button>
                <?= form_close() ?>
                <p class="text-center small mt-3 mb-0">Belum punya akun? <a href="<?= site_url('register') ?>">Daftar di sini</a></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
