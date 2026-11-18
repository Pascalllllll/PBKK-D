<?php

namespace App\Controllers\Panel;

use App\Models\LogMasukModel;
use App\Models\UserModel;

/** Kelola akun (khusus admin): cari, bekukan / aktifkan, ubah role, keluarkan dari semua perangkat. */
class Pengguna extends PanelController
{
    protected string $routeDaftar = 'panel.pengguna';

    public function __construct()
    {
        $this->model = model(UserModel::class);
    }

    public function index(): string
    {
        $filter = [
            'q'      => trim((string) $this->request->getGet('q')),
            'role'   => (string) $this->request->getGet('role'),
            'status' => (string) $this->request->getGet('status'),
        ];

        return view('panel/pengguna/index', [
            'title'      => 'Pengguna',
            'rows'       => $this->model->daftar($filter)->paginate(12),
            'pager'      => $this->model->pager,
            'filter'     => $filter,
            'ringkasan'  => $this->model->ringkasan(),
        ]);
    }

    public function status(int $id)
    {
        if ($id === user('id')) {
            return $this->kembali('error', 'Akun Anda sendiri tidak bisa dibekukan atau diubah role-nya, supaya admin tidak terkunci dari panel.');
        }
        $akun = $this->cariAtau404($id);

        $aktif = $akun['aktif'] ? 0 : 1;
        $this->model->update($id, ['aktif' => $aktif]);
        if (! $aktif) {
            // Session yang sedang berjalan ikut berakhir pada request berikutnya
            $this->model->cabutSemuaSesi($id);
        }

        return $this->kembali('success', $akun['nama'] . ($aktif ? ' diaktifkan kembali.' : ' dibekukan dan dikeluarkan dari semua perangkat.'));
    }

    public function role(int $id)
    {
        if ($id === user('id')) {
            return $this->kembali('error', 'Akun Anda sendiri tidak bisa dibekukan atau diubah role-nya, supaya admin tidak terkunci dari panel.');
        }
        $akun = $this->cariAtau404($id);

        $role = (string) $this->request->getPost('role');
        if (! $this->model->update($id, ['role' => $role])) {
            return $this->kembali('error', implode(' ', $this->model->errors()));
        }

        return $this->kembali('success', "Role {$akun['nama']} sekarang " . label_role($role) . '.');
    }

    public function cabut(int $id)
    {
        $akun = $this->cariAtau404($id);
        $this->model->cabutSemuaSesi($id);

        return $this->kembali('success', "{$akun['nama']} dikeluarkan dari semua perangkat.");
    }

    public function log(): string
    {
        $hasil = (string) $this->request->getGet('hasil');
        $log   = model(LogMasukModel::class);

        return view('panel/pengguna/log', [
            'title' => 'Riwayat Masuk',
            'rows'  => $log->terbaru(null, $hasil)->paginate(20),
            'pager' => $log->pager,
            'hasil' => $hasil,
        ]);
    }

    /** Kembali ke daftar dengan filter yang sama. */
    private function kembali(string $jenis, string $pesan)
    {
        return redirect()->to(previous_url() ?: url_to($this->routeDaftar))->with($jenis, $pesan);
    }
}
