<?php

namespace App\Controllers;

use App\Libraries\AlurPesanan;
use App\Models\PembayaranModel;
use App\Models\PengirimanModel;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Pesanan extends BaseController
{
    // Bukti transfer disimpan di writable/, bukan public/, dan hanya bisa dibuka lewat route bukti (cek pemilik)
    private const BUKTI_DIR = WRITEPATH . 'uploads/bukti/';

    public function index(): string
    {
        $status = (string) $this->request->getGet('status');
        $model  = model(PesananModel::class);

        return view('pesanan/index', [
            'title'  => 'Pesanan Saya',
            'rows'   => $model->daftar(['user' => user('id'), 'status' => $status])->paginate(10),
            'pager'  => $model->pager,
            'status' => $status,
        ]);
    }

    public function detail(string $kode): string
    {
        $p = $this->milikSendiri($kode);

        return view('pesanan/detail', [
            'title'      => 'Pesanan ' . $p['kode'],
            'pesanan'    => $p,
            'items'      => model(PesananItemModel::class)->untuk($p['id']),
            'pembayaran' => model(PembayaranModel::class)->untuk($p['id']),
            'pengiriman' => model(PengirimanModel::class)->untuk($p['id']),
        ]);
    }

    public function bayar(string $kode)
    {
        $p = $this->milikSendiri($kode);

        $rules = [
            'bank_pengirim' => ['label' => 'Bank pengirim', 'rules' => 'required|max_length[40]'],
            'nama_rekening' => ['label' => 'Nama pemilik rekening', 'rules' => 'required|min_length[3]|max_length[100]'],
            'bukti'         => [
                'label' => 'Bukti transfer',
                'rules' => 'uploaded[bukti]|is_image[bukti]|mime_in[bukti,image/jpeg,image/png,image/webp]|max_size[bukti,2048]',
            ],
        ];
        $pesan = ['bukti' => [
            'uploaded' => 'Pilih file bukti transfer.',
            'is_image' => 'Bukti transfer harus berupa gambar.',
            'mime_in'  => 'Format bukti harus JPG, PNG, atau WEBP.',
            'max_size' => 'Ukuran bukti maksimal 2 MB.',
        ]];

        if (! $this->validate($rules, $pesan)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lama = model(PembayaranModel::class)->where('pesanan_id', $p['id'])->first()['bukti'] ?? null;
        $file = $this->request->getFile('bukti');
        $nama = $file->getRandomName();
        $file->move(self::BUKTI_DIR, $nama);

        try {
            (new AlurPesanan())->unggahBukti($p['id'], [
                'bank_pengirim' => $this->request->getPost('bank_pengirim'),
                'nama_rekening' => trim((string) $this->request->getPost('nama_rekening')),
                'bukti'         => $nama,
            ]);
        } catch (RuntimeException $e) {
            unlink(self::BUKTI_DIR . $nama);

            return redirect()->back()->with('error', $e->getMessage());
        }

        // Bukti lama (yang pernah ditolak) dihapus setelah bukti baru tersimpan
        if ($lama && is_file(self::BUKTI_DIR . $lama)) {
            unlink(self::BUKTI_DIR . $lama);
        }

        return redirect()->to(url_to('pesanan.detail', $kode))->with('success', 'Bukti pembayaran terkirim. Admin akan memeriksanya.');
    }

    public function batal(string $kode)
    {
        $p = $this->milikSendiri($kode);

        // Pelanggan hanya boleh membatalkan sebelum membayar
        if ($p['status'] !== 'menunggu_bayar') {
            return redirect()->back()->with('error', 'Pesanan hanya bisa dibatalkan selama belum dibayar.');
        }

        (new AlurPesanan())->batal($p['id']);

        return redirect()->to(url_to('pesanan.detail', $kode))->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }

    public function diterima(string $kode)
    {
        $p = $this->milikSendiri($kode);

        try {
            (new AlurPesanan())->selesai($p['id']);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('pesanan.detail', $kode))->with('success', 'Terima kasih! Pesanan ditandai selesai.');
    }

    /** Pesanan orang lain dianggap tidak ada (404), jadi kode pesanan orang lain tidak bisa ditebak. */
    private function milikSendiri(string $kode): array
    {
        return model(PesananModel::class)->milik($kode, user('id')) ?? throw PageNotFoundException::forPageNotFound();
    }
}
