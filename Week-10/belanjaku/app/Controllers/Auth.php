<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function masuk(): string
    {
        return view('auth/masuk', ['title' => 'Masuk']);
    }

    public function prosesMasuk()
    {
        $rules = [
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Kata sandi', 'rules' => 'required'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = model(UserModel::class)->cocokkan(
            (string) $this->request->getPost('email'),
            (string) $this->request->getPost('password'),
        );

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
        }

        $this->mulaiSesi($user);

        // Pelanggan kembali ke halaman yang tadi diminta (misalnya keranjang)
        $tujuan = $user['role'] === 'customer' ? session()->getTempdata('tujuan') : null;
        session()->removeTempdata('tujuan');

        return redirect()->to($tujuan ?? halaman_awal($user['role']))->with('success', 'Halo, ' . $user['nama'] . '!');
    }

    public function daftar(): string
    {
        return view('auth/daftar', ['title' => 'Daftar Akun']);
    }

    public function prosesDaftar()
    {
        $data = [
            'nama'     => trim((string) $this->request->getPost('nama')),
            'email'    => strtolower(trim((string) $this->request->getPost('email'))),
            'telepon'  => trim((string) $this->request->getPost('telepon')) ?: null,
            'password' => (string) $this->request->getPost('password'),
            'role'     => 'customer', // role tidak pernah diambil dari input
        ];
        $users = model(UserModel::class);

        // Keduanya selalu dijalankan supaya semua pesan error tampil sekaligus
        $dataValid       = $users->validate($data);
        $konfirmasiValid = $this->validate([
            'konfirmasi' => ['label' => 'Ulangi kata sandi', 'rules' => 'required|matches[password]'],
            'setuju'     => ['label' => 'Persetujuan', 'rules' => 'required'],
        ], [
            'konfirmasi' => ['matches' => 'Ulangi kata sandi tidak sama.'],
            'setuju'     => ['required' => 'Centang persetujuan syarat dan ketentuan.'],
        ]);

        if (! $dataValid || ! $konfirmasiValid) {
            return redirect()->back()->withInput()->with('errors', $users->errors() + $this->validator->getErrors());
        }

        $id = $users->insert($data);
        $this->mulaiSesi($users->find($id));

        return redirect()->to(url_to('katalog'))->with('success', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    public function keluar()
    {
        session()->destroy();

        return redirect()->to(url_to('beranda'));
    }

    private function mulaiSesi(array $user): void
    {
        // ID session diganti untuk mencegah session fixation
        session()->regenerate(true);
        session()->set([
            'logged_in' => true,
            'role'      => $user['role'],
            'user'      => ['id' => (int) $user['id'], 'nama' => $user['nama'], 'email' => $user['email'], 'role' => $user['role']],
        ]);
    }
}
