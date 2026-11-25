<?php
/** Linimasa perubahan status pesanan. $riwayat dari PesananRiwayatModel::untuk(), $tampilPelaku untuk panel. */
$tampilPelaku ??= false;
?>
<h2 class="h6 fw-bold mb-3">Riwayat status</h2>
<ol class="linimasa list-unstyled mb-0">
    <?php foreach (array_reverse($riwayat) as $r): ?>
        <?php [$label, $warna] = label_status($r['ke']); ?>
        <li class="linimasa-<?= $warna ?>">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <span class="fw-semibold"><?= $r['dari'] === null ? 'Pesanan dibuat' : esc($label) ?></span>
                <span class="small text-secondary text-nowrap"><?= tanggal($r['created_at']) ?></span>
            </div>
            <?php if ($r['catatan']): ?><div class="small text-secondary"><?= esc($r['catatan']) ?></div><?php endif ?>
            <?php if ($tampilPelaku): ?>
                <div class="small text-secondary"><i class="bi bi-person me-1"></i><?= $r['pelaku'] ? esc($r['pelaku']) . ' (' . label_role($r['role_pelaku']) . ')' : 'Sistem' ?></div>
            <?php endif ?>
        </li>
    <?php endforeach ?>
</ol>
