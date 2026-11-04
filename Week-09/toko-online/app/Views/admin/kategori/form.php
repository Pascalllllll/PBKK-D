<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card shadow mb-4" style="max-width: 640px">
    <div class="card-body">
        <?= form_open($row ? "admin/kategori/{$row['id']}" : 'admin/kategori') ?>
            <div class="form-group">
                <label for="nama">Nama Kategori</label>
                <input type="text" class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= esc(old('nama', $row['nama'] ?? '')) ?>" required>
                <?= field_error('nama') ?>
            </div>
            <div class="form-group">
                <label for="ikon">Ikon (Font Awesome)</label>
                <input type="text" class="form-control<?= invalid('ikon') ?>" id="ikon" name="ikon" value="<?= esc(old('ikon', $row['ikon'] ?? 'fa-box')) ?>" placeholder="fa-laptop">
                <?= field_error('ikon') ?>
                <small class="form-text text-muted">Ditampilkan di etalase untuk produk yang belum punya gambar. Contoh: fa-laptop, fa-tshirt, fa-book.</small>
            </div>
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
            <a href="<?= site_url('admin/kategori') ?>" class="btn btn-secondary">Batal</a>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
