<?php

namespace App\Controllers\Panel;

use App\Models\KategoriModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Kategori extends PanelController
{
    protected string $routeDaftar = 'panel.kategori';

    public function __construct()
    {
        $this->model = model(KategoriModel::class);
    }

    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));

        return view('panel/kategori/index', [
            'title' => 'Kategori',
            'rows'  => $this->model->denganJumlahProduk($q)->paginate(10),
            'pager' => $this->model->pager,
            'q'     => $q,
        ]);
    }

    public function baru(): string
    {
        return view('panel/kategori/form', ['title' => 'Tambah Kategori', 'row' => null]);
    }

    public function simpan()
    {
        $data = $this->input();
        if (! $this->model->insert($data)) {
            return $this->kembaliKeForm();
        }

        return $this->keDaftar('success', "Kategori {$data['nama']} ditambahkan.");
    }

    public function ubah(int $id): string
    {
        return view('panel/kategori/form', ['title' => 'Ubah Kategori', 'row' => $this->cariAtau404($id)]);
    }

    public function perbarui(int $id)
    {
        $this->cariAtau404($id);
        if (! $this->model->update($id, $this->input() + ['id' => $id])) {
            return $this->kembaliKeForm();
        }

        return $this->keDaftar('success', 'Kategori diperbarui.');
    }

    public function hapus(int $id)
    {
        $row = $this->cariAtau404($id);

        try {
            $this->model->delete($id);
        } catch (DatabaseException) {
            // FK produk -> kategori bersifat RESTRICT
            return $this->keDaftar('error', "Kategori {$row['nama']} tidak dapat dihapus karena masih memiliki produk.");
        }

        return $this->keDaftar('success', "Kategori {$row['nama']} dihapus.");
    }

    private function input(): array
    {
        return [
            'nama'      => trim((string) $this->request->getPost('nama')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')) ?: null,
        ];
    }
}
