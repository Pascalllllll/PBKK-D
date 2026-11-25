<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    /** Huruf dan angka, 8-72 karakter. bcrypt hanya membaca 72 byte pertama, jadi sisanya ditolak. */
    public const ATURAN_SANDI = 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]';

    public const PESAN_SANDI = [
        'min_length'  => '{field} minimal 8 karakter.',
        'max_length'  => '{field} maksimal 72 karakter.',
        'regex_match' => '{field} harus berisi huruf dan angka.',
    ];

    /** Hash pengganti saat email tidak ditemukan, supaya waktu respons sama dengan sandi salah. */
    private const HASH_PALSU = '$2y$10$CnLS.tVMy4WeV9lXJ6NYnOdhQxm1naYYQ9e18vHbHiPEa7aULdUPC';

    protected $table           = 'users';
    protected $returnType      = 'array';
    protected $allowedFields   = ['nama', 'email', 'telepon', 'password', 'role', 'aktif', 'versi_sesi', 'login_terakhir'];
    protected $useTimestamps   = true;
    protected $beforeInsert    = ['hashPassword'];
    protected $beforeUpdate    = ['hashPassword'];
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'nama'     => ['label' => 'Nama lengkap', 'rules' => 'required|min_length[3]|max_length[100]'],
        'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]'],
        'telepon'  => ['label' => 'Nomor HP', 'rules' => 'permit_empty|regex_match[/^(\+62|0)8[0-9]{8,12}$/]'],
        'password' => ['label' => 'Kata sandi', 'rules' => self::ATURAN_SANDI],
        'role'     => ['label' => 'Role', 'rules' => 'permit_empty|in_list[customer,gudang,admin]'],
    ];
    protected $validationMessages = [
        'nama'     => ['min_length' => 'Nama minimal 3 huruf.', 'max_length' => 'Nama maksimal 100 karakter.'],
        'email'    => ['is_unique' => 'Email ini sudah dipakai akun lain.'],
        'telepon'  => ['regex_match' => 'Nomor HP harus diawali 08 atau +628, contoh 081234567890.'],
        'password' => self::PESAN_SANDI,
        'role'     => ['in_list' => 'Role tidak dikenal.'],
    ];

    /** Field `password` hanya ada di input. Sebelum disimpan, isinya selalu diganti hash bcrypt. */
    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']);
        }

        return $data;
    }

    public function cariEmail(string $email): ?array
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }

    /**
     * User jika email dan sandi cocok, selain itu null.
     * password_verify tetap dijalankan walaupun email tidak ada, agar lama respons tidak membocorkan email terdaftar.
     */
    public function periksa(string $email, string $password): ?array
    {
        $user = $this->cariEmail($email);

        if (! password_verify($password, $user['password'] ?? self::HASH_PALSU) || $user === null) {
            return null;
        }

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $this->update($user['id'], ['password' => $password]);
        }

        return $user;
    }

    public function cocokSandi(int $id, string $password): bool
    {
        $hash = $this->select('password')->find($id)['password'] ?? self::HASH_PALSU;

        return password_verify($password, $hash);
    }

    /** Membuat semua session dan token "Ingat saya" milik user ini tidak berlaku lagi. */
    public function cabutSemuaSesi(int $id): void
    {
        $this->builder()->set('versi_sesi', 'versi_sesi + 1', false)->where('id', $id)->update();
        model(TokenIngatModel::class)->where('user_id', $id)->delete();
    }

    /** Isi session: secukupnya untuk tampilan, tanpa hash sandi. */
    public static function payload(array $user): array
    {
        return [
            'id'    => (int) $user['id'],
            'nama'  => $user['nama'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'versi' => (int) $user['versi_sesi'],
        ];
    }

    /** Daftar pengguna untuk panel admin, dengan pencarian dan filter role/status. */
    public function daftar(array $filter): static
    {
        $this->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');

        if ($q = trim($filter['q'] ?? '')) {
            $this->groupStart()->like('nama', $q)->orLike('email', $q)->groupEnd();
        }
        if (in_array($filter['role'] ?? '', ['customer', 'gudang', 'admin'], true)) {
            $this->where('role', $filter['role']);
        }
        if (in_array($filter['status'] ?? '', ['0', '1'], true)) {
            $this->where('aktif', (int) $filter['status']);
        }

        return $this;
    }

    /** Jumlah akun per role, dipisah aktif dan dibekukan. */
    public function ringkasan(): array
    {
        $hasil = [];
        foreach ($this->select('role, aktif, COUNT(*) AS n')->groupBy('role, aktif')->findAll() as $r) {
            $hasil[$r['role']][(int) $r['aktif']] = (int) $r['n'];
        }

        return $hasil;
    }
}
