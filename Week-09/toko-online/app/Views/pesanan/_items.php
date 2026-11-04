<table class="table mb-0">
    <thead class="thead-light"><tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th class="text-right">Subtotal</th></tr></thead>
    <tbody>
    <?php foreach ($items as $it): ?>
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <span class="mr-3"><?= view('toko/_gambar', ['p' => $it, 'class' => 'thumb', 'size' => 'fa-lg']) ?></span>
                    <?= esc($it['nama']) ?>
                </div>
            </td>
            <td class="align-middle"><?= rupiah($it['harga']) ?></td>
            <td class="align-middle"><?= $it['qty'] ?></td>
            <td class="align-middle text-right"><?= rupiah($it['harga'] * $it['qty']) ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr><th colspan="3" class="text-right">Total</th><th class="text-right harga"><?= rupiah($order['total']) ?></th></tr>
    </tfoot>
</table>
