<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Pesanan extends BaseController
{
    public function index(): string
    {
        return view('pesanan/index', [
            'title'   => 'Pesanan Saya',
            'pesanan' => model(OrderModel::class)->withPelanggan(user('id'))->findAll(),
        ]);
    }

    public function show(int $id): string
    {
        $order = $this->milikSendiri($id);

        return view('pesanan/show', [
            'title' => 'Pesanan ' . kode_pesanan($id),
            'order' => $order,
            'items' => model(OrderItemModel::class)->forOrder($id),
        ]);
    }

    public function batal(int $id)
    {
        $order = $this->milikSendiri($id);
        if ($order['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pesanan berstatus pending yang bisa dibatalkan.');
        }

        try {
            model(OrderModel::class)->ubahStatus($id, 'batal');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to("/pesanan/{$id}")->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    /** Pelanggan hanya bisa membuka pesanannya sendiri; pesanan orang lain dianggap tidak ada. */
    private function milikSendiri(int $id): array
    {
        return model(OrderModel::class)->where(['id' => $id, 'user_id' => user('id')])->first()
            ?? throw PageNotFoundException::forPageNotFound();
    }
}
