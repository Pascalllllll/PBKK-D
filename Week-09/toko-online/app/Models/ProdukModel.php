<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table           = 'produk';
    protected $returnType      = 'array';
    protected $allowedFields   = ['kategori_id', 'nama', 'deskripsi', 'harga', 'stok', 'gambar'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'kategori_id' => ['label' => 'Kategori', 'rules' => 'required|is_not_unique[kategori.id]'],
        'nama'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[100]'],
        'deskripsi'   => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[2000]'],
        'harga'       => ['label' => 'Harga', 'rules' => 'required|is_natural_no_zero|less_than[1000000000]'],
        'stok'        => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than[100000]'],
    ];

    /**
     * Query daftar produk beserta nama kategori, dengan pencarian dan filter kategori.
     * Dipakai etalase dan halaman produk admin.
     */
    public function daftar(string $q = '', int $kategoriId = 0): self
    {
        $this->select('produk.*, kategori.nama AS kategori, kategori.ikon')
            ->join('kategori', 'kategori.id = produk.kategori_id');

        if ($q !== '') {
            $this->like('produk.nama', $q);
        }
        if ($kategoriId > 0) {
            $this->where('produk.kategori_id', $kategoriId);
        }

        return $this->orderBy('produk.nama');
    }

    public function detail(int $id): ?array
    {
        return $this->daftar()->where('produk.id', $id)->first();
    }
}
