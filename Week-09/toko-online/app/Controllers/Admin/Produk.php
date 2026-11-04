<?php

namespace App\Controllers\Admin;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\Files\UploadedFile;

class Produk extends AdminController
{
    private const GAMBAR_DIR  = FCPATH . 'uploads/produk/';
    private const GAMBAR_RULE = 'is_image[gambar]|mime_in[gambar,image/jpeg,image/png,image/webp]|max_size[gambar,2048]';

    protected string $uri = 'produk';

    public function __construct()
    {
        $this->model = model(ProdukModel::class);
    }

    public function index(): string
    {
        $q        = $this->q();
        $kategori = (int) $this->request->getGet('kategori');

        return view('admin/produk/index', [
            'title'      => 'Data Produk',
            'rows'       => $this->model->daftar($q, $kategori)->paginate(10),
            'pager'      => $this->model->pager,
            'q'          => $q,
            'kategori'   => model(KategoriModel::class)->options(),
            'kategoriId' => $kategori,
        ]);
    }

    public function new(): string
    {
        return $this->form('Tambah Produk', null);
    }

    public function create()
    {
        $data = $this->input();
        $file = $this->gambarBaru();

        // Gambar opsional; jika ada, divalidasi. Operator | supaya semua error tampil sekaligus.
        if (! $this->model->validate($data) | ! $this->gambarValid($file)) {
            return $this->back();
        }

        if ($file !== null) {
            $data['gambar'] = $this->storeGambar($file);
        }
        $this->model->insert($data);

        return $this->toIndex('success', "Produk {$data['nama']} ditambahkan.");
    }

    public function edit(int $id): string
    {
        return $this->form('Edit Produk', $this->findOr404($id));
    }

    public function update(int $id)
    {
        $row  = $this->findOr404($id);
        $data = $this->input();
        $file = $this->gambarBaru();

        if (! $this->model->validate($data) | ! $this->gambarValid($file)) {
            return $this->back();
        }

        if ($file !== null) {
            $data['gambar'] = $this->storeGambar($file);
        }
        $this->model->update($id, $data);

        // Gambar lama dihapus setelah data baru tersimpan
        if ($file !== null) {
            $this->hapusFile($row['gambar']);
        }

        return $this->toIndex('success', 'Produk diperbarui.');
    }

    public function delete(int $id)
    {
        $row = $this->findOr404($id);

        try {
            $this->model->delete($id);
        } catch (DatabaseException) {
            // FK order_items -> produk bersifat RESTRICT
            return $this->toIndex('error', "Produk {$row['nama']} tidak dapat dihapus karena sudah pernah dipesan.");
        }

        $this->hapusFile($row['gambar']);

        return $this->toIndex('success', "Produk {$row['nama']} dihapus.");
    }

    private function form(string $title, ?array $row): string
    {
        return view('admin/produk/form', [
            'title'    => $title,
            'row'      => $row,
            'kategori' => model(KategoriModel::class)->options(),
        ]);
    }

    private function input(): array
    {
        return [
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
            'nama'        => trim((string) $this->request->getPost('nama')),
            'deskripsi'   => trim((string) $this->request->getPost('deskripsi')),
            'harga'       => (int) $this->request->getPost('harga'),
            'stok'        => (int) $this->request->getPost('stok'),
        ];
    }

    private function gambarBaru(): ?UploadedFile
    {
        $file = $this->request->getFile('gambar');

        return $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE ? $file : null;
    }

    private function gambarValid(?UploadedFile $file): bool
    {
        return $file === null || $this->validate(['gambar' => self::GAMBAR_RULE], ['gambar' => [
            'is_image' => 'File harus berupa gambar.',
            'mime_in'  => 'Format gambar harus JPG, PNG, atau WEBP.',
            'max_size' => 'Ukuran gambar maksimal 2 MB.',
        ]]);
    }

    /** Disimpan dengan nama acak supaya nama file dari user tidak dipakai. */
    private function storeGambar(UploadedFile $file): string
    {
        $name = $file->getRandomName();
        $file->move(self::GAMBAR_DIR, $name);

        return $name;
    }

    private function hapusFile(?string $gambar): void
    {
        if ($gambar && is_file(self::GAMBAR_DIR . $gambar)) {
            unlink(self::GAMBAR_DIR . $gambar);
        }
    }
}
