<?php

namespace App\Controllers;

use App\Models\AlamatModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/** Buku alamat pelanggan: tambah, ubah, hapus, dan pilih alamat utama. */
class Alamat extends BaseController
{
    private AlamatModel $model;

    public function __construct()
    {
        $this->model = model(AlamatModel::class);
    }

    public function index(): string
    {
        return view('akun/alamat', [
            'title'  => 'Buku Alamat',
            'alamat' => $this->model->milik(user('id')),
        ]);
    }

    public function simpan()
    {
        if (! $this->model->tambah(user('id'), $this->input())) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors())->with('form_alamat', true);
        }

        return redirect()->to(url_to('akun.alamat'))->with('success', 'Alamat ditambahkan.');
    }

    public function ubah(int $id): string
    {
        return view('akun/alamat_ubah', ['title' => 'Ubah Alamat', 'a' => $this->milik($id)]);
    }

    public function perbarui(int $id)
    {
        $this->milik($id);
        if (! $this->model->update($id, $this->input())) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to(url_to('akun.alamat'))->with('success', 'Alamat diperbarui.');
    }

    public function utama(int $id)
    {
        $a = $this->milik($id);
        $this->model->jadikanUtama($id, user('id'));

        return redirect()->to(url_to('akun.alamat'))->with('success', "Alamat \"{$a['label']}\" sekarang menjadi alamat utama.");
    }

    public function hapus(int $id)
    {
        $a = $this->milik($id);
        $this->model->hapusMilik($a);

        return redirect()->to(url_to('akun.alamat'))->with('success', "Alamat \"{$a['label']}\" dihapus.");
    }

    /** Alamat orang lain dianggap tidak ada (404), bukan 403, supaya ID alamat tidak bisa ditebak. */
    private function milik(int $id): array
    {
        return $this->model->milikUser($id, user('id')) ?? throw PageNotFoundException::forPageNotFound();
    }

    private function input(): array
    {
        return array_map('trim', (array) $this->request->getPost(['label', 'penerima', 'telepon', 'alamat', 'kota', 'kode_pos']));
    }
}
