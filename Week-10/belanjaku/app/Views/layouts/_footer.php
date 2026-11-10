<footer class="footer-toko pt-5 pb-3 mt-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="fs-4 fw-bold text-white mb-2"><i class="bi bi-bag-heart-fill me-1"></i>BelanjaKu</div>
                <p class="small mb-0">Toko online kebutuhan kuliah dan sehari-hari. Dibuat untuk tugas Pemrograman Berbasis Kerangka Kerja (D) dengan CodeIgniter <?= \CodeIgniter\CodeIgniter::CI_VERSION ?>.</p>
            </div>
            <div class="col-6 col-md-3">
                <div class="fw-semibold text-white mb-2">Belanja</div>
                <ul class="list-unstyled small mb-0">
                    <li><a href="<?= url_to('katalog') ?>">Katalog</a></li>
                    <li><a href="<?= url_to('katalog') ?>?urut=termurah">Harga termurah</a></li>
                    <?php if (session('role') === 'customer'): ?><li><a href="<?= url_to('pesanan') ?>">Pesanan saya</a></li><?php endif ?>
                </ul>
            </div>
            <div class="col-6 col-md-4">
                <div class="fw-semibold text-white mb-2">BelanjaKu</div>
                <ul class="list-unstyled small mb-0">
                    <li><a href="<?= url_to('tentang') ?>">Tentang kami</a></li>
                    <li><a href="<?= url_to('kontak') ?>">Kontak</a></li>
                    <li>Gratis ongkir mulai <?= rupiah(\App\Libraries\AlurPesanan::GRATIS_ONGKIR) ?></li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary-subtle">
        <div class="small text-center">&copy; <?= date('Y') ?> BelanjaKu &middot; Hosea Felix Sanjaya (5025241177)</div>
    </div>
</footer>
