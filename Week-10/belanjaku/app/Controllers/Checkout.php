<?php

namespace App\Controllers;

use App\Libraries\AlurPesanan;
use App\Models\AlamatModel;
use App\Models\KeranjangModel;
use RuntimeException;

class Checkout extends BaseController
{
    public function index()
    {
        $isi = model(KeranjangModel::class)->isi(user('id'));

        if ($isi['items'] === []) {
            return redirect()->to(url_to('keranjang'))->with('error', 'Keranjang masih kosong.');
        }
        if ($isi['bermasalah']) {
            return redirect()->to(url_to('keranjang'))->with('error', 'Ada produk di keranjang yang stoknya kurang atau sudah tidak dijual.');
        }

        $alamat = model(AlamatModel::class)->milik(user('id'));

        return view('checkout/index', $isi + [
            'title'   => 'Checkout',
            'alamat'  => $alamat,
            'dipilih' => (int) (old('alamat_id') ?: (session('alamat_baru') ?? ($alamat[0]['id'] ?? 0))),
            'ongkir'  => AlurPesanan::ongkir($isi['subtotal']),
        ]);
    }

    public function simpanAlamat()
    {
        $model = model(AlamatModel::class);
        $data  = array_map('trim', (array) $this->request->getPost(['label', 'penerima', 'telepon', 'alamat', 'kota', 'kode_pos']));

        if (! $model->insert($data + ['user_id' => user('id')])) {
            return redirect()->back()->withInput()->with('errors', $model->errors())->with('form_alamat', true);
        }

        return redirect()->to(url_to('checkout'))
            ->with('success', "Alamat \"{$data['label']}\" disimpan.")
            ->with('alamat_baru', $model->getInsertID());
    }

    public function proses()
    {
        $rules = [
            'alamat_id' => ['label' => 'Alamat pengiriman', 'rules' => 'required|is_natural_no_zero'],
            'catatan'   => ['label' => 'Catatan', 'rules' => 'permit_empty|max_length[255]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $pesanan = (new AlurPesanan())->checkout(
                user('id'),
                (int) $this->request->getPost('alamat_id'),
                trim((string) $this->request->getPost('catatan')),
            );
        } catch (RuntimeException $e) {
            // Tidak ada yang tersimpan: pesanan, stok, dan keranjang di-rollback
            return redirect()->to(url_to('keranjang'))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('pesanan.detail', $pesanan['kode']))
            ->with('success', 'Pesanan dibuat. Silakan transfer lalu unggah bukti pembayaran.');
    }
}
