<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Libraries\AlurPesanan;
use App\Models\PembayaranModel;
use App\Models\PengirimanModel;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Pesanan extends BaseController
{
    public function index(): string
    {
        $filter = [
            'status' => (string) $this->request->getGet('status'),
            'q'      => trim((string) $this->request->getGet('q')),
        ];
        $model = model(PesananModel::class);

        return view('panel/pesanan/index', [
            'title'     => 'Pesanan',
            'rows'      => $model->daftar($filter)->paginate(10),
            'pager'     => $model->pager,
            'filter'    => $filter,
            'perStatus' => model(PesananModel::class)->jumlahPerStatus(),
        ]);
    }

    public function detail(int $id): string
    {
        $p = model(PesananModel::class)->daftar()->where('pesanan.id', $id)->first()
            ?? throw PageNotFoundException::forPageNotFound();

        return view('panel/pesanan/detail', [
            'title'      => 'Pesanan ' . $p['kode'],
            'pesanan'    => $p,
            'items'      => model(PesananItemModel::class)->untuk($id),
            'pembayaran' => model(PembayaranModel::class)->untuk($id),
            'pengiriman' => model(PengirimanModel::class)->untuk($id),
        ]);
    }

    public function kirim(int $id)
    {
        $rules = model(PengirimanModel::class)->getValidationRules();
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return $this->jalankan($id, 'Nomor resi disimpan, pesanan berstatus Dikirim.', static fn (AlurPesanan $alur) => $alur->kirim(
            $id,
            user('id'),
            (string) service('request')->getPost('kurir'),
            trim((string) service('request')->getPost('no_resi')),
        ));
    }

    public function selesai(int $id)
    {
        return $this->jalankan($id, 'Pesanan ditandai selesai.', static fn (AlurPesanan $alur) => $alur->selesai($id));
    }

    public function batal(int $id)
    {
        return $this->jalankan($id, 'Pesanan dibatalkan dan stok dikembalikan.', static fn (AlurPesanan $alur) => $alur->batal($id));
    }

    private function jalankan(int $id, string $berhasil, callable $aksi)
    {
        try {
            $aksi(new AlurPesanan());
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('panel.pesanan.detail', $id))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('panel.pesanan.detail', $id))->with('success', $berhasil);
    }
}
