<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
    use HasUserAccount;

    protected $table           = 'dosen';
    protected $returnType      = 'array';
    protected $allowedFields   = ['user_id', 'nidn', 'nama', 'email'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'id'    => 'permit_empty|is_natural_no_zero',
        'nidn'  => ['label' => 'NIDN', 'rules' => 'required|numeric|min_length[8]|max_length[20]|is_unique[dosen.nidn,id,{id}]'],
        'nama'  => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'email' => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[100]'],
    ];
    protected $validationMessages = [
        'nidn' => ['is_unique' => 'NIDN sudah terdaftar.'],
    ];

    protected function nomorInduk(): string
    {
        return 'nidn';
    }

    protected function role(): string
    {
        return 'dosen';
    }

    public function search(string $q): self
    {
        if ($q !== '') {
            $this->groupStart()->like('nama', $q)->orLike('nidn', $q)->groupEnd();
        }

        return $this->orderBy('nama');
    }

    public function findByUser(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /** Pilihan dropdown [id => nama]. */
    public function options(): array
    {
        return array_column($this->orderBy('nama')->findAll(), 'nama', 'id');
    }
}
