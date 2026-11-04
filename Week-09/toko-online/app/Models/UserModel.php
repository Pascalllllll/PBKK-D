<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table           = 'users';
    protected $returnType      = 'array';
    protected $allowedFields   = ['username', 'password', 'nama', 'email', 'role'];
    protected $useTimestamps   = true;
    protected $beforeInsert    = ['hashPassword'];
    protected $beforeUpdate    = ['hashPassword'];
    protected $validationRules = [
        'username' => ['label' => 'Username', 'rules' => 'required|alpha_numeric|min_length[4]|max_length[50]|is_unique[users.username]'],
        'nama'     => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[100]|is_unique[users.email]'],
        'password' => ['label' => 'Password', 'rules' => 'required|min_length[6]'],
    ];
    protected $validationMessages = [
        'username' => ['is_unique' => 'Username sudah dipakai.'],
        'email'    => ['is_unique' => 'Email sudah terdaftar.'],
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

    public function attempt(string $username, string $password): ?array
    {
        $user = $this->where('username', $username)->first();

        return ($user && password_verify($password, $user['password'])) ? $user : null;
    }
}
