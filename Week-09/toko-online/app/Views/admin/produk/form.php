<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $v = static fn (string $f) => esc(old($f, $row[$f] ?? '')); ?>
<div class="card shadow mb-4">
    <div class="card-body">
        <?= form_open_multipart($row ? "admin/produk/{$row['id']}" : 'admin/produk') ?>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="d-block">Gambar Produk</label>
                <img src="<?= gambar_produk($row['gambar'] ?? null) ?? base_url('img/no-image.svg') ?>" class="img-thumbnail preview mb-2" alt="Gambar produk">
                <div class="custom-file">
                    <input type="file" class="custom-file-input<?= invalid('gambar') ?>" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp">
                    <label class="custom-file-label" for="gambar">Pilih file...</label>
                    <?= field_error('gambar') ?>
                </div>
                <small class="form-text text-muted">JPG/PNG/WEBP, maks. 2 MB<?= $row ? '. Kosongkan jika tidak diganti.' : '.' ?></small>
            </div>
            <div class="col-md-9">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="nama">Nama Produk</label>
                        <input type="text" class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= $v('nama') ?>" required>
                        <?= field_error('nama') ?>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="kategori_id">Kategori</label>
                        <?= form_dropdown('kategori_id', ['' => '- Pilih -'] + $kategori, old('kategori_id', $row['kategori_id'] ?? ''), ['class' => 'form-control' . invalid('kategori_id'), 'id' => 'kategori_id', 'required' => true]) ?>
                        <?= field_error('kategori_id') ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="harga">Harga (Rp)</label>
                        <input type="number" min="1" class="form-control<?= invalid('harga') ?>" id="harga" name="harga" value="<?= $v('harga') ?>" required>
                        <?= field_error('harga') ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="stok">Stok</label>
                        <input type="number" min="0" class="form-control<?= invalid('stok') ?>" id="stok" name="stok" value="<?= $v('stok') ?>" required>
                        <?= field_error('stok') ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea class="form-control<?= invalid('deskripsi') ?>" id="deskripsi" name="deskripsi" rows="4"><?= $v('deskripsi') ?></textarea>
                    <?= field_error('deskripsi') ?>
                </div>
                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button>
                <a href="<?= site_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?= $this->endSection() ?>
