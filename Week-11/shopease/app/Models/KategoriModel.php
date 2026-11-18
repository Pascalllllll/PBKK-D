<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    use PunyaSlug;

    protected $table           = 'kategori';
    protected $returnType      = 'array';
    protected $allowedFields   = ['nama', 'slug', 'deskripsi'];
    protected $useTimestamps   = true;
    protected $beforeInsert    = ['isiSlug'];
    protected $beforeUpdate    = ['isiSlug'];
    protected $validationRules = [
        'id'        => 'permit_empty|is_natural_no_zero',
        'nama'      => ['label' => 'Nama kategori', 'rules' => 'required|min_length[3]|max_length[60]|is_unique[kategori.nama,id,{id}]'],
        'deskripsi' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[255]'],
    ];
    protected $validationMessages = [
        'nama' => ['is_unique' => 'Kategori dengan nama ini sudah ada.'],
    ];

    /** Kategori beserta jumlah produk (semua dan yang aktif). */
    public function denganJumlahProduk(string $q = ''): self
    {
        $this->select('kategori.*, COUNT(produk.id) AS jumlah_produk, COALESCE(SUM(produk.aktif), 0) AS jumlah_aktif')
            ->join('produk', 'produk.kategori_id = kategori.id', 'left')
            ->groupBy('kategori.id');

        if ($q !== '') {
            $this->like('kategori.nama', $q);
        }

        return $this->orderBy('kategori.nama');
    }

    /** @return array<int, string> [id => nama] untuk dropdown */
    public function pilihan(): array
    {
        return array_column($this->orderBy('nama')->findAll(), 'nama', 'id');
    }
}
