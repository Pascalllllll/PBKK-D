<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    use HasUserAccount;

    protected $table           = 'mahasiswa';
    protected $returnType      = 'array';
    protected $allowedFields   = ['user_id', 'nrp', 'nama', 'prodi', 'angkatan', 'dosen_wali_id', 'foto'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'id'            => 'permit_empty|is_natural_no_zero',
        'nrp'           => ['label' => 'NRP', 'rules' => 'required|numeric|exact_length[10]|is_unique[mahasiswa.nrp,id,{id}]'],
        'nama'          => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'prodi'         => ['label' => 'Program studi', 'rules' => 'required|max_length[100]'],
        'angkatan'      => ['label' => 'Angkatan', 'rules' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[2030]'],
        'dosen_wali_id' => ['label' => 'Dosen wali', 'rules' => 'permit_empty|is_not_unique[dosen.id]'],
    ];
    protected $validationMessages = [
        'nrp' => ['is_unique' => 'NRP sudah terdaftar.', 'exact_length' => 'NRP harus 10 digit.'],
    ];

    protected function nomorInduk(): string
    {
        return 'nrp';
    }

    protected function role(): string
    {
        return 'mahasiswa';
    }

    /** Daftar mahasiswa beserta nama dosen wali, dengan pencarian NRP/nama. */
    public function search(string $q): self
    {
        $this->select('mahasiswa.*, dosen.nama AS dosen_wali')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left');

        if ($q !== '') {
            $this->groupStart()->like('mahasiswa.nama', $q)->orLike('mahasiswa.nrp', $q)->groupEnd();
        }

        return $this->orderBy('mahasiswa.nrp');
    }

    public function findByUser(int $userId): ?array
    {
        return $this->select('mahasiswa.*, dosen.nama AS dosen_wali')
            ->join('dosen', 'dosen.id = mahasiswa.dosen_wali_id', 'left')
            ->where('mahasiswa.user_id', $userId)
            ->first();
    }
}
