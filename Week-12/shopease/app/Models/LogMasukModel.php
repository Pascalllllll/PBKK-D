<?php

namespace App\Models;

use CodeIgniter\Model;

class LogMasukModel extends Model
{
    protected $table         = 'log_masuk';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'email', 'hasil', 'ip', 'perangkat'];
    protected $useTimestamps = true;
    protected $updatedField  = '';

    public function terbaru(?int $userId = null, ?string $hasil = null): static
    {
        $this->select('log_masuk.*, users.nama')
            ->join('users', 'users.id = log_masuk.user_id', 'left')
            ->orderBy('log_masuk.id', 'DESC');

        if ($userId !== null) {
            $this->where('log_masuk.user_id', $userId);
        }
        if ($hasil) {
            $this->where('log_masuk.hasil', $hasil);
        }

        return $this;
    }
}
