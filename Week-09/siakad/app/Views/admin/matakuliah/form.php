<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $v = static fn (string $f) => esc(old($f, $row[$f] ?? '')); ?>
<div class="card shadow mb-4" style="max-width: 720px">
    <div class="card-body">
        <?= form_open($row ? "admin/matakuliah/{$row['id']}" : 'admin/matakuliah') ?>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="kode">Kode</label>
                    <input type="text" class="form-control<?= invalid('kode') ?>" id="kode" name="kode" value="<?= $v('kode') ?>" maxlength="10" required>
                    <?= field_error('kode') ?>
                </div>
                <div class="form-group col-md-8">
                    <label for="nama">Nama Matakuliah</label>
                    <input type="text" class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= $v('nama') ?>" required>
                    <?= field_error('nama') ?>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="sks">SKS</label>
                    <input type="number" min="1" max="6" class="form-control<?= invalid('sks') ?>" id="sks" name="sks" value="<?= $v('sks') ?>" required>
                    <?= field_error('sks') ?>
                </div>
                <div class="form-group col-md-3">
                    <label for="semester">Semester</label>
                    <input type="number" min="1" max="8" class="form-control<?= invalid('semester') ?>" id="semester" name="semester" value="<?= $v('semester') ?>" required>
                    <?= field_error('semester') ?>
                </div>
                <div class="form-group col-md-6">
                    <label for="dosen_id">Dosen Pengampu</label>
                    <?= form_dropdown('dosen_id', ['' => '- Belum ada -'] + $dosen, old('dosen_id', $row['dosen_id'] ?? ''), ['class' => 'form-control', 'id' => 'dosen_id']) ?>
                </div>
            </div>
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
            <a href="<?= site_url('admin/matakuliah') ?>" class="btn btn-secondary">Batal</a>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
