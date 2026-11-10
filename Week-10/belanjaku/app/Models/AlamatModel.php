<?php

namespace App\Models;

use CodeIgniter\Model;

class AlamatModel extends Model
{
    protected $table           = 'alamat';
    protected $returnType      = 'array';
    protected $allowedFields   = ['user_id', 'label', 'penerima', 'telepon', 'alamat', 'kota', 'kode_pos'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'label'    => ['label' => 'Label alamat', 'rules' => 'required|max_length[30]'],
        'penerima' => ['label' => 'Nama penerima', 'rules' => 'required|min_length[3]|max_length[100]'],
        'telepon'  => ['label' => 'Nomor HP', 'rules' => 'required|regex_match[/^(\+62|0)8[0-9]{8,12}$/]'],
        'alamat'   => ['label' => 'Alamat lengkap', 'rules' => 'required|min_length[10]|max_length[255]'],
        'kota'     => ['label' => 'Kota', 'rules' => 'required|max_length[60]'],
        'kode_pos' => ['label' => 'Kode pos', 'rules' => 'required|exact_length[5]|numeric'],
    ];
    protected $validationMessages = [
        'telepon' => ['regex_match' => 'Nomor HP harus diawali 08 atau +628, contoh 081234567890.'],
    ];

    public function milik(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('id')->findAll();
    }

    /** Teks alamat yang disalin ke pesanan. */
    public static function format(array $a): string
    {
        return "{$a['alamat']}, {$a['kota']} {$a['kode_pos']}";
    }
}
