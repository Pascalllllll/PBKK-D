<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $v = static fn (string $f) => esc(old($f, $row[$f] ?? '')); ?>
<div class="card shadow mb-4" style="max-width: 720px">
    <div class="card-body">
        <?= form_open($row ? "admin/dosen/{$row['id']}" : 'admin/dosen') ?>
            <div class="form-group">
                <label for="nidn">NIDN</label>
                <input type="text" class="form-control<?= invalid('nidn') ?>" id="nidn" name="nidn" value="<?= $v('nidn') ?>" required>
                <?= field_error('nidn') ?>
            </div>
            <div class="form-group">
                <label for="nama">Nama Lengkap dan Gelar</label>
                <input type="text" class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= $v('nama') ?>" required>
                <?= field_error('nama') ?>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control<?= invalid('email') ?>" id="email" name="email" value="<?= $v('email') ?>">
                <?= field_error('email') ?>
            </div>
            <?php if (! $row): ?>
                <p class="small text-muted"><i class="fas fa-info-circle mr-1"></i>Akun login dibuat otomatis. Username dan password awal = NIDN.</p>
            <?php endif ?>
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
            <a href="<?= site_url('admin/dosen') ?>" class="btn btn-secondary">Batal</a>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
