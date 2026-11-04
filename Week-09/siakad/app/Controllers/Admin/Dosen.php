<?php

namespace App\Controllers\Admin;

use App\Models\DosenModel;
use RuntimeException;

class Dosen extends AdminController
{
    protected string $uri = 'dosen';

    public function __construct()
    {
        $this->model = model(DosenModel::class);
    }

    public function index(): string
    {
        $q = $this->q();

        return view('admin/dosen/index', [
            'title' => 'Data Dosen',
            'rows'  => $this->model->search($q)->paginate(10),
            'pager' => $this->model->pager,
            'q'     => $q,
        ]);
    }

    public function new(): string
    {
        return view('admin/dosen/form', ['title' => 'Tambah Dosen', 'row' => null]);
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->model->validate($data)) {
            return $this->back();
        }

        try {
            $this->model->createWithAccount($data);
        } catch (RuntimeException $e) {
            return $this->back(['nidn' => $e->getMessage()]);
        }

        return $this->toIndex('success', "Dosen {$data['nama']} ditambahkan. Username dan password awal: {$data['nidn']}.");
    }

    public function edit(int $id): string
    {
        return view('admin/dosen/form', ['title' => 'Edit Dosen', 'row' => $this->findOr404($id)]);
    }

    public function update(int $id)
    {
        $row  = $this->findOr404($id);
        $data = $this->input();
        if (! $this->model->validate($data + ['id' => $id])) {
            return $this->back();
        }

        $this->model->updateWithAccount($row, $data);

        return $this->toIndex('success', 'Data dosen diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($id);
        $this->model->deleteWithAccount($row);

        // Dosen wali dan pengampu di tabel lain otomatis jadi NULL (FK SET NULL)
        return $this->toIndex('success', "Dosen {$row['nama']} dihapus.");
    }

    private function input(): array
    {
        return [
            'nidn'  => trim((string) $this->request->getPost('nidn')),
            'nama'  => trim((string) $this->request->getPost('nama')),
            'email' => trim((string) $this->request->getPost('email')) ?: null,
        ];
    }
}
