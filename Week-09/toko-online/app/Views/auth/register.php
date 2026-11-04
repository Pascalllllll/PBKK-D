<?= $this->extend('layouts/toko') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-4">Daftar Akun Pelanggan</h1>
                <?= form_open('register') ?>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= esc(old('nama')) ?>" autocomplete="name" required>
                        <?= field_error('nama') ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label for="username">Username</label>
                            <input type="text" class="form-control<?= invalid('username') ?>" id="username" name="username" value="<?= esc(old('username')) ?>" autocomplete="username" required>
                            <?= field_error('username') ?>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control<?= invalid('email') ?>" id="email" name="email" value="<?= esc(old('email')) ?>" autocomplete="email" required>
                            <?= field_error('email') ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control<?= invalid('password') ?>" id="password" name="password" autocomplete="new-password" required minlength="6">
                            <?= field_error('password') ?>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="password_confirm">Ulangi Password</label>
                            <input type="password" class="form-control<?= invalid('password_confirm') ?>" id="password_confirm" name="password_confirm" autocomplete="new-password" required>
                            <?= field_error('password_confirm') ?>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block">Daftar</button>
                <?= form_close() ?>
                <p class="text-center small mt-3 mb-0">Sudah punya akun? <a href="<?= site_url('login') ?>">Login</a></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
