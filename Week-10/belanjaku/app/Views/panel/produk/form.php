<?= $this->extend('layouts/panel') ?>

<?= $this->section('content') ?>
<?= form_open_multipart($row ? url_to('panel.produk.perbarui', $row['id']) : url_to('panel.produk.simpan'), ['novalidate' => true]) ?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="stat">
            <div class="mb-3">
                <label class="form-label" for="nama">Nama produk</label>
                <input class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= old('nama', $row['nama'] ?? '') ?>">
                <?= field_error('nama') ?>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="kategori_id">Kategori</label>
                    <select class="form-select<?= invalid('kategori_id') ?>" id="kategori_id" name="kategori_id">
                        <option value="">Pilih kategori</option>
                        <?php foreach ($kategori as $id => $nama): ?><option value="<?= $id ?>" <?= (int) old('kategori_id', $row['kategori_id'] ?? 0) === $id ? 'selected' : '' ?>><?= esc($nama) ?></option><?php endforeach ?>
                    </select>
                    <?= field_error('kategori_id') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="harga">Harga</label>
                    <div class="input-group has-validation"><span class="input-group-text">Rp</span>
                        <input class="form-control<?= invalid('harga') ?>" type="number" id="harga" name="harga" min="1" value="<?= old('harga', $row['harga'] ?? '') ?>">
                        <?= field_error('harga') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="stok">Stok</label>
                    <input class="form-control<?= invalid('stok') ?>" type="number" id="stok" name="stok" min="0" value="<?= old('stok', $row['stok'] ?? 0) ?>">
                    <?= field_error('stok') ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="berat">Berat</label>
                    <div class="input-group has-validation">
                        <input class="form-control<?= invalid('berat') ?>" type="number" id="berat" name="berat" min="1" value="<?= old('berat', $row['berat'] ?? 500) ?>"><span class="input-group-text">gram</span>
                        <?= field_error('berat') ?>
                    </div>
                </div>
            </div>
            <label class="form-label" for="deskripsi">Deskripsi</label>
            <textarea class="form-control<?= invalid('deskripsi') ?>" id="deskripsi" name="deskripsi" rows="5"><?= old('deskripsi', $row['deskripsi'] ?? '') ?></textarea>
            <?= field_error('deskripsi') ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat mb-3">
            <label class="form-label" for="gambar">Gambar produk</label>
            <img id="pratinjau" class="detail-img mb-2" src="<?= gambar_produk($row['gambar'] ?? null) ?>" alt="Pratinjau gambar">
            <input class="form-control<?= invalid('gambar') ?>" type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp">
            <?= field_error('gambar') ?: '<div class="form-text">JPG/PNG/WEBP, maks. 2 MB.' . ($row ? ' Kosongkan jika tidak diganti.' : '') . '</div>' ?>
        </div>
        <div class="stat mb-3">
            <div class="form-check form-switch">
                <input type="hidden" name="aktif" value="0">
                <input class="form-check-input" type="checkbox" role="switch" id="aktif" name="aktif" value="1" <?= old('aktif', $row['aktif'] ?? 1) ? 'checked' : '' ?>>
                <label class="form-check-label" for="aktif">Tampilkan di katalog</label>
            </div>
            <div class="form-text">Produk nonaktif tidak muncul di katalog dan tidak bisa dibeli.</div>
        </div>
        <div class="d-grid gap-2">
            <button class="btn btn-primary">Simpan produk</button>
            <a class="btn btn-link text-secondary" href="<?= url_to('panel.produk') ?>">Batal</a>
        </div>
    </div>
</div>
<?= form_close() ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('gambar').addEventListener('change', function () {
        if (this.files[0]) document.getElementById('pratinjau').src = URL.createObjectURL(this.files[0]);
    });
</script>
<?= $this->endSection() ?>
