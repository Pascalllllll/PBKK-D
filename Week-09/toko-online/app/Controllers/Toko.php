<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Toko extends BaseController
{
    public function index(): string
    {
        $q        = trim((string) $this->request->getGet('q'));
        $kategori = (int) $this->request->getGet('kategori');
        $model    = model(ProdukModel::class);

        return view('toko/index', [
            'title'      => 'Katalog Produk',
            'produk'     => $model->daftar($q, $kategori)->paginate(9),
            'pager'      => $model->pager,
            'kategori'   => model(KategoriModel::class)->withJumlahProduk()->findAll(),
            'q'          => $q,
            'kategoriId' => $kategori,
        ]);
    }

    public function detail(int $id): string
    {
        $produk = model(ProdukModel::class)->detail($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('toko/detail', [
            'title'  => $produk['nama'],
            'produk' => $produk,
            'lain'   => model(ProdukModel::class)->daftar('', (int) $produk['kategori_id'])
                ->where('produk.id !=', $id)->findAll(4),
        ]);
    }
}
