<?php /** Tabel item pesanan + rincian biaya. Dipakai halaman pelanggan dan panel. */ ?>
<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Produk</th><th class="text-end">Harga</th><th class="text-center">Qty</th><th class="text-end">Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><div class="d-flex align-items-center gap-2"><img class="thumb" src="<?= gambar_produk($it['gambar']) ?>" alt=""><span><?= esc($it['nama_produk']) ?></span></div></td>
                <td class="text-end"><?= rupiah($it['harga']) ?></td>
                <td class="text-center"><?= $it['qty'] ?></td>
                <td class="text-end"><?= rupiah($it['subtotal']) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-end text-secondary">Subtotal</td><td class="text-end"><?= rupiah($pesanan['subtotal']) ?></td></tr>
            <tr><td colspan="3" class="text-end text-secondary">Ongkos kirim</td><td class="text-end"><?= $pesanan['ongkir'] ? rupiah($pesanan['ongkir']) : '<span class="text-success">Gratis</span>' ?></td></tr>
            <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold harga fs-5"><?= rupiah($pesanan['total']) ?></td></tr>
        </tfoot>
    </table>
</div>
