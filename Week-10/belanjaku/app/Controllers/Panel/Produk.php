<?php

namespace App\Controllers\Panel;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\Files\UploadedFile;

class Produk extends PanelController
{
    private const GAMBAR_DIR  = FCPATH . 'uploads/produk/';
    private const GAMBAR_RULE = 'is_image[gambar]|mime_in[gambar,image/jpeg,image/png,image/webp]|max_size[gambar,2048]';

    protected string $routeDaftar = 'panel.produk';

    public function __construct()
    {
        $this->model = model(ProdukModel::class);
    }

    public function index(): string
    {
        $filter = [
            'q'        => trim((string) $this->request->getGet('q')),
            'kategori' => (int) $this->request->getGet('kategori'),
            'aktif'    => match ($this->request->getGet('status')) { 'aktif' => true, 'nonaktif' => false, default => null },
            'urut'     => 'nama',
        ];

        return view('panel/produk/index', [
            'title'    => 'Produk',
            'rows'     => $this->model->cari($filter)->paginate(10),
            'pager'    => $this->model->pager,
            'filter'   => $filter + ['status' => (string) $this->request->getGet('status')],
            'kategori' => model(KategoriModel::class)->pilihan(),
        ]);
    }

    public function baru(): string
    {
        return $this->form('Tambah Produk', null);
    }

    public function simpan()
    {
        $data = $this->input();
        $file = $this->gambarBaru();

        // Operator | supaya validasi data dan gambar sama-sama jalan, semua error tampil sekaligus
        if (! $this->model->validate($data) | ! $this->gambarValid($file)) {
            return $this->kembaliKeForm();
        }

        if ($file !== null) {
            $data['gambar'] = $this->simpanGambar($file);
        }
        $this->model->insert($data);

        return $this->keDaftar('success', "Produk {$data['nama']} ditambahkan.");
    }

    public function ubah(int $id): string
    {
        return $this->form('Ubah Produk', $this->cariAtau404($id));
    }

    public function perbarui(int $id)
    {
        $row  = $this->cariAtau404($id);
        $data = $this->input();
        $file = $this->gambarBaru();

        if (! $this->model->validate($data) | ! $this->gambarValid($file)) {
            return $this->kembaliKeForm();
        }

        if ($file !== null) {
            $data['gambar'] = $this->simpanGambar($file);
        }
        $this->model->update($id, $data);

        // Gambar lama dihapus setelah data baru tersimpan
        if ($file !== null) {
            $this->hapusGambar($row['gambar']);
        }

        return $this->keDaftar('success', "Produk {$data['nama']} diperbarui.");
    }

    /** Produk yang pernah dipesan tidak dihapus (FK RESTRICT), tetapi dinonaktifkan supaya hilang dari katalog. */
    public function hapus(int $id)
    {
        $row = $this->cariAtau404($id);

        try {
            $this->model->delete($id);
        } catch (DatabaseException) {
            $this->model->update($id, ['aktif' => 0]);

            return $this->keDaftar('warning', "Produk {$row['nama']} sudah pernah dipesan, jadi tidak dihapus melainkan dinonaktifkan.");
        }

        $this->hapusGambar($row['gambar']);

        return $this->keDaftar('success', "Produk {$row['nama']} dihapus.");
    }

    private function form(string $title, ?array $row): string
    {
        return view('panel/produk/form', [
            'title'    => $title,
            'row'      => $row,
            'kategori' => model(KategoriModel::class)->pilihan(),
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
            'berat'       => (int) $this->request->getPost('berat'),
            'aktif'       => $this->request->getPost('aktif') === '1' ? 1 : 0,
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
    private function simpanGambar(UploadedFile $file): string
    {
        $nama = $file->getRandomName();
        $file->move(self::GAMBAR_DIR, $nama);

        return $nama;
    }

    private function hapusGambar(?string $gambar): void
    {
        if ($gambar && is_file(self::GAMBAR_DIR . $gambar)) {
            unlink(self::GAMBAR_DIR . $gambar);
        }
    }
}
