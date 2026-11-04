<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-university fa-3x text-primary"></i>
                        <h1 class="h4 mt-2 mb-0">Login SIAKAD</h1>
                        <small class="text-muted">Admin, dosen (NIDN), atau mahasiswa (NRP)</small>
                    </div>

                    <?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $class): ?>
                        <?php if (session()->getFlashdata($key)): ?>
                            <div class="alert alert-<?= $class ?> py-2"><?= esc(session()->getFlashdata($key)) ?></div>
                        <?php endif ?>
                    <?php endforeach ?>

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
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
