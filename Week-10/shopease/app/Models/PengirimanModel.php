<?php

namespace App\Models;

use CodeIgniter\Model;

class PengirimanModel extends Model
{
    public const KURIR = ['JNE', 'J&T', 'SiCepat', 'AnterAja', 'POS Indonesia'];

    protected $table           = 'pengiriman';
    protected $returnType      = 'array';
    protected $allowedFields   = ['pesanan_id', 'kurir', 'no_resi', 'diinput_oleh', 'dikirim_at', 'diterima_at'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'kurir'   => ['label' => 'Kurir', 'rules' => 'required|in_list[JNE,J&T,SiCepat,AnterAja,POS Indonesia]'],
        'no_resi' => ['label' => 'Nomor resi', 'rules' => 'required|alpha_numeric|min_length[8]|max_length[40]'],
    ];

    public function untuk(int $pesananId): ?array
    {
        return $this->select('pengiriman.*, users.nama AS petugas')
            ->join('users', 'users.id = pengiriman.diinput_oleh', 'left')
            ->where('pesanan_id', $pesananId)
            ->first();
    }
}
