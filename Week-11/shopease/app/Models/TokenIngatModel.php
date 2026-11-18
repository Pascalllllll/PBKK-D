<?php

namespace App\Models;

use CodeIgniter\Model;

class TokenIngatModel extends Model
{
    protected $table         = 'token_ingat';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'selector', 'validator_hash', 'perangkat', 'kedaluwarsa', 'dipakai_pada'];
    protected $useTimestamps = true;
    protected $updatedField  = '';

    /** Token yang masih berlaku untuk selector ini. */
    public function berlaku(string $selector): ?array
    {
        return $this->where('selector', $selector)->where('kedaluwarsa >', date('Y-m-d H:i:s'))->first();
    }

    public function milik(int $userId): array
    {
        return $this->where('user_id', $userId)->where('kedaluwarsa >', date('Y-m-d H:i:s'))
            ->orderBy('created_at', 'DESC')->findAll();
    }

    public function bersihkan(): void
    {
        $this->where('kedaluwarsa <=', date('Y-m-d H:i:s'))->delete();
    }
}
