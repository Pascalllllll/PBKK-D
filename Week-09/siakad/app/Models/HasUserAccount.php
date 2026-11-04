<?php

namespace App\Models;

use RuntimeException;

/**
 * Dipakai DosenModel dan MahasiswaModel: profil selalu punya akun login di tabel users.
 * Akun dan profil dibuat/diubah dalam satu transaksi supaya tidak ada akun tanpa profil.
 * Username dan password awal = nomor induk (NIDN / NRP).
 */
trait HasUserAccount
{
    abstract protected function nomorInduk(): string;

    abstract protected function role(): string;

    public function createWithAccount(array $data): int
    {
        $users = model(UserModel::class);
        $nomor = $data[$this->nomorInduk()];

        if ($users->where('username', $nomor)->countAllResults() > 0) {
            throw new RuntimeException("Username {$nomor} sudah dipakai akun lain.");
        }

        $this->db->transException(true)->transStart();
        $data['user_id'] = $users->insert([
            'username' => $nomor, 'password' => $nomor, 'nama' => $data['nama'], 'role' => $this->role(),
        ]);
        $id = $this->insert($data);
        $this->db->transComplete();

        return (int) $id;
    }

    public function updateWithAccount(array $row, array $data): void
    {
        $this->db->transException(true)->transStart();
        // id ikut dikirim untuk placeholder {id} pada aturan is_unique, lalu dibuang oleh allowedFields
        $this->update($row['id'], $data + ['id' => $row['id']]);
        model(UserModel::class)->update($row['user_id'], [
            'username' => $data[$this->nomorInduk()], 'nama' => $data['nama'],
        ]);
        $this->db->transComplete();
    }

    /** Menghapus akun; profil ikut terhapus lewat FK ON DELETE CASCADE. */
    public function deleteWithAccount(array $row): void
    {
        model(UserModel::class)->delete($row['user_id']);
    }
}
