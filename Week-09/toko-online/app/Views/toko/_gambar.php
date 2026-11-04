<?php
/* Gambar produk, atau ikon kategori jika belum ada gambar. Variabel: $p, $class, $size (opsional) */
$ikonClass = strtr($class, ['produk-img' => 'produk-ikon', 'thumb' => 'thumb-ikon']);
?>
<?php if ($url = gambar_produk($p['gambar'] ?? null)): ?>
    <img src="<?= $url ?>" class="<?= $class ?>" alt="<?= esc($p['nama']) ?>">
<?php else: ?>
    <div class="<?= $ikonClass ?>"><i class="fas <?= esc($p['ikon'] ?? 'fa-box') ?> <?= $size ?? 'fa-4x' ?>" aria-hidden="true"></i></div>
<?php endif ?>
