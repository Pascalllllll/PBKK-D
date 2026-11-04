<?php foreach (['success' => 'success', 'error' => 'danger'] as $key => $class): ?>
    <?php if (session()->getFlashdata($key)): ?>
        <div class="alert alert-<?= $class ?> alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata($key)) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif ?>
<?php endforeach ?>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger" role="alert">
        <b>Data belum valid.</b> Periksa kembali isian berikut:
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach ?></ul>
    </div>
<?php endif ?>
