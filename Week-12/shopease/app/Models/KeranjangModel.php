<?php

namespace App\Models;

use CodeIgniter\Model;

/** Keranjang disimpan di database (satu baris per user dan produk), jadi tetap ada setelah logout. */
class KeranjangModel extends Model
{
    protected $table         = 'keranjang';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'produk_id', 'qty', 'harga'];
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
     * Harga tidak pernah diambil dari form. harga_lama adalah harga saat produk dimasukkan ke keranjang.
     *
     * @return array{items: list<array>, subtotal: int, bermasalah: bool, hargaBerubah: bool}
     */
    public function isi(int $userId): array
    {
        $items = $this->select('keranjang.id, keranjang.qty, keranjang.harga AS harga_lama, produk.id AS produk_id, produk.nama, produk.slug, produk.harga, produk.stok, produk.gambar, produk.berat')
            ->select('produk.aktif * kategori.aktif AS aktif', false)
            ->join('produk', 'produk.id = keranjang.produk_id')
            ->join('kategori', 'kategori.id = produk.kategori_id')
            ->where('keranjang.user_id', $userId)
            ->orderBy('keranjang.id')
            ->findAll();

        $subtotal     = 0;
        $bermasalah   = false;
        $hargaBerubah = false;
        foreach ($items as &$it) {
            $it['berubah']  = (int) $it['harga'] !== (int) $it['harga_lama'];
            $it['subtotal'] = $it['harga'] * $it['qty'];
            $it['masalah']  = match (true) {
                ! $it['aktif']           => 'Produk sudah tidak dijual.',
                $it['qty'] > $it['stok'] => "Stok tersisa {$it['stok']}.",
                $it['berubah']           => 'Harga berubah dari ' . rupiah($it['harga_lama']) . '.',
                default                  => null,
            };
            $bermasalah   = $bermasalah || $it['masalah'] !== null;
            $hargaBerubah = $hargaBerubah || $it['berubah'];
            $subtotal += $it['subtotal'];
        }

        return ['items' => $items, 'subtotal' => $subtotal, 'bermasalah' => $bermasalah, 'hargaBerubah' => $hargaBerubah];
    }

    /** Pelanggan menyetujui harga terbaru: harga di keranjang disamakan dengan harga produk sekarang. */
    public function setujuiHarga(int $userId): int
    {
        $this->db->query('UPDATE keranjang k JOIN produk p ON p.id = k.produk_id SET k.harga = p.harga WHERE k.user_id = ? AND k.harga <> p.harga', [$userId]);

        return $this->db->affectedRows();
    }
}
