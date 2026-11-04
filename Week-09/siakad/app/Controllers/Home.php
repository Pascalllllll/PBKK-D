<?php

namespace App\Controllers;

use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home', [
            'jumlah' => [
                'mahasiswa'  => model(MahasiswaModel::class)->countAllResults(),
                'dosen'      => model(DosenModel::class)->countAllResults(),
                'matakuliah' => model(MatakuliahModel::class)->countAllResults(),
            ],
        ]);
    }
}
