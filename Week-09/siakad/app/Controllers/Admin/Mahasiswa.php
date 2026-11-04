<?php

namespace App\Controllers\Admin;

use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

class Mahasiswa extends AdminController
{
    private const FOTO_DIR  = FCPATH . 'uploads/foto/';
    private const FOTO_RULE = 'is_image[foto]|max_size[foto,1024]|mime_in[foto,image/jpeg,image/png,image/webp]';

    protected string $uri = 'mahasiswa';

    public function __construct()
    {
        $this->model = model(MahasiswaModel::class);
    }

    public function index(): string
    {
        $q = $this->q();

        return view('admin/mahasiswa/index', [
            'title' => 'Data Mahasiswa',
            'rows'  => $this->model->search($q)->paginate(10),
            'pager' => $this->model->pager,
            'q'     => $q,
        ]);
    }

    public function new(): string
    {
        return $this->form('Tambah Mahasiswa', null);
    }

    public function create()
    {
        $data = $this->input();
        // Foto wajib saat tambah data. Operator | (bukan ||) supaya kedua validasi tetap jalan
        // dan semua pesan error tampil sekaligus.
        if (! $this->model->validate($data) | ! $this->validate(['foto' => 'uploaded[foto]|' . self::FOTO_RULE], $this->fotoMessages())) {
            return $this->back();
        }

        $data['foto'] = $this->storeFoto($this->request->getFile('foto'));

        try {
            $this->model->createWithAccount($data);
        } catch (RuntimeException $e) {
            $this->hapusFoto($data['foto']);

            return $this->back(['nrp' => $e->getMessage()]);
        }

        return $this->toIndex('success', "Mahasiswa {$data['nama']} ditambahkan. Username dan password awal: {$data['nrp']}.");
    }

    public function edit(int $id): string
    {
        return $this->form('Edit Mahasiswa', $this->findOr404($id));
    }

    public function update(int $id)
    {
        $row  = $this->findOr404($id);
        $data = $this->input();
        $file = $this->request->getFile('foto');
        $adaFoto = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;

        // Foto opsional saat edit: hanya divalidasi jika ada file baru
        $fotoValid = ! $adaFoto || $this->validate(['foto' => self::FOTO_RULE], $this->fotoMessages());
        if (! $this->model->validate($data + ['id' => $id]) | ! $fotoValid) {
            return $this->back();
        }

        if ($adaFoto) {
            $data['foto'] = $this->storeFoto($file);
        }

        $this->model->updateWithAccount($row, $data);

        if ($adaFoto) {
            $this->hapusFoto($row['foto']);
        }

        return $this->toIndex('success', 'Data mahasiswa diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($id);
        $this->model->deleteWithAccount($row);
        $this->hapusFoto($row['foto']);

        return $this->toIndex('success', "Mahasiswa {$row['nama']} dihapus.");
    }

    private function form(string $title, ?array $row): string
    {
        return view('admin/mahasiswa/form', [
            'title' => $title,
            'row'   => $row,
            'dosen' => model(DosenModel::class)->options(),
        ]);
    }

    private function input(): array
    {
        return [
            'nrp'           => trim((string) $this->request->getPost('nrp')),
            'nama'          => trim((string) $this->request->getPost('nama')),
            'prodi'         => trim((string) $this->request->getPost('prodi')),
            'angkatan'      => (int) $this->request->getPost('angkatan'),
            'dosen_wali_id' => $this->request->getPost('dosen_wali_id') ?: null,
        ];
    }

    private function fotoMessages(): array
    {
        return ['foto' => [
            'uploaded' => 'Foto wajib diunggah.',
            'is_image' => 'File harus berupa gambar.',
            'max_size' => 'Ukuran foto maksimal 1 MB.',
            'mime_in'  => 'Format foto harus JPG, PNG, atau WEBP.',
        ]];
    }

    /** Disimpan dengan nama acak supaya nama file dari user tidak dipakai. */
    private function storeFoto(UploadedFile $file): string
    {
        $name = $file->getRandomName();
        $file->move(self::FOTO_DIR, $name);

        return $name;
    }

    private function hapusFoto(?string $foto): void
    {
        if ($foto && is_file(self::FOTO_DIR . $foto)) {
            unlink(self::FOTO_DIR . $foto);
        }
    }
}
