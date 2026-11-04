<?php

namespace App\Controllers;

use App\Models\KrsModel;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Krs extends BaseController
{
    private KrsModel $krs;
    private array $mahasiswa;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->krs       = model(KrsModel::class);
        $this->mahasiswa = model(MahasiswaModel::class)->findByUser(user('id'));
    }

    public function index(): string
    {
        $krs   = $this->krs->forMahasiswa($this->mahasiswa['id']);
        $ambil = array_column($krs, 'matakuliah_id');

        return view('krs/index', [
            'title'    => 'Kartu Rencana Studi',
            'mhs'      => $this->mahasiswa,
            'krs'      => $krs,
            'totalSks' => array_sum(array_column($krs, 'sks')),
            'tersedia' => array_filter(
                model(MatakuliahModel::class)->search()->findAll(),
                static fn ($mk) => ! in_array($mk['id'], $ambil),
            ),
        ]);
    }

    public function ambil()
    {
        $mk = model(MatakuliahModel::class)->find((int) $this->request->getPost('matakuliah_id'));
        if ($mk === null) {
            return redirect()->to('/krs')->with('error', 'Matakuliah tidak ditemukan.');
        }

        $error = $this->krs->ambil($this->mahasiswa['id'], $mk);

        return $error
            ? redirect()->to('/krs')->with('error', $error)
            : redirect()->to('/krs')->with('success', "{$mk['nama']} ditambahkan ke KRS.");
    }

    public function drop(int $id)
    {
        // Syarat kepemilikan, semester berjalan, dan belum dinilai ditulis langsung di DELETE,
        // jadi mengganti ID di URL tidak bisa membatalkan KRS mahasiswa lain.
        $this->krs->where([
            'id'           => $id,
            'mahasiswa_id' => $this->mahasiswa['id'],
            'tahun_ajaran' => KrsModel::TAHUN_AJARAN,
        ])->where('nilai', null)->delete();

        return db_connect()->affectedRows() > 0
            ? redirect()->to('/krs')->with('success', 'Matakuliah dibatalkan dari KRS.')
            : redirect()->to('/krs')->with('error', 'KRS tidak ditemukan atau sudah dinilai.');
    }

    public function khs(): string
    {
        $semester = [];
        foreach ($this->krs->forMahasiswa($this->mahasiswa['id'], null) as $row) {
            $semester[$row['tahun_ajaran']][] = $row;
        }

        $semua = array_merge(...array_values($semester ?: [[]]));

        return view('krs/khs', [
            'title'    => 'Kartu Hasil Studi',
            'mhs'      => $this->mahasiswa,
            'semester' => $semester,
            'ipk'      => hitung_ip($semua),
        ]);
    }
}
