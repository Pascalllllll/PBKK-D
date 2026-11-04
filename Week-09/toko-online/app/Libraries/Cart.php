<?php

namespace App\Libraries;

use App\Models\ProdukModel;

/**
 * Keranjang belanja disimpan di session sebagai [produk_id => qty],
 * jadi pengunjung bisa belanja sebelum login. Jumlah selalu dibatasi stok.
 */
class Cart
{
    private const KEY = 'cart';

    /** @return array<int, int> */
    public function raw(): array
    {
        return session(self::KEY) ?? [];
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /** Menambah produk. Mengembalikan pesan peringatan/error, atau null jika lancar. */
    public function add(int $id, int $qty): ?string
    {
        $produk = model(ProdukModel::class)->find($id);
        if ($produk === null) {
            return 'Produk tidak ditemukan.';
        }
        if ($produk['stok'] < 1) {
            return 'Stok produk habis.';
        }

        $cart      = $this->raw();
        $baru      = ($cart[$id] ?? 0) + max(1, $qty);
        $cart[$id] = min($baru, (int) $produk['stok']);
        session()->set(self::KEY, $cart);

        return $baru > $produk['stok'] ? "Jumlah dibatasi sesuai stok tersedia ({$produk['stok']})." : null;
    }

    /**
     * Mengubah jumlah beberapa produk sekaligus. Jumlah 0 berarti dihapus.
     *
     * @param array<int|string, int|string> $qty
     */
    public function update(array $qty): ?string
    {
        $cart     = $this->raw();
        $stok     = $this->stok(array_keys($cart));
        $dibatasi = false;

        foreach ($qty as $id => $n) {
            $id = (int) $id;
            $n  = (int) $n;
            if (! isset($cart[$id])) {
                continue;
            }
            if ($n < 1 || ($stok[$id] ?? 0) < 1) {
                unset($cart[$id]);

                continue;
            }
            $dibatasi  = $dibatasi || $n > $stok[$id];
            $cart[$id] = min($n, $stok[$id]);
        }

        session()->set(self::KEY, $cart);

        return $dibatasi ? 'Sebagian jumlah dibatasi sesuai stok tersedia.' : null;
    }

    public function remove(int $id): void
    {
        $cart = $this->raw();
        unset($cart[$id]);
        session()->set(self::KEY, $cart);
    }

    public function clear(): void
    {
        session()->remove(self::KEY);
    }

    /**
     * Isi keranjang beserta data produk terbaru dari database.
     *
     * @return array{items: list<array>, total: int}
     */
    public function items(): array
    {
        $cart = $this->raw();
        if ($cart === []) {
            return ['items' => [], 'total' => 0];
        }

        $items = [];
        $total = 0;
        foreach (model(ProdukModel::class)->daftar()->whereIn('produk.id', array_keys($cart))->findAll() as $p) {
            $p['qty']      = $cart[$p['id']];
            $p['subtotal'] = $p['qty'] * $p['harga'];
            $total += $p['subtotal'];
            $items[] = $p;
        }

        return ['items' => $items, 'total' => $total];
    }

    /** @return array<int, int> */
    private function stok(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return array_map('intval', array_column(
            model(ProdukModel::class)->select('id, stok')->whereIn('id', $ids)->findAll(),
            'stok',
            'id',
        ));
    }
}
