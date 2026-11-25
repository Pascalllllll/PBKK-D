<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="<?= url_to('akun.alamat') ?>" class="small"><i class="bi bi-arrow-left"></i> Buku alamat</a>
        <h1 class="h4 fw-bold mt-1 mb-3">Ubah alamat "<?= esc($a['label']) ?>"</h1>
        <div class="kotak p-4">
            <?= form_open(url_to('akun.alamat.perbarui', $a['id']), ['novalidate' => true]) ?>
                <?= view('komponen/form_alamat', ['a' => $a], ['saveData' => false]) ?>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary">Simpan perubahan</button>
                    <a class="btn btn-outline-secondary" href="<?= url_to('akun.alamat') ?>">Batal</a>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
