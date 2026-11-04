<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $v = static fn (string $f) => esc(old($f, $row[$f] ?? '')); ?>
<div class="card shadow mb-4">
    <div class="card-body">
        <?= form_open_multipart($row ? "admin/mahasiswa/{$row['id']}" : 'admin/mahasiswa') ?>
        <div class="row">
            <div class="col-md-3 text-center mb-3">
                <img src="<?= foto_mahasiswa($row['foto'] ?? null) ?>" class="img-thumbnail foto-preview mb-2" alt="Foto mahasiswa">
                <div class="custom-file text-left">
                    <input type="file" class="custom-file-input<?= invalid('foto') ?>" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
                    <label class="custom-file-label" for="foto">Pilih file...</label>
                    <?= field_error('foto') ?>
                </div>
                <small class="form-text text-muted">JPG/PNG/WEBP, maks. 1 MB<?= $row ? '. Kosongkan jika tidak diganti.' : '.' ?></small>
            </div>
            <div class="col-md-9">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="nrp">NRP</label>
                        <input type="text" class="form-control<?= invalid('nrp') ?>" id="nrp" name="nrp" value="<?= $v('nrp') ?>" maxlength="10" required>
                        <?= field_error('nrp') ?>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= $v('nama') ?>" required>
                        <?= field_error('nama') ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="prodi">Program Studi</label>
                        <input type="text" class="form-control<?= invalid('prodi') ?>" id="prodi" name="prodi" value="<?= esc(old('prodi', $row['prodi'] ?? 'Teknik Informatika')) ?>" required>
                        <?= field_error('prodi') ?>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="angkatan">Angkatan</label>
                        <input type="number" class="form-control<?= invalid('angkatan') ?>" id="angkatan" name="angkatan" value="<?= esc(old('angkatan', $row['angkatan'] ?? date('Y'))) ?>" required>
                        <?= field_error('angkatan') ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dosen_wali_id">Dosen Wali</label>
                        <?= form_dropdown('dosen_wali_id', ['' => '- Belum ada -'] + $dosen, old('dosen_wali_id', $row['dosen_wali_id'] ?? ''), ['class' => 'form-control', 'id' => 'dosen_wali_id']) ?>
                    </div>
                </div>
                <?php if (! $row): ?>
                    <p class="small text-muted mb-3"><i class="fas fa-info-circle mr-1"></i>Akun login dibuat otomatis. Username dan password awal = NRP.</p>
                <?php endif ?>
                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
                <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
