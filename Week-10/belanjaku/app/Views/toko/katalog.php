<?= $this->extend('layouts/toko') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <aside class="col-lg-3">
        <form class="kotak p-3" method="get" action="<?= url_to('katalog') ?>">
            <label class="form-label fw-semibold small" for="q">Kata kunci</label>
            <input class="form-control mb-3" id="q" name="q" value="<?= esc($filter['q']) ?>" placeholder="mis. kopi">

            <div class="fw-semibold small mb-2">Kategori</div>
            <div class="form-check mb-1">
                <input class="form-check-input" type="radio" name="kategori" id="k0" value="" <?= $filter['kategori'] ? '' : 'checked' ?>>
                <label class="form-check-label" for="k0">Semua</label>
            </div>
            <?php foreach ($kategori as $k): ?>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="radio" name="kategori" id="k<?= $k['id'] ?>" value="<?= $k['id'] ?>" <?= $filter['kategori'] === (int) $k['id'] ? 'checked' : '' ?>>
                    <label class="form-check-label d-flex justify-content-between w-100" for="k<?= $k['id'] ?>"><?= esc($k['nama']) ?><span class="text-secondary small"><?= $k['jumlah_aktif'] ?></span></label>
                </div>
            <?php endforeach ?>

            <label class="form-label fw-semibold small mt-3" for="urut">Urutkan</label>
            <select class="form-select mb-3" id="urut" name="urut">
                <?php foreach (\App\Models\ProdukModel::URUTAN as $kunci => [, , $label]): ?>
                    <option value="<?= $kunci ?>" <?= $filter['urut'] === $kunci ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach ?>
            </select>
            <div class="d-grid gap-2">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Terapkan</button>
                <a href="<?= url_to('katalog') ?>" class="btn btn-link btn-sm text-secondary">Reset filter</a>
            </div>
        </form>
    </aside>

    <section class="col-lg-9">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h1 class="h4 fw-bold mb-0">Katalog</h1>
            <span class="text-secondary small">
                <?= $total ?> produk<?= $filter['q'] !== '' ? ' untuk "<strong>' . esc($filter['q']) . '</strong>"' : '' ?>
                &middot; halaman <?= $pager->getCurrentPage() ?> dari <?= max(1, $pager->getPageCount()) ?>
            </span>
        </div>

        <?php if ($produk === []): ?>
            <div class="kotak p-5 text-center text-secondary">
                <i class="bi bi-search fs-1 d-block mb-2"></i>Tidak ada produk yang cocok. Coba kata kunci atau kategori lain.
            </div>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 mb-4">
                <?php foreach ($produk as $p): ?><div class="col"><?= view('komponen/kartu_produk', ['p' => $p]) ?></div><?php endforeach ?>
            </div>
            <div class="d-flex justify-content-center"><?= $pager->links('default', 'bs5') ?></div>
        <?php endif ?>
    </section>
</div>
<?= $this->endSection() ?>
