<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    public const STATUS = ['menunggu_bayar', 'menunggu_verifikasi', 'diproses', 'dikirim', 'selesai', 'batal'];

    /** Status yang dihitung sebagai omzet (pembayaran sudah diterima). */
    public const SUDAH_DIBAYAR = ['diproses', 'dikirim', 'selesai'];

    protected $table         = 'pesanan';
    protected $returnType    = 'array';
    protected $allowedFields = ['kode', 'user_id', 'penerima', 'telepon', 'alamat', 'subtotal', 'ongkir', 'total', 'catatan', 'status'];
    protected $useTimestamps = true;

    /**
     * Pesanan beserta nama pelanggan, jumlah barang, dan status pembayaran.
     *
     * @param array{user?: int, status?: string, q?: string, dari?: string, sampai?: string} $filter
     */
    public function daftar(array $filter = []): self
    {
        $this->select('pesanan.*, users.nama AS pelanggan, users.email, pembayaran.status AS status_bayar,
                (SELECT SUM(qty) FROM pesanan_item WHERE pesanan_id = pesanan.id) AS jumlah_barang')
            ->join('users', 'users.id = pesanan.user_id')
            ->join('pembayaran', 'pembayaran.pesanan_id = pesanan.id', 'left');

        if (! empty($filter['user'])) {
            $this->where('pesanan.user_id', $filter['user']);
        }
        if (in_array($filter['status'] ?? '', self::STATUS, true)) {
            $this->where('pesanan.status', $filter['status']);
        }
        if (($q = trim($filter['q'] ?? '')) !== '') {
            $this->groupStart()->like('pesanan.kode', $q)->orLike('users.nama', $q)->groupEnd();
        }
        if (! empty($filter['dari'])) {
            $this->where('pesanan.created_at >=', $filter['dari'] . ' 00:00:00');
        }
        if (! empty($filter['sampai'])) {
            $this->where('pesanan.created_at <=', $filter['sampai'] . ' 23:59:59');
        }

        return $this->orderBy('pesanan.created_at', 'DESC')->orderBy('pesanan.id', 'DESC');
    }

    public function milik(string $kode, int $userId): ?array
    {
        return $this->where(['kode' => $kode, 'user_id' => $userId])->first();
    }

    /** @return array<string, int> [status => jumlah] */
    public function jumlahPerStatus(): array
    {
        $rows = $this->builder()->select('status, COUNT(*) AS n')->groupBy('status')->get()->getResultArray();

        return array_map('intval', array_column($rows, 'n', 'status')) + array_fill_keys(self::STATUS, 0);
    }

    public function omzet(?string $dari = null, ?string $sampai = null): int
    {
        $b = $this->builder()->selectSum('total')->whereIn('status', self::SUDAH_DIBAYAR);
        if ($dari) {
            $b->where('created_at >=', $dari . ' 00:00:00');
        }
        if ($sampai) {
            $b->where('created_at <=', $sampai . ' 23:59:59');
        }

        return (int) ($b->get()->getRowArray()['total'] ?? 0);
    }

    /**
     * Omzet harian N hari terakhir untuk grafik dashboard. Hari tanpa penjualan tetap muncul dengan nilai 0.
     *
     * @return array<string, int> ['2026-11-04' => 450000, ...]
     */
    public function omzetHarian(int $hari = 7): array
    {
        $mulai = date('Y-m-d', strtotime('-' . ($hari - 1) . ' days'));
        $rows  = $this->builder()
            ->select('DATE(created_at) AS tgl, SUM(total) AS omzet')
            ->whereIn('status', self::SUDAH_DIBAYAR)
            ->where('created_at >=', $mulai . ' 00:00:00')
            ->groupBy('tgl')
            ->get()->getResultArray();

        $hasil = [];
        for ($i = 0; $i < $hari; $i++) {
            $hasil[date('Y-m-d', strtotime("{$mulai} +{$i} days"))] = 0;
        }

        return array_merge($hasil, array_map('intval', array_column($rows, 'omzet', 'tgl')));
    }

    /** Penjualan per kategori dalam satu periode (untuk laporan). */
    public function perKategori(string $dari, string $sampai): array
    {
        return $this->db->table('pesanan_item pi')
            ->select('kategori.nama AS kategori, SUM(pi.qty) AS qty, SUM(pi.subtotal) AS omzet')
            ->join('pesanan p', 'p.id = pi.pesanan_id')
            ->join('produk', 'produk.id = pi.produk_id')
            ->join('kategori', 'kategori.id = produk.kategori_id')
            ->whereIn('p.status', self::SUDAH_DIBAYAR)
            ->where('p.created_at >=', $dari . ' 00:00:00')
            ->where('p.created_at <=', $sampai . ' 23:59:59')
            ->groupBy('kategori.id')
            ->orderBy('omzet', 'DESC')
            ->get()->getResultArray();
    }
}
