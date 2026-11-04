<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="hero py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h1 class="display-4 font-weight-bold">Sistem Informasi Akademik</h1>
                <p class="lead">Pengisian KRS, Kartu Hasil Studi, dan input nilai dalam satu tempat untuk mahasiswa, dosen, dan admin.</p>
                <a href="<?= site_url('login') ?>" class="btn btn-light btn-lg mt-2"><i class="fas fa-sign-in-alt mr-2"></i>Masuk ke SIAKAD</a>
            </div>
            <div class="col-md-5 d-none d-md-block text-center">
                <i class="fas fa-user-graduate" style="font-size: 10rem; opacity: .85"></i>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row text-center">
        <?php foreach ([
            ['mahasiswa', 'fa-user-graduate', 'Mahasiswa'],
            ['dosen', 'fa-chalkboard-teacher', 'Dosen'],
            ['matakuliah', 'fa-book', 'Matakuliah'],
        ] as [$key, $icon, $label]): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <i class="fas <?= $icon ?> fa-2x text-primary mb-2"></i>
                        <h2 class="font-weight-bold mb-0"><?= $jumlah[$key] ?></h2>
                        <p class="text-muted mb-0"><?= $label ?> terdaftar</p>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <div class="row mt-4">
        <?php foreach ([
            ['Admin', 'Mengelola data mahasiswa, dosen, dan matakuliah, termasuk foto mahasiswa.'],
            ['Dosen', 'Melihat matakuliah yang diampu dan menginput nilai peserta.'],
            ['Mahasiswa', 'Mengisi KRS semester berjalan dan melihat KHS beserta IP dan IPK.'],
        ] as [$role, $desc]): ?>
            <div class="col-md-4 mb-3">
                <h5 class="font-weight-bold"><?= $role ?></h5>
                <p class="text-muted"><?= $desc ?></p>
            </div>
        <?php endforeach ?>
    </div>
</section>
<?= $this->endSection() ?>
