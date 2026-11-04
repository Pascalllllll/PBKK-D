<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Pesanan extends BaseController
{
    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $status = in_array($status, OrderModel::STATUS, true) ? $status : '';
        $model  = model(OrderModel::class);

        return view('admin/pesanan/index', [
            'title'  => 'Data Pesanan',
            'rows'   => $model->withPelanggan(null, $status)->paginate(10),
            'pager'  => $model->pager,
            'status' => $status,
        ]);
    }

    public function show(int $id): string
    {
        $order = model(OrderModel::class)->withPelanggan()->where('orders.id', $id)->first()
            ?? throw PageNotFoundException::forPageNotFound();

        return view('admin/pesanan/show', [
            'title' => 'Pesanan ' . kode_pesanan($id),
            'order' => $order,
            'items' => model(OrderItemModel::class)->forOrder($id),
        ]);
    }

    public function status(int $id)
    {
        try {
            model(OrderModel::class)->ubahStatus($id, (string) $this->request->getPost('status'));
        } catch (RuntimeException $e) {
            return redirect()->to("/admin/pesanan/{$id}")->with('error', $e->getMessage());
        }

        return redirect()->to("/admin/pesanan/{$id}")->with('success', 'Status pesanan diperbarui.');
    }
}
