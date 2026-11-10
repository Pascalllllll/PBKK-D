<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table           = 'users';
    protected $returnType      = 'array';
    protected $allowedFields   = ['nama', 'email', 'telepon', 'password', 'role'];
    protected $useTimestamps   = true;
    protected $beforeInsert    = ['hashPassword'];
    protected $beforeUpdate    = ['hashPassword'];
    protected $validationRules = [
        'nama'     => ['label' => 'Nama lengkap', 'rules' => 'required|min_length[3]|max_length[100]'],
        'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[100]|is_unique[users.email]'],
        'telepon'  => ['label' => 'Nomor HP', 'rules' => 'permit_empty|regex_match[/^(\+62|0)8[0-9]{8,12}$/]'],
        'password' => ['label' => 'Kata sandi', 'rules' => 'required|min_length[8]'],
    ];
    protected $validationMessages = [
        'email'   => ['is_unique' => 'Email ini sudah terdaftar. Silakan masuk.'],
        'telepon' => ['regex_match' => 'Nomor HP harus diawali 08 atau +628, contoh 081234567890.'],
    ];

    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']);
        }

        return $data;
    }

    public function cocokkan(string $email, string $password): ?array
    {
        $user = $this->where('email', strtolower($email))->first();

        return ($user && password_verify($password, $user['password'])) ? $user : null;
    }
}
