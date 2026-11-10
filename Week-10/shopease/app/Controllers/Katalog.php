<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Katalog extends BaseController
{
    public function index(): string
    {
        $filter = [
            'q'        => trim((string) $this->request->getGet('q')),
            'kategori' => (int) $this->request->getGet('kategori'),
            'urut'     => (string) $this->request->getGet('urut'),
            'aktif'    => true, // produk nonaktif tidak pernah tampil di katalog
        ];
        $model = model(ProdukModel::class);

        return view('toko/katalog', [
            'title'    => 'Katalog Produk',
            'produk'   => $model->cari($filter)->paginate(8),
            'pager'    => $model->pager,
            'total'    => $model->pager->getTotal(),
            'kategori' => model(KategoriModel::class)->denganJumlahProduk()->findAll(),
            'filter'   => $filter,
        ]);
    }

    public function detail(string $slug): string
    {
        $produk = model(ProdukModel::class)->aktifBySlug($slug) ?? throw PageNotFoundException::forPageNotFound();

        return view('toko/detail', [
            'title'  => $produk['nama'],
            'produk' => $produk,
            'serupa' => model(ProdukModel::class)->cari(['aktif' => true, 'kategori' => $produk['kategori_id']])
                ->where('produk.id !=', $produk['id'])->findAll(4),
        ]);
    }
}
