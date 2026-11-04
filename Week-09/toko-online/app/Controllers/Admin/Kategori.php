<?php

namespace App\Controllers\Admin;

use App\Models\KategoriModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Kategori extends AdminController
{
    protected string $uri = 'kategori';

    public function __construct()
    {
        $this->model = model(KategoriModel::class);
    }

    public function index(): string
    {
        $q = $this->q();

        return view('admin/kategori/index', [
            'title' => 'Data Kategori',
            'rows'  => $this->model->withJumlahProduk($q)->paginate(10),
            'pager' => $this->model->pager,
            'q'     => $q,
        ]);
    }

    public function new(): string
    {
        return view('admin/kategori/form', ['title' => 'Tambah Kategori', 'row' => null]);
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->model->insert($data)) {
            return $this->back();
        }

        return $this->toIndex('success', "Kategori {$data['nama']} ditambahkan.");
    }

    public function edit(int $id): string
    {
        return view('admin/kategori/form', ['title' => 'Edit Kategori', 'row' => $this->findOr404($id)]);
    }

    public function update(int $id)
    {
        $this->findOr404($id);
        if (! $this->model->update($id, $this->input() + ['id' => $id])) {
            return $this->back();
        }

        return $this->toIndex('success', 'Kategori diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($id);

        try {
            $this->model->delete($id);
        } catch (DatabaseException) {
            // FK produk -> kategori bersifat RESTRICT
            return $this->toIndex('error', "Kategori {$row['nama']} tidak dapat dihapus karena masih memiliki produk.");
        }

        return $this->toIndex('success', "Kategori {$row['nama']} dihapus.");
    }

    private function input(): array
    {
        return [
            'nama' => trim((string) $this->request->getPost('nama')),
            'ikon' => trim((string) $this->request->getPost('ikon')) ?: 'fa-box',
        ];
    }
}
