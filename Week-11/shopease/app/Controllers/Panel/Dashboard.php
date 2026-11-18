<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $pesanan = model(PesananModel::class);
        $harian  = $pesanan->omzetHarian(7);

        return view('panel/dashboard', [
            'title'      => 'Dashboard',
            'omzet'      => $pesanan->omzet(),
            'omzetBulan' => $pesanan->omzet(date('Y-m-01'), date('Y-m-t')),
            'perStatus'  => $pesanan->jumlahPerStatus(),
            'pelanggan'  => model(UserModel::class)->where('role', 'customer')->countAllResults(),
            'antrean'    => model(PembayaranModel::class)->where('status', 'menunggu')->countAllResults(),
            'grafik'     => ['label' => array_map(static fn ($t) => tanggal($t, false), array_keys($harian)), 'nilai' => array_values($harian)],
            'terbaru'    => $pesanan->daftar()->findAll(6),
            'stokTipis'  => model(ProdukModel::class)->where(['aktif' => 1, 'stok <' => 5])->orderBy('stok')->findAll(5),
            'terlaris'   => model(ProdukModel::class)->terlaris(5),
        ]);
    }
}
