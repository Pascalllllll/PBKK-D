<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;

class Beranda extends BaseController
{
    public function index(): string
    {
        return view('toko/beranda', [
            'title'    => 'Beranda',
            'kategori' => model(KategoriModel::class)->denganJumlahProduk()->findAll(),
            'terbaru'  => model(ProdukModel::class)->cari(['aktif' => true, 'urut' => 'terbaru'])->findAll(8),
            'terlaris' => model(ProdukModel::class)->terlaris(4),
        ]);
    }

    public function tentang(): string
    {
        return view('toko/tentang', ['title' => 'Tentang Kami']);
    }

    public function kontak(): string
    {
        return view('toko/kontak', ['title' => 'Hubungi Kami']);
    }

    /** Dipasang lewat set404Override: route tidak ada, atau controller melempar PageNotFoundException. */
    public function tidakDitemukan(): string
    {
        $this->response->setStatusCode(404);

        return view('errors/tidak_ditemukan', [
            'title' => 'Halaman Tidak Ditemukan',
            'path'  => '/' . ltrim($this->request->getUri()->getPath(), '/'),
        ]);
    }
}
