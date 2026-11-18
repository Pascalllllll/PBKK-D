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
        // Email dirapikan dulu, jadi " Budi@Mail.Test " tetap dikenali
        $input = [
            'email'    => strtolower(trim((string) $this->request->getPost('email'))),
            'password' => (string) $this->request->getPost('password'),
        ];
        $rules = [
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Kata sandi', 'rules' => 'required'],
        ];
        if (! $this->validateData($input, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $auth  = service('autentikasi');
        $gagal = $auth->coba($input['email'], $input['password'], $this->request->getPost('ingat') === '1');

        if ($gagal !== null) {
            return redirect()->back()->withInput()->with('error', $gagal);
        }

        $user = $auth->user();

        // Kembali ke halaman yang tadi diminta, asalkan role-nya memang boleh membuka halaman itu
        $tujuan = session()->getTempdata('tujuan');
        session()->removeTempdata('tujuan');
        $boleh = $tujuan && ($tujuan['role'] === [] || in_array($user['role'], $tujuan['role'], true));

        return redirect()->to($boleh ? $tujuan['url'] : halaman_awal($user['role']))->with('success', 'Halo, ' . $user['nama'] . '!');
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
        service('autentikasi')->mulai($users->find($id));

        return redirect()->to(url_to('katalog'))->with('success', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    public function keluar()
    {
        service('autentikasi')->keluar();

        return redirect()->to(url_to('masuk'))->with('success', 'Anda sudah keluar. Sampai jumpa lagi!');
    }
}
