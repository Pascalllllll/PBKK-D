<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananItemModel extends Model
{
    protected $table         = 'pesanan_item';
    protected $returnType    = 'array';
    protected $allowedFields = ['pesanan_id', 'produk_id', 'nama_produk', 'harga', 'qty', 'subtotal'];

    /** Item pesanan. Nama dan harga diambil dari snapshot, gambar dari produk. */
    public function untuk(int $pesananId): array
    {
        return $this->select('pesanan_item.*, produk.gambar, produk.slug')
            ->join('produk', 'produk.id = pesanan_item.produk_id')
            ->where('pesanan_id', $pesananId)
            ->findAll();
    }
}
