<?php
/** Progres status pesanan. */
$urutan = ['menunggu_bayar' => 'Bayar', 'menunggu_verifikasi' => 'Verifikasi', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai'];
$posisi = array_search($status, array_keys($urutan), true);
?>
<?php if ($status === 'batal'): ?>
    <div class="alert alert-secondary mb-0"><i class="bi bi-x-circle me-1"></i>Pesanan ini dibatalkan. Stok produk sudah dikembalikan.</div>
<?php else: ?>
    <ol class="langkah p-0 mb-0">
        <?php $i = 0; foreach ($urutan as $kunci => $label): ?>
            <li class="<?= $i < $posisi || $status === 'selesai' ? 'done' : ($i === $posisi ? 'now' : '') ?>"><?= $label ?></li>
        <?php $i++; endforeach ?>
    </ol>
<?php endif ?>
