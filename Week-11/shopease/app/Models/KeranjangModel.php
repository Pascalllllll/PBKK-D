<?php

namespace App\Models;

use CodeIgniter\Model;

/** Keranjang disimpan di database (satu baris per user dan produk), jadi tetap ada setelah logout. */
class KeranjangModel extends Model
{
    protected $table         = 'keranjang';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'produk_id', 'qty'];
    protected $useTimestamps = true;

    public function jumlah(int $userId): int
    {
        $row = $this->builder()->selectSum('qty')->where('user_id', $userId)->get()->getRowArray();

        return (int) ($row['qty'] ?? 0);
    }

    public function baris(int $userId, int $produkId): ?array
    {
        return $this->where(['user_id' => $userId, 'produk_id' => $produkId])->first();
    }

    /**
     * Isi keranjang dengan harga, stok, dan status produk terbaru dari database.
     * Harga tidak pernah diambil dari form.
     *
     * @return array{items: list<array>, subtotal: int, bermasalah: bool}
     */
    public function isi(int $userId): array
    {
        $items = $this->select('keranjang.id, keranjang.qty, produk.id AS produk_id, produk.nama, produk.slug, produk.harga, produk.stok, produk.gambar, produk.aktif, produk.berat')
            ->join('produk', 'produk.id = keranjang.produk_id')
            ->where('keranjang.user_id', $userId)
            ->orderBy('keranjang.id')
            ->findAll();

        $subtotal   = 0;
        $bermasalah = false;
        foreach ($items as &$it) {
            $it['subtotal'] = $it['harga'] * $it['qty'];
            $it['masalah']  = ! $it['aktif'] ? 'Produk sudah tidak dijual.' : ($it['qty'] > $it['stok'] ? "Stok tersisa {$it['stok']}." : null);
            $bermasalah     = $bermasalah || $it['masalah'] !== null;
            $subtotal += $it['subtotal'];
        }

        return ['items' => $items, 'subtotal' => $subtotal, 'bermasalah' => $bermasalah];
    }
}
