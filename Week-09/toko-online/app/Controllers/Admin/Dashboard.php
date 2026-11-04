<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\ProdukModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $orders = model(OrderModel::class);

        return view('admin/dashboard', [
            'title'      => 'Dashboard',
            'produk'     => model(ProdukModel::class)->countAllResults(),
            'pesanan'    => $orders->countAllResults(),
            'pelanggan'  => model(UserModel::class)->where('role', 'customer')->countAllResults(),
            'pendapatan' => $orders->pendapatan(),
            'perStatus'  => array_column(
                $orders->builder()->select('status, COUNT(*) AS jumlah')->groupBy('status')->get()->getResultArray(),
                'jumlah',
                'status',
            ),
            'terbaru'    => $orders->withPelanggan()->findAll(5),
            'stokTipis'  => model(ProdukModel::class)->where('stok <', 5)->orderBy('stok')->findAll(5),
        ]);
    }
}
