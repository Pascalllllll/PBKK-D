<?php

namespace App\Controllers;

use App\Models\DosenModel;
use App\Models\KrsModel;
use App\Models\MatakuliahModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Nilai extends BaseController
{
    private array $dosen;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->dosen = model(DosenModel::class)->findByUser(user('id'));
    }

    public function index(): string
    {
        return view('nilai/index', [
            'title'      => 'Input Nilai',
            'matakuliah' => model(MatakuliahModel::class)->diampu($this->dosen['id']),
        ]);
    }

    public function show(int $id): string
    {
        $mk = $this->matakuliahOr404($id);

        return view('nilai/show', [
            'title'   => 'Nilai ' . $mk['nama'],
            'mk'      => $mk,
            'peserta' => model(KrsModel::class)->peserta($id),
        ]);
    }

    public function save(int $id)
    {
        $this->matakuliahOr404($id);
        $krs    = model(KrsModel::class);
        $nilai  = (array) $this->request->getPost('nilai');
        $sah    = array_map('intval', array_column($krs->peserta($id), 'id'));
        $simpan = [];

        // Validasi semua nilai dulu, baru disimpan sekaligus dalam satu transaksi
        foreach ($nilai as $krsId => $huruf) {
            $huruf = is_string($huruf) && $huruf !== '' ? $huruf : null;
            if (! in_array((int) $krsId, $sah, true)) {
                continue; // bukan peserta matakuliah ini
            }
            if ($huruf !== null && ! in_array($huruf, KrsModel::NILAI, true)) {
                return redirect()->back()->with('error', "Nilai \"{$huruf}\" tidak valid.");
            }
            $simpan[(int) $krsId] = $huruf;
        }

        $db = db_connect();
        $db->transStart();
        foreach ($simpan as $krsId => $huruf) {
            $krs->update($krsId, ['nilai' => $huruf]);
        }
        $db->transComplete();

        return redirect()->to("/nilai/{$id}")->with('success', 'Nilai berhasil disimpan.');
    }

    /** Dosen hanya boleh membuka matakuliah yang diampunya sendiri. */
    private function matakuliahOr404(int $id): array
    {
        return model(MatakuliahModel::class)->where(['id' => $id, 'dosen_id' => $this->dosen['id']])->first()
            ?? throw PageNotFoundException::forPageNotFound();
    }
}
