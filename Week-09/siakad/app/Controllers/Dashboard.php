<?php

namespace App\Controllers;

use App\Models\DosenModel;
use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return match (session('role')) {
            'admin'     => $this->admin(),
            'dosen'     => $this->dosen(),
            'mahasiswa' => $this->mahasiswa(),
        };
    }

    private function admin(): string
    {
        $db = db_connect();

        return view('dashboard/admin', [
            'title'      => 'Dashboard',
            'mahasiswa'  => model(MahasiswaModel::class)->countAllResults(),
            'dosen'      => model(DosenModel::class)->countAllResults(),
            'matakuliah' => model(MatakuliahModel::class)->countAllResults(),
            'krs'        => model(KrsModel::class)->where('tahun_ajaran', KrsModel::TAHUN_AJARAN)->countAllResults(),
            'angkatan'   => $db->table('mahasiswa')->select('angkatan, COUNT(*) AS jumlah')
                ->groupBy('angkatan')->orderBy('angkatan')->get()->getResultArray(),
            'terbaru'    => model(MahasiswaModel::class)->orderBy('id', 'DESC')->findAll(5),
        ]);
    }

    private function dosen(): string
    {
        $dosen = model(DosenModel::class)->findByUser(user('id'));

        return view('dashboard/dosen', [
            'title'      => 'Dashboard',
            'dosen'      => $dosen,
            'matakuliah' => model(MatakuliahModel::class)->diampu($dosen['id']),
            'perwalian'  => model(MahasiswaModel::class)->where('dosen_wali_id', $dosen['id'])->orderBy('nrp')->findAll(),
        ]);
    }

    private function mahasiswa(): string
    {
        $mhs = model(MahasiswaModel::class)->findByUser(user('id'));
        $semua = model(KrsModel::class)->forMahasiswa($mhs['id'], null);
        $sekarang = array_filter($semua, static fn ($r) => $r['tahun_ajaran'] === KrsModel::TAHUN_AJARAN);

        return view('dashboard/mahasiswa', [
            'title'     => 'Dashboard',
            'mhs'       => $mhs,
            'krs'       => $sekarang,
            'sksKrs'    => array_sum(array_column($sekarang, 'sks')),
            'ipk'       => hitung_ip($semua),
        ]);
    }
}
