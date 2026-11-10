<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    use PunyaSlug;

    public const URUTAN = [
        'terbaru'    => ['produk.id', 'DESC', 'Terbaru'],
        'termurah'   => ['produk.harga', 'ASC', 'Harga termurah'],
        'termahal'   => ['produk.harga', 'DESC', 'Harga termahal'],
        'nama'       => ['produk.nama', 'ASC', 'Nama A-Z'],
    ];

    protected $table           = 'produk';
    protected $returnType      = 'array';
    protected $allowedFields   = ['kategori_id', 'nama', 'slug', 'deskripsi', 'harga', 'stok', 'berat', 'gambar', 'aktif'];
    protected $useTimestamps   = true;
    protected $beforeInsert    = ['isiSlug'];
    protected $beforeUpdate    = ['isiSlug'];
    protected $validationRules = [
        'kategori_id' => ['label' => 'Kategori', 'rules' => 'required|is_not_unique[kategori.id]'],
        'nama'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[120]'],
        'deskripsi'   => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[3000]'],
        'harga'       => ['label' => 'Harga', 'rules' => 'required|is_natural_no_zero|less_than[1000000000]'],
        'stok'        => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than[100000]'],
        'berat'       => ['label' => 'Berat', 'rules' => 'required|is_natural_no_zero|less_than[100000]'],
        'aktif'       => ['label' => 'Status', 'rules' => 'required|in_list[0,1]'],
    ];
    protected $validationMessages = [
        'kategori_id' => ['is_not_unique' => 'Pilih kategori produk.'],
    ];

    /**
     * Query produk beserta nama kategori. Dipakai katalog (hanya aktif) dan panel admin (semua).
     *
     * @param array{q?: string, kategori?: int, urut?: string, aktif?: bool|null} $filter
     */
    public function cari(array $filter = []): self
    {
        $this->select('produk.*, kategori.nama AS kategori, kategori.slug AS kategori_slug')
            ->join('kategori', 'kategori.id = produk.kategori_id');

        if (($filter['aktif'] ?? null) !== null) {
            $this->where('produk.aktif', $filter['aktif'] ? 1 : 0);
        }
        if (($q = trim($filter['q'] ?? '')) !== '') {
            $this->groupStart()->like('produk.nama', $q)->orLike('produk.deskripsi', $q)->groupEnd();
        }
        if (! empty($filter['kategori'])) {
            $this->where('produk.kategori_id', (int) $filter['kategori']);
        }

        [$kolom, $arah] = self::URUTAN[$filter['urut'] ?? ''] ?? self::URUTAN['terbaru'];

        return $this->orderBy($kolom, $arah);
    }

    /** Produk aktif berdasarkan slug, untuk halaman detail. */
    public function aktifBySlug(string $slug): ?array
    {
        return $this->cari(['aktif' => true])->where('produk.slug', $slug)->first();
    }

    /** Produk aktif berdasarkan ID. Produk nonaktif dianggap tidak ada saat dibeli. */
    public function aktif(int $id): ?array
    {
        return $this->where(['id' => $id, 'aktif' => 1])->first();
    }

    /** Produk terlaris dari pesanan yang sudah dibayar. */
    public function terlaris(int $limit = 4): array
    {
        return $this->cari(['aktif' => true])
            ->select('SUM(pesanan_item.qty) AS terjual')
            ->join('pesanan_item', 'pesanan_item.produk_id = produk.id')
            ->join('pesanan', 'pesanan.id = pesanan_item.pesanan_id')
            ->whereIn('pesanan.status', PesananModel::SUDAH_DIBAYAR)
            ->groupBy('produk.id')
            ->orderBy('terjual', 'DESC', false)
            ->findAll($limit);
    }
}
