<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Libraries\AlurPesanan;
use App\Models\PembayaranModel;
use App\Models\PengirimanModel;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use App\Models\PesananRiwayatModel;
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
        $boleh = PesananModel::statusUntuk(user('role'));

        return view('panel/pesanan/index', [
            'title'     => user('role') === 'admin' ? 'Pesanan' : 'Antrean Pengiriman',
            'rows'      => $model->daftar($filter + ['hanya' => $boleh])->paginate(10),
            'pager'     => $model->pager,
            'filter'    => $filter,
            'status'    => $boleh,
            'perStatus' => array_intersect_key($model->jumlahPerStatus(), array_flip($boleh)),
        ]);
    }

    public function detail(int $id): string
    {
        // Pesanan di luar status yang boleh dilihat role ini dianggap tidak ada
        $p = model(PesananModel::class)->daftar(['hanya' => PesananModel::statusUntuk(user('role'))])->where('pesanan.id', $id)->first()
            ?? throw PageNotFoundException::forPageNotFound();

        return view('panel/pesanan/detail', [
            'title'      => 'Pesanan ' . $p['kode'],
            'pesanan'    => $p,
            'items'      => model(PesananItemModel::class)->untuk($id),
            'pembayaran' => model(PembayaranModel::class)->untuk($id),
            'pengiriman' => model(PengirimanModel::class)->untuk($id),
            'riwayat'    => model(PesananRiwayatModel::class)->untuk($id),
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
        return $this->jalankan($id, 'Pesanan dibatalkan dan stok dikembalikan.', static fn (AlurPesanan $alur) => $alur->batal($id, 'Dibatalkan admin, stok dikembalikan'));
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
