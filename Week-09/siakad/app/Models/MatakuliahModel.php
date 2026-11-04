<?php

namespace App\Models;

use CodeIgniter\Model;

class MatakuliahModel extends Model
{
    protected $table           = 'matakuliah';
    protected $returnType      = 'array';
    protected $allowedFields   = ['kode', 'nama', 'sks', 'semester', 'dosen_id'];
    protected $useTimestamps   = true;
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'kode'     => ['label' => 'Kode', 'rules' => 'required|alpha_numeric|max_length[10]|is_unique[matakuliah.kode,id,{id}]'],
        'nama'     => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'sks'      => ['label' => 'SKS', 'rules' => 'required|integer|greater_than[0]|less_than_equal_to[6]'],
        'semester' => ['label' => 'Semester', 'rules' => 'required|integer|greater_than[0]|less_than_equal_to[8]'],
        'dosen_id' => ['label' => 'Dosen pengampu', 'rules' => 'permit_empty|is_not_unique[dosen.id]'],
    ];
    protected $validationMessages = [
        'kode' => ['is_unique' => 'Kode matakuliah sudah dipakai.'],
    ];

    public function search(string $q = ''): self
    {
        $this->select('matakuliah.*, dosen.nama AS dosen')
            ->join('dosen', 'dosen.id = matakuliah.dosen_id', 'left');

        if ($q !== '') {
            $this->groupStart()->like('matakuliah.nama', $q)->orLike('matakuliah.kode', $q)->groupEnd();
        }

        return $this->orderBy('matakuliah.semester')->orderBy('matakuliah.kode');
    }

    /** Matakuliah yang diampu seorang dosen, beserta jumlah peserta semester berjalan. */
    public function diampu(int $dosenId): array
    {
        return $this->select('matakuliah.*, COUNT(krs.id) AS peserta, SUM(krs.nilai IS NULL AND krs.id IS NOT NULL) AS belum_dinilai')
            ->join('krs', 'krs.matakuliah_id = matakuliah.id AND krs.tahun_ajaran = ' . $this->db->escape(KrsModel::TAHUN_AJARAN), 'left')
            ->where('matakuliah.dosen_id', $dosenId)
            ->groupBy('matakuliah.id')
            ->orderBy('matakuliah.kode')
            ->findAll();
    }
}
