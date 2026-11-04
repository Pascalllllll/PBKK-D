<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class OrderModel extends Model
{
    public const STATUS = ['pending', 'dibayar', 'dikirim', 'selesai', 'batal'];

    /** Perpindahan status yang diizinkan. Status selesai dan batal bersifat final. */
    public const ALUR = [
        'pending' => ['dibayar', 'batal'],
        'dibayar' => ['dikirim', 'batal'],
        'dikirim' => ['selesai'],
        'selesai' => [],
        'batal'   => [],
    ];

    protected $table         = 'orders';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'total', 'nama_penerima', 'telepon', 'alamat', 'status'];
    protected $useTimestamps = true;

    /**
     * Membuat pesanan dari isi keranjang dalam satu transaksi.
     * Baris produk dikunci (SELECT ... FOR UPDATE) supaya stok tidak bisa minus
     * walaupun dua pelanggan checkout bersamaan.
     *
     * @param list<array{id: int, qty: int}> $items
     *
     * @throws RuntimeException jika stok tidak cukup atau produk hilang
     */
    public function buat(int $userId, array $items, array $pengiriman): int
    {
        $db = $this->db;
        $db->transBegin();

        try {
            $total = 0;
            $baris = [];

            foreach ($items as $item) {
                $p = $db->query('SELECT id, nama, harga, stok FROM produk WHERE id = ? FOR UPDATE', [$item['id']])->getRowArray();
                if ($p === null) {
                    throw new RuntimeException('Ada produk di keranjang yang sudah tidak tersedia.');
                }
                if ($p['stok'] < $item['qty']) {
                    throw new RuntimeException("Stok \"{$p['nama']}\" tidak mencukupi (tersisa {$p['stok']}).");
                }

                $total += $p['harga'] * $item['qty'];
                $baris[] = ['produk_id' => $p['id'], 'qty' => $item['qty'], 'harga' => $p['harga']];
                $db->query('UPDATE produk SET stok = stok - ? WHERE id = ?', [$item['qty'], $p['id']]);
            }

            $orderId = $this->insert($pengiriman + ['user_id' => $userId, 'total' => $total, 'status' => 'pending'], true);
            $itemModel = model(OrderItemModel::class);
            foreach ($baris as $b) {
                $itemModel->insert($b + ['order_id' => $orderId]);
            }

            $db->transCommit();

            return (int) $orderId;
        } catch (\Throwable $e) {
            $db->transRollback();

            throw $e;
        }
    }

    /**
     * Mengubah status pesanan sesuai ALUR. Pembatalan mengembalikan stok
     * di transaksi yang sama. Baris order dikunci supaya stok tidak dikembalikan dua kali.
     *
     * @throws RuntimeException jika perpindahan status tidak diizinkan
     */
    public function ubahStatus(int $id, string $status): void
    {
        $db = $this->db;
        $db->transBegin();

        try {
            $order = $db->query('SELECT id, status FROM orders WHERE id = ? FOR UPDATE', [$id])->getRowArray();
            if ($order === null) {
                throw new RuntimeException('Pesanan tidak ditemukan.');
            }
            if (! in_array($status, self::ALUR[$order['status']], true)) {
                throw new RuntimeException("Status tidak bisa diubah dari {$order['status']} ke {$status}.");
            }

            if ($status === 'batal') {
                foreach (model(OrderItemModel::class)->where('order_id', $id)->findAll() as $it) {
                    $db->query('UPDATE produk SET stok = stok + ? WHERE id = ?', [$it['qty'], $it['produk_id']]);
                }
            }

            $this->update($id, ['status' => $status]);
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();

            throw $e;
        }
    }

    /** Pesanan beserta nama pelanggan dan jumlah item. */
    public function withPelanggan(?int $userId = null, string $status = ''): self
    {
        $this->select('orders.*, users.nama AS pelanggan, (SELECT SUM(qty) FROM order_items WHERE order_id = orders.id) AS jumlah_item')
            ->join('users', 'users.id = orders.user_id');

        if ($userId !== null) {
            $this->where('orders.user_id', $userId);
        }
        if ($status !== '') {
            $this->where('orders.status', $status);
        }

        return $this->orderBy('orders.id', 'DESC');
    }

    /** Total pendapatan dari pesanan yang sudah dibayar. */
    public function pendapatan(): int
    {
        $row = $this->builder()->selectSum('total')->whereIn('status', ['dibayar', 'dikirim', 'selesai'])->get()->getRowArray();

        return (int) ($row['total'] ?? 0);
    }
}
