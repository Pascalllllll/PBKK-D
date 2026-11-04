<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table           = 'kategori';
    protected $returnType      = 'array';
    protected $allowedFields   = ['nama', 'ikon'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'id'   => 'permit_empty|is_natural_no_zero',
        'nama' => ['label' => 'Nama kategori', 'rules' => 'required|min_length[3]|max_length[50]|is_unique[kategori.nama,id,{id}]'],
        'ikon' => ['label' => 'Ikon', 'rules' => 'required|regex_match[/^fa-[a-z0-9-]+$/]'],
    ];
    protected $validationMessages = [
        'nama' => ['is_unique' => 'Kategori dengan nama ini sudah ada.'],
        'ikon' => ['regex_match' => 'Ikon harus nama ikon Font Awesome, contoh fa-laptop.'],
    ];

    /** Kategori beserta jumlah produknya. */
    public function withJumlahProduk(string $q = ''): self
    {
        $this->select('kategori.*, COUNT(produk.id) AS jumlah_produk')
            ->join('produk', 'produk.kategori_id = kategori.id', 'left')
            ->groupBy('kategori.id');

        if ($q !== '') {
            $this->like('kategori.nama', $q);
        }

        return $this->orderBy('kategori.nama');
    }

    public function options(): array
    {
        return array_column($this->orderBy('nama')->findAll(), 'nama', 'id');
    }
}
