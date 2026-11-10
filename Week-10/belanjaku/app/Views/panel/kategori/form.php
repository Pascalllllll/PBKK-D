<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<div class="stat" style="max-width:40rem">
    <?= form_open($row ? url_to('panel.kategori.perbarui', $row['id']) : url_to('panel.kategori.simpan')) ?>
        <div class="mb-3">
            <label class="form-label" for="nama">Nama kategori</label>
            <input class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= old('nama', $row['nama'] ?? '') ?>">
            <?= field_error('nama') ?: '<div class="form-text">Slug URL dibuat otomatis dari nama' . ($row ? ', saat ini <code>' . esc($row['slug']) . '</code>' : '') . '.</div>' ?>
        </div>
        <div class="mb-4">
            <label class="form-label" for="deskripsi">Deskripsi singkat</label>
            <input class="form-control<?= invalid('deskripsi') ?>" id="deskripsi" name="deskripsi" value="<?= old('deskripsi', $row['deskripsi'] ?? '') ?>">
            <?= field_error('deskripsi') ?>
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a class="btn btn-link text-secondary" href="<?= url_to('panel.kategori') ?>">Batal</a>
    <?= form_close() ?>
</div>
<?= $this->endSection() ?>
