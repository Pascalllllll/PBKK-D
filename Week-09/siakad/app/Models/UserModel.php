<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $returnType    = 'array';
    protected $allowedFields = ['username', 'password', 'nama', 'role'];
    protected $useTimestamps = true;
    protected $beforeInsert  = ['hashPassword'];
    protected $beforeUpdate  = ['hashPassword'];

    /**
     * Password selalu disimpan dalam bentuk hash. Password kosong saat update
     * berarti tidak diganti.
     */
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
