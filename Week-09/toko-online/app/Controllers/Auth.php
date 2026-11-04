<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session('logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/login', ['title' => 'Login']);
    }

    public function attempt()
    {
        if (! $this->validate(['username' => 'required', 'password' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $user = model(UserModel::class)->attempt(
            (string) $this->request->getPost('username'),
            (string) $this->request->getPost('password'),
        );

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        $this->masuk($user);

        // Pelanggan kembali ke halaman yang tadi diminta (misalnya checkout)
        $tujuan = $user['role'] === 'admin' ? '/admin' : (session()->getTempdata('tujuan') ?? '/');
        session()->removeTempdata('tujuan');

        return redirect()->to($tujuan)->with('success', 'Selamat datang, ' . $user['nama'] . '.');
    }

    public function register()
    {
        if (session('logged_in')) {
            return redirect()->to('/');
        }

        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    public function store()
    {
        $data = [
            'username' => trim((string) $this->request->getPost('username')),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'email'    => trim((string) $this->request->getPost('email')),
            'password' => (string) $this->request->getPost('password'),
            'role'     => 'customer', // role tidak pernah diambil dari input
        ];

        $users = model(UserModel::class);

        // Keduanya selalu dijalankan supaya semua pesan error tampil sekaligus
        $userValid    = $users->validate($data);
        $confirmValid = $this->validate([
            'password_confirm' => ['label' => 'Konfirmasi password', 'rules' => 'required|matches[password]'],
        ]);

        if (! $userValid || ! $confirmValid) {
            return redirect()->back()->withInput()
                ->with('errors', $users->errors() + $this->validator->getErrors());
        }

        $id = $users->insert($data);
        $this->masuk($users->find($id));

        return redirect()->to('/')->with('success', 'Akun berhasil dibuat. Selamat berbelanja!');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/')->with('success', 'Anda sudah logout.');
    }

    private function masuk(array $user): void
    {
        // Keranjang tetap dibawa setelah login; ID session diganti untuk mencegah session fixation
        session()->regenerate(true);
        session()->set([
            'logged_in' => true,
            'role'      => $user['role'],
            'user'      => ['id' => $user['id'], 'username' => $user['username'], 'nama' => $user['nama'], 'role' => $user['role']],
        ]);
    }
}
