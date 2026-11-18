<?php
/**
 * Input kata sandi dengan tombol tampilkan/sembunyikan.
 * Dipanggil lewat helper input_sandi(). Variabel: $nama, $label, $auto, $meter, $bantuan.
 */
?>
<label class="form-label" for="<?= $nama ?>"><?= esc($label) ?></label>
<div class="input-group has-validation">
    <input class="form-control<?= invalid($nama) ?>" type="password" id="<?= $nama ?>" name="<?= $nama ?>" autocomplete="<?= $auto ?>"<?= $meter ? ' data-meter' : '' ?>>
    <button class="btn btn-outline-secondary" type="button" data-intip="<?= $nama ?>" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><i class="bi bi-eye"></i></button>
    <?= field_error($nama) ?>
</div>
<?php if ($meter): ?>
    <div class="meter-sandi mt-2" id="meter-<?= $nama ?>" aria-live="polite"><span></span></div>
<?php endif ?>
<?php if ($bantuan && ! invalid($nama)): ?><div class="form-text"><?= esc($bantuan) ?></div><?php endif ?>
