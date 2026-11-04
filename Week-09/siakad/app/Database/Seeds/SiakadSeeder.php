<?php

namespace App\Database\Seeds;

use App\Models\DosenModel;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class SiakadSeeder extends Seeder
{
    /** Foto contoh di Seeds/img disalin ke folder upload dengan nama acak, sama seperti hasil upload. */
    private function salinFoto(?string $nama): ?string
    {
        if ($nama === null) {
            return null;
        }
        $tujuan = FCPATH . 'uploads/foto/';
        is_dir($tujuan) || mkdir($tujuan, 0775, true);
        $file = bin2hex(random_bytes(10)) . '.jpg';
        copy(__DIR__ . "/img/{$nama}.jpg", $tujuan . $file);

        return $file;
    }

    public function run()
    {
        $users = model(UserModel::class);
        $users->insert(['username' => 'admin', 'password' => 'admin123', 'nama' => 'Administrator', 'role' => 'admin']);

        // Username dan password awal dosen = NIDN
        $dosen = [];
        foreach ([
            ['0011223301', 'Dr. Rina Kartika, S.Kom., M.Kom.', 'rina@its.ac.id'],
            ['0011223302', 'Bagus Pratama, S.T., M.T.', 'bagus@its.ac.id'],
            ['0011223303', 'Prof. Dewi Lestari, Ph.D.', 'dewi@its.ac.id'],
        ] as [$nidn, $nama, $email]) {
            $userId  = $users->insert(['username' => $nidn, 'password' => $nidn, 'nama' => $nama, 'role' => 'dosen']);
            $dosen[] = model(DosenModel::class)->insert(['user_id' => $userId, 'nidn' => $nidn, 'nama' => $nama, 'email' => $email]);
        }

        $mk = [];
        foreach ([
            ['IF184501', 'Pemrograman Berbasis Kerangka Kerja', 3, 5, 0],
            ['IF184502', 'Basis Data Lanjut', 3, 5, 1],
            ['IF184503', 'Kecerdasan Buatan', 3, 5, 2],
            ['IF184504', 'Rekayasa Perangkat Lunak', 3, 5, 0],
            ['IF184505', 'Jaringan Komputer', 4, 5, 1],
            ['IF184301', 'Struktur Data', 4, 3, 2],
            ['IF184302', 'Sistem Operasi', 3, 3, 1],
            ['IF184303', 'Matematika Diskrit', 3, 3, 0],
        ] as [$kode, $nama, $sks, $sem, $d]) {
            $mk[$kode] = model(MatakuliahModel::class)->insert([
                'kode' => $kode, 'nama' => $nama, 'sks' => $sks, 'semester' => $sem, 'dosen_id' => $dosen[$d],
            ]);
        }

        // Username dan password awal mahasiswa = NRP
        $mhs = [];
        foreach ([
            ['5025241177', 'Hosea Felix Sanjaya', 2024, 0],
            ['5025241001', 'Andi Saputra', 2024, 0],
            ['5025241002', 'Bella Anggraini', 2024, 1],
            ['5025241003', 'Choirul Anam', 2024, 1],
            ['5025241004', 'Dinda Maharani', 2024, 2],
            ['5025241005', 'Eko Prasetyo', 2024, 2],
            ['5025231010', 'Fajar Nugroho', 2023, 0],
            ['5025231011', 'Gita Permata', 2023, 1],
            ['5025231012', 'Hendra Wijaya', 2023, 2],
            ['5025231013', 'Indah Puspita', 2023, 0],
            ['5025231014', 'Joko Susilo', 2023, 1],
            ['5025231015', 'Kirana Salsabila', 2023, 2],
        ] as $i => [$nrp, $nama, $angkatan, $wali]) {
            $userId    = $users->insert(['username' => $nrp, 'password' => $nrp, 'nama' => $nama, 'role' => 'mahasiswa']);
            $mhs[$nrp] = model(MahasiswaModel::class)->insert([
                'user_id' => $userId, 'nrp' => $nrp, 'nama' => $nama, 'prodi' => 'Teknik Informatika',
                'angkatan' => $angkatan, 'dosen_wali_id' => $dosen[$wali],
                // Sebagian mahasiswa diberi foto contoh, sisanya memakai avatar bawaan
                'foto' => $this->salinFoto($i < 6 ? 'm' . ($i + 1) : null),
            ]);
        }

        // Riwayat KRS semester lalu (sudah dinilai) dan semester berjalan
        $krs = $this->db->table('krs');
        $now = date('Y-m-d H:i:s');
        foreach (['5025241177' => ['A', 'AB', 'B'], '5025241001' => ['B', 'BC', 'A'], '5025241002' => ['AB', 'A', 'C']] as $nrp => $nilai) {
            foreach (['IF184301', 'IF184302', 'IF184303'] as $i => $kode) {
                $krs->insert(['mahasiswa_id' => $mhs[$nrp], 'matakuliah_id' => $mk[$kode], 'tahun_ajaran' => '2025/2026 Genap',
                    'nilai' => $nilai[$i], 'created_at' => $now, 'updated_at' => $now]);
            }
            foreach (['IF184501', 'IF184502'] as $kode) {
                $krs->insert(['mahasiswa_id' => $mhs[$nrp], 'matakuliah_id' => $mk[$kode], 'tahun_ajaran' => KrsModel::TAHUN_AJARAN,
                    'nilai' => null, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }
}
