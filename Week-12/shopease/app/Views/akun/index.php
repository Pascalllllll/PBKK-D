<?php $staf = user('role') !== 'customer'; ?>
<?= $this->extend($staf ? 'layouts/panel' : 'layouts/toko') ?>

<?= $this->section('subjudul') ?><div class="small text-secondary"><?= esc($akun['email']) ?> · <?= label_role($akun['role']) ?></div><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (! $staf): ?>
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="avatar"><?= esc(mb_strtoupper(mb_substr($akun['nama'], 0, 1))) ?></div>
        <div>
            <h1 class="h4 fw-bold mb-0"><?= esc($akun['nama']) ?></h1>
            <div class="small text-secondary">Pelanggan sejak <?= tanggal($akun['created_at'], false) ?></div>
        </div>
        <a class="btn btn-outline-primary btn-sm ms-auto" href="<?= url_to('akun.alamat') ?>"><i class="bi bi-geo-alt me-1"></i>Buku alamat</a>
    </div>
<?php endif ?>

<div class="row g-4">
    <div class="col-lg-7">
        <section class="kotak p-4 mb-4" id="profil">
            <h2 class="h6 fw-bold mb-3">Profil</h2>
            <?= form_open(url_to('akun.profil'), ['novalidate' => true]) ?>
                <div class="mb-3">
                    <label class="form-label" for="nama">Nama lengkap</label>
                    <input class="form-control<?= invalid('nama') ?>" id="nama" name="nama" value="<?= old('nama', $akun['nama']) ?>" autocomplete="name">
                    <?= field_error('nama') ?>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control<?= invalid('email') ?>" type="email" id="email" name="email" value="<?= old('email', $akun['email']) ?>" data-email-awal="<?= esc($akun['email']) ?>" autocomplete="email">
                        <?= field_error('email') ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="telepon">Nomor HP</label>
                        <input class="form-control<?= invalid('telepon') ?>" id="telepon" name="telepon" value="<?= old('telepon', $akun['telepon']) ?>" placeholder="08..." autocomplete="tel">
                        <?= field_error('telepon') ?>
                    </div>
                </div>
                <div class="mb-3<?= invalid('sandi_konfirmasi') || old('email', $akun['email']) !== $akun['email'] ? '' : ' d-none' ?>" id="kolomKonfirmasi">
                    <?= input_sandi('sandi_konfirmasi', 'Kata sandi saat ini', 'current-password', false, 'Email dipakai untuk masuk, jadi menggantinya perlu kata sandi.') ?>
                </div>
                <button class="btn btn-primary">Simpan profil</button>
            <?= form_close() ?>
        </section>

        <section class="kotak p-4" id="sandi">
            <h2 class="h6 fw-bold mb-1">Ganti kata sandi</h2>
            <p class="small text-secondary mb-3">Setelah diganti, semua perangkat lain yang masih login akan dikeluarkan.</p>
            <?= form_open(url_to('akun.sandi'), ['novalidate' => true]) ?>
                <div class="mb-3">
                    <?= input_sandi('sandi_lama', 'Kata sandi lama', 'current-password') ?>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <?= input_sandi('sandi_baru', 'Kata sandi baru', 'new-password', true) ?>
                    </div>
                    <div class="col-md-6">
                        <?= input_sandi('ulangi_sandi', 'Ulangi kata sandi baru', 'new-password') ?>
                    </div>
                </div>
                <button class="btn btn-outline-primary">Ganti kata sandi</button>
            <?= form_close() ?>
        </section>
    </div>

    <div class="col-lg-5">
        <section class="kotak p-4 mb-4" id="perangkat">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                <h2 class="h6 fw-bold mb-0">Perangkat yang diingat</h2>
                <?= form_open(url_to('akun.keluar_semua'), ['data-confirm' => 'Keluarkan semua perangkat lain dari akun ini?']) ?>
                    <button class="btn btn-sm btn-outline-danger">Keluarkan perangkat lain</button>
                <?= form_close() ?>
            </div>
            <p class="small text-secondary">Perangkat yang login dengan "Ingat saya".</p>
            <?php if ($perangkat === []): ?>
                <p class="small text-secondary mb-0">Belum ada. Login biasa berakhir saat browser ditutup atau 2 jam tidak aktif.</p>
            <?php endif ?>
            <ul class="list-unstyled mb-0">
                <?php foreach ($perangkat as $t): ?>
                    <li class="d-flex align-items-center gap-3 py-2 border-top">
                        <i class="bi bi-laptop fs-4 text-secondary"></i>
                        <div class="flex-grow-1 small">
                            <div class="fw-semibold"><?= esc($t['perangkat']) ?>
                                <?php if ($t['selector'] === $selectorIni): ?><span class="badge text-bg-success ms-1">perangkat ini</span><?php endif ?>
                            </div>
                            <div class="text-secondary">Sejak <?= tanggal($t['created_at']) ?> · berlaku s.d. <?= tanggal($t['kedaluwarsa'], false) ?></div>
                        </div>
                        <?php if ($t['selector'] !== $selectorIni): ?>
                            <?= form_open(url_to('akun.perangkat.lupakan', $t['id'])) ?>
                                <button class="btn btn-sm btn-link text-danger p-0">Lupakan</button>
                            <?= form_close() ?>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>

        <section class="kotak p-4">
            <h2 class="h6 fw-bold mb-3">Riwayat masuk</h2>
            <?= view('komponen/tabel_log', ['rows' => $riwayat, 'ringkas' => true]) ?>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
