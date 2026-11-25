<?php
/** Tabel log masuk. $ringkas = true untuk halaman akun (tanpa kolom nama/email). */
$ringkas ??= false;
$warna = ['berhasil' => 'success', 'ingat' => 'info', 'gagal' => 'danger', 'dibekukan' => 'secondary', 'dibatasi' => 'warning'];
$label = ['berhasil' => 'Berhasil', 'ingat' => 'Ingat saya', 'gagal' => 'Sandi salah', 'dibekukan' => 'Akun beku', 'dibatasi' => 'Dibatasi'];
?>
<div class="table-responsive">
    <table class="table table-sm align-middle small mb-0">
        <thead><tr>
            <th>Waktu</th>
            <?php if (! $ringkas): ?><th>Akun</th><?php endif ?>
            <th>Hasil</th><th>Perangkat</th><?php if (! $ringkas): ?><th>IP</th><?php endif ?>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td class="text-nowrap"><?= tanggal($r['created_at']) ?></td>
                <?php if (! $ringkas): ?>
                    <td><?= $r['nama'] ? '<div class="fw-semibold">' . esc($r['nama']) . '</div>' : '' ?><span class="text-secondary"><?= esc($r['email']) ?></span></td>
                <?php endif ?>
                <td><span class="badge text-bg-<?= $warna[$r['hasil']] ?>"><?= $label[$r['hasil']] ?></span></td>
                <td class="text-secondary"><?= esc($r['perangkat']) ?></td>
                <?php if (! $ringkas): ?><td><code><?= esc($r['ip']) ?></code></td><?php endif ?>
            </tr>
        <?php endforeach ?>
        <?php if ($rows === []): ?><tr><td colspan="5" class="text-center text-secondary py-3">Belum ada catatan.</td></tr><?php endif ?>
        </tbody>
    </table>
</div>
