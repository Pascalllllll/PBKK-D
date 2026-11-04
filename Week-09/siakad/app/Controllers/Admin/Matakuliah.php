<?php

namespace App\Controllers\Admin;

use App\Models\DosenModel;
use App\Models\MatakuliahModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Matakuliah extends AdminController
{
    protected string $uri = 'matakuliah';

    public function __construct()
    {
        $this->model = model(MatakuliahModel::class);
    }

    public function index(): string
    {
        $q = $this->q();

        return view('admin/matakuliah/index', [
            'title' => 'Data Matakuliah',
            'rows'  => $this->model->search($q)->paginate(10),
            'pager' => $this->model->pager,
            'q'     => $q,
        ]);
    }

    public function new(): string
    {
        return $this->form('Tambah Matakuliah', null);
    }

    public function create()
    {
        $data = $this->input();
        if (! $this->model->insert($data)) {
            return $this->back();
        }

        return $this->toIndex('success', "Matakuliah {$data['nama']} ditambahkan.");
    }

    public function edit(int $id): string
    {
        return $this->form('Edit Matakuliah', $this->findOr404($id));
    }

    public function update(int $id)
    {
        $this->findOr404($id);
        $data = $this->input();
        if (! $this->model->update($id, $data + ['id' => $id])) {
            return $this->back();
        }

        return $this->toIndex('success', 'Data matakuliah diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($id);

        try {
            $this->model->delete($id);
        } catch (DatabaseException) {
            // FK krs -> matakuliah bersifat RESTRICT
            return $this->toIndex('error', "Matakuliah {$row['nama']} tidak dapat dihapus karena sudah diambil mahasiswa di KRS.");
        }

        return $this->toIndex('success', "Matakuliah {$row['nama']} dihapus.");
    }

    private function form(string $title, ?array $row): string
    {
        return view('admin/matakuliah/form', [
            'title' => $title,
            'row'   => $row,
            'dosen' => model(DosenModel::class)->options(),
        ]);
    }

    private function input(): array
    {
        return [
            'kode'     => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'sks'      => (int) $this->request->getPost('sks'),
            'semester' => (int) $this->request->getPost('semester'),
            'dosen_id' => $this->request->getPost('dosen_id') ?: null,
        ];
    }
}
