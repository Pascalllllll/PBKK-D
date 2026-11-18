<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table         = 'pembayaran';
    protected $returnType    = 'array';
    protected $allowedFields = ['pesanan_id', 'jumlah', 'bank_pengirim', 'nama_rekening', 'bukti', 'status', 'alasan_tolak', 'diunggah_at', 'diverifikasi_oleh', 'diverifikasi_at'];
    protected $useTimestamps = true;

    public function untuk(int $pesananId): ?array
    {
        return $this->select('pembayaran.*, users.nama AS verifikator')
            ->join('users', 'users.id = pembayaran.diverifikasi_oleh', 'left')
            ->where('pesanan_id', $pesananId)
            ->first();
    }

    /** Antrean bukti bayar yang menunggu diperiksa, yang paling lama di atas. */
    public function antrean(): array
    {
        return $this->select('pembayaran.*, pesanan.kode, pesanan.total, pesanan.created_at AS dipesan_at, users.nama AS pelanggan')
            ->join('pesanan', 'pesanan.id = pembayaran.pesanan_id')
            ->join('users', 'users.id = pesanan.user_id')
            ->where('pembayaran.status', 'menunggu')
            ->orderBy('pembayaran.diunggah_at')
            ->findAll();
    }

    /** Riwayat verifikasi terakhir. */
    public function riwayat(int $limit = 8): array
    {
        return $this->select('pembayaran.*, pesanan.kode, users.nama AS verifikator')
            ->join('pesanan', 'pesanan.id = pembayaran.pesanan_id')
            ->join('users', 'users.id = pembayaran.diverifikasi_oleh')
            ->whereIn('pembayaran.status', ['diterima', 'ditolak'])
            ->orderBy('pembayaran.diverifikasi_at', 'DESC')
            ->findAll($limit);
    }
}
