<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attempt()
    {
        $rules = ['username' => 'required', 'password' => 'required'];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $user = model(UserModel::class)->attempt(
            (string) $this->request->getPost('username'),
            (string) $this->request->getPost('password'),
        );

        if ($user === null) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        // ID session diganti untuk mencegah session fixation
        session()->regenerate(true);
        session()->set([
            'logged_in' => true,
            'role'      => $user['role'],
            'user'      => ['id' => $user['id'], 'username' => $user['username'], 'nama' => $user['nama'], 'role' => $user['role']],
        ]);

        return redirect()->to('/dashboard')->with('success', 'Selamat datang, ' . $user['nama'] . '.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda sudah logout.');
    }
}
