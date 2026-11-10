<?php foreach (['success' => 'success', 'info' => 'info', 'warning' => 'warning', 'error' => 'danger'] as $key => $kelas): ?>
    <?php if ($pesan = session()->getFlashdata($key)): ?>
        <div class="alert alert-<?= $kelas ?> alert-dismissible fade show" role="alert">
            <?= esc($pesan) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif ?>
<?php endforeach ?>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Data belum valid.</strong> Periksa kembali:
        <ul class="mb-0 mt-1"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach ?></ul>
    </div>
<?php endif ?>
