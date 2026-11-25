<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananRiwayatModel extends Model
{
    protected $table         = 'pesanan_riwayat';
    protected $returnType    = 'array';
    protected $allowedFields = ['pesanan_id', 'dari', 'ke', 'oleh', 'catatan', 'created_at'];
    protected $useTimestamps = false;

    /** Riwayat satu pesanan, lengkap dengan nama dan role pelaku. */
    public function untuk(int $pesananId): array
    {
        return $this->select('pesanan_riwayat.*, users.nama AS pelaku, users.role AS role_pelaku')
            ->join('users', 'users.id = pesanan_riwayat.oleh', 'left')
            ->where('pesanan_id', $pesananId)
            ->orderBy('pesanan_riwayat.id')
            ->findAll();
    }
}
