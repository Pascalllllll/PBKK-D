<?php $sb = 'https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/'; ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'SIAKAD') ?> | SIAKAD</title>
    <link rel="stylesheet" href="<?= $sb ?>vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,800">
    <link rel="stylesheet" href="<?= $sb ?>css/sb-admin-2.min.css">
    <style>
        .foto-thumb { width: 40px; height: 40px; object-fit: cover; }
        .foto-preview { width: 120px; height: 150px; object-fit: cover; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <?= $this->include('layouts/_sidebar') ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?= $this->include('layouts/_topbar') ?>

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><?= esc($title ?? '') ?></h1>
                    <?= $this->renderSection('actions') ?>
                </div>
                <?= $this->include('layouts/_alerts') ?>
                <?= $this->renderSection('content') ?>
            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>SIAKAD &middot; Tugas PBKK D &middot; CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="<?= $sb ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= $sb ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $sb ?>vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= $sb ?>js/sb-admin-2.min.js"></script>
<script>
    // Konfirmasi sebelum form hapus dikirim
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.dataset.confirm)) e.preventDefault();
        });
    });
    // Nama file di input custom-file Bootstrap
    document.querySelectorAll('.custom-file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            input.nextElementSibling.textContent = input.files.length ? input.files[0].name : 'Pilih file...';
            // Pratinjau gambar sebelum diunggah
            var preview = input.closest('form').querySelector('img.foto-preview');
            if (preview && input.files.length && input.files[0].type.indexOf('image/') === 0) {
                preview.src = URL.createObjectURL(input.files[0]);
            }
        });
    });
</script>
</body>
</html>
