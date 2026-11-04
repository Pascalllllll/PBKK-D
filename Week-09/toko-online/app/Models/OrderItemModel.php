<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table         = 'order_items';
    protected $returnType    = 'array';
    protected $allowedFields = ['order_id', 'produk_id', 'qty', 'harga'];

    public function forOrder(int $orderId): array
    {
        return $this->select('order_items.*, produk.nama, produk.gambar, kategori.ikon')
            ->join('produk', 'produk.id = order_items.produk_id')
            ->join('kategori', 'kategori.id = produk.kategori_id')
            ->where('order_id', $orderId)
            ->findAll();
    }
}
