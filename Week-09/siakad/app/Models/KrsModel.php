<?php

namespace App\Models;

use CodeIgniter\Model;

class KrsModel extends Model
{
    public const TAHUN_AJARAN = '2026/2027 Ganjil';
    public const MAX_SKS      = 24;
    public const NILAI        = ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'];

    protected $table         = 'krs';
    protected $returnType    = 'array';
    protected $allowedFields = ['mahasiswa_id', 'matakuliah_id', 'tahun_ajaran', 'nilai'];
    protected $useTimestamps = true;

    /** KRS mahasiswa beserta data matakuliah; default semester berjalan. */
    public function forMahasiswa(int $mahasiswaId, ?string $tahunAjaran = self::TAHUN_AJARAN): array
    {
        $this->select('krs.*, matakuliah.kode, matakuliah.nama, matakuliah.sks, dosen.nama AS dosen')
            ->join('matakuliah', 'matakuliah.id = krs.matakuliah_id')
            ->join('dosen', 'dosen.id = matakuliah.dosen_id', 'left')
            ->where('krs.mahasiswa_id', $mahasiswaId);

        if ($tahunAjaran !== null) {
            $this->where('krs.tahun_ajaran', $tahunAjaran);
        }

        return $this->orderBy('krs.tahun_ajaran')->orderBy('matakuliah.kode')->findAll();
    }

    /**
     * Mengambil matakuliah untuk semester berjalan.
     * Mengembalikan pesan error, atau null jika berhasil.
     */
    public function ambil(int $mahasiswaId, array $matakuliah): ?string
    {
        $krs = $this->forMahasiswa($mahasiswaId);

        if (in_array($matakuliah['id'], array_column($krs, 'matakuliah_id'))) {
            return 'Matakuliah sudah ada di KRS.';
        }

        $total = array_sum(array_column($krs, 'sks')) + $matakuliah['sks'];
        if ($total > self::MAX_SKS) {
            return 'Total SKS melebihi batas ' . self::MAX_SKS . ' SKS.';
        }

        $this->insert([
            'mahasiswa_id'  => $mahasiswaId,
            'matakuliah_id' => $matakuliah['id'],
            'tahun_ajaran'  => self::TAHUN_AJARAN,
        ]);

        return null;
    }

    /** Peserta sebuah matakuliah pada semester berjalan. */
    public function peserta(int $matakuliahId): array
    {
        return $this->select('krs.id, krs.nilai, mahasiswa.nrp, mahasiswa.nama')
            ->join('mahasiswa', 'mahasiswa.id = krs.mahasiswa_id')
            ->where(['krs.matakuliah_id' => $matakuliahId, 'krs.tahun_ajaran' => self::TAHUN_AJARAN])
            ->orderBy('mahasiswa.nrp')
            ->findAll();
    }
}
