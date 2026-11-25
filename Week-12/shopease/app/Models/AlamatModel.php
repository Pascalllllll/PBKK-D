<?php

namespace App\Models;

use CodeIgniter\Model;

class AlamatModel extends Model
{
    protected $table           = 'alamat';
    protected $returnType      = 'array';
    protected $allowedFields   = ['user_id', 'label', 'penerima', 'telepon', 'alamat', 'kota', 'kode_pos', 'utama'];
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
        'label'    => ['required' => 'Beri label alamat, misalnya Rumah atau Kos.'],
        'penerima' => ['required' => 'Nama penerima wajib diisi.', 'min_length' => 'Nama penerima minimal 3 huruf.'],
        'telepon'  => ['required' => 'Nomor HP wajib diisi.', 'regex_match' => 'Nomor HP harus diawali 08 atau +628, contoh 081234567890.'],
        'alamat'   => ['required' => 'Alamat lengkap wajib diisi.', 'min_length' => 'Alamat terlalu pendek, sertakan nama jalan dan nomor.'],
        'kota'     => ['required' => 'Kota wajib diisi.'],
        'kode_pos' => ['required' => 'Kode pos wajib diisi.', 'exact_length' => 'Kode pos terdiri dari 5 angka.', 'numeric' => 'Kode pos hanya berisi angka.'],
    ];

    /** Alamat milik user, alamat utama paling atas. */
    public function milik(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('utama', 'DESC')->orderBy('id')->findAll();
    }

    public function milikUser(int $id, int $userId): ?array
    {
        return $this->where(['id' => $id, 'user_id' => $userId])->first();
    }

    /** Alamat pertama seorang user otomatis menjadi alamat utama. */
    public function tambah(int $userId, array $data): int|false
    {
        $pertama = $this->where('user_id', $userId)->countAllResults() === 0;

        return $this->insert($data + ['user_id' => $userId, 'utama' => $pertama ? 1 : 0]);
    }

    /** Tepat satu alamat utama per user: semua dimatikan lalu satu dinyalakan, dalam satu transaksi. */
    public function jadikanUtama(int $id, int $userId): void
    {
        $this->db->transStart();
        $this->builder()->where('user_id', $userId)->update(['utama' => 0]);
        $this->builder()->where(['id' => $id, 'user_id' => $userId])->update(['utama' => 1]);
        $this->db->transComplete();
    }

    /** Menghapus alamat. Jika yang dihapus alamat utama, alamat tertua yang tersisa menggantikannya. */
    public function hapusMilik(array $alamat): void
    {
        $this->delete($alamat['id']);

        if ($alamat['utama'] && ($pengganti = $this->where('user_id', $alamat['user_id'])->orderBy('id')->first())) {
            $this->jadikanUtama((int) $pengganti['id'], (int) $alamat['user_id']);
        }
    }

    /** Teks alamat yang disalin ke pesanan. */
    public static function format(array $a): string
    {
        return "{$a['alamat']}, {$a['kota']} {$a['kode_pos']}";
    }
}
