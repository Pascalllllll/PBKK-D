<?php

namespace App\Controllers;

use App\Models\LogMasukModel;
use App\Models\TokenIngatModel;
use App\Models\UserModel;

/** Halaman akun untuk semua role: profil, ganti sandi, perangkat yang diingat, riwayat masuk. */
class Akun extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = model(UserModel::class);
    }

    public function index(): string
    {
        $id = (int) user('id');

        return view('akun/index', [
            'title'       => 'Akun Saya',
            'akun'        => $this->users->find($id),
            'perangkat'   => model(TokenIngatModel::class)->milik($id),
            'selectorIni' => service('autentikasi')->selectorIni(),
            'riwayat'     => model(LogMasukModel::class)->terbaru($id)->findAll(8),
        ]);
    }

    public function profil()
    {
        $akun = $this->users->find(user('id'));
        $data = [
            'id'      => $akun['id'],
            'nama'    => trim((string) $this->request->getPost('nama')),
            'email'   => strtolower(trim((string) $this->request->getPost('email'))),
            'telepon' => trim((string) $this->request->getPost('telepon')) ?: null,
        ];

        // Email dipakai untuk login, jadi menggantinya harus dikonfirmasi dengan sandi
        if ($data['email'] !== $akun['email'] && ! $this->users->cocokSandi($akun['id'], (string) $this->request->getPost('sandi_konfirmasi'))) {
            return $this->gagal(['sandi_konfirmasi' => 'Masukkan kata sandi yang benar untuk mengganti email.']);
        }

        if (! $this->users->update($akun['id'], $data)) {
            return $this->gagal($this->users->errors());
        }

        return redirect()->to(url_to('akun'))->with('success', 'Profil diperbarui.');
    }

    public function sandi()
    {
        $rules = [
            'sandi_lama'   => ['label' => 'Kata sandi lama', 'rules' => 'required'],
            'sandi_baru'   => ['label' => 'Kata sandi baru', 'rules' => UserModel::ATURAN_SANDI . '|differs[sandi_lama]'],
            'ulangi_sandi' => ['label' => 'Ulangi kata sandi baru', 'rules' => 'required|matches[sandi_baru]'],
        ];
        $pesan = [
            'sandi_baru'   => UserModel::PESAN_SANDI + ['differs' => 'Kata sandi baru harus berbeda dari yang lama.'],
            'ulangi_sandi' => ['matches' => 'Ulangi kata sandi baru tidak sama.'],
        ];
        if (! $this->validate($rules, $pesan)) {
            return $this->gagal($this->validator->getErrors(), '#sandi');
        }

        $id = (int) user('id');
        if (! $this->users->cocokSandi($id, (string) $this->request->getPost('sandi_lama'))) {
            return $this->gagal(['sandi_lama' => 'Kata sandi lama salah.'], '#sandi');
        }

        $this->users->update($id, ['password' => $this->request->getPost('sandi_baru')]);
        service('autentikasi')->cabutPerangkatLain();

        return redirect()->to(url_to('akun'))->with('success', 'Kata sandi diganti. Perangkat lain yang masih login sudah dikeluarkan.');
    }

    public function lupakanPerangkat(int $tokenId)
    {
        model(TokenIngatModel::class)->where(['id' => $tokenId, 'user_id' => user('id')])->delete();

        return redirect()->to(url_to('akun') . '#perangkat')->with('success', 'Perangkat itu tidak diingat lagi.');
    }

    public function keluarSemua()
    {
        service('autentikasi')->cabutPerangkatLain();

        return redirect()->to(url_to('akun') . '#perangkat')->with('success', 'Semua perangkat lain sudah dikeluarkan.');
    }

    private function gagal(array $errors, string $anchor = '#profil')
    {
        return redirect()->to(url_to('akun') . $anchor)->withInput()->with('errors', $errors);
    }
}
