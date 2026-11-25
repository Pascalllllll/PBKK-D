<?= $this->extend('layouts/panel') ?>

<?= $this->section('subjudul') ?><div class="small text-secondary">Setiap percobaan masuk dicatat, termasuk yang gagal dan yang ditahan pembatas.</div><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="stat p-0">
    <div class="p-3 d-flex flex-wrap gap-2">
        <?php foreach (['' => 'Semua', 'berhasil' => 'Berhasil', 'ingat' => 'Ingat saya', 'gagal' => 'Sandi salah', 'dibekukan' => 'Akun beku', 'dibatasi' => 'Dibatasi'] as $kode => $teks): ?>
            <a class="chip-kategori<?= $hasil === $kode ? ' active' : '' ?>" href="<?= url_to('panel.log') . ($kode ? '?hasil=' . $kode : '') ?>"><?= $teks ?></a>
        <?php endforeach ?>
    </div>
    <div class="px-3"><?= view('komponen/tabel_log', ['rows' => $rows]) ?></div>
    <div class="p-3"><?= $pager->links('default', 'bs5') ?></div>
</div>
<?= $this->endSection() ?>
