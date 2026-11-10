<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Libraries\AlurPesanan;
use App\Models\PembayaranModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Pembayaran extends BaseController
{
    public function index(): string
    {
        return view('panel/pembayaran/index', [
            'title'   => 'Verifikasi Pembayaran',
            'antrean' => model(PembayaranModel::class)->antrean(),
            'riwayat' => model(PembayaranModel::class)->riwayat(),
        ]);
    }

    public function terima(int $id)
    {
        return $this->verifikasi($id, true, null);
    }

    public function tolak(int $id)
    {
        if (! $this->validate(['alasan' => ['label' => 'Alasan penolakan', 'rules' => 'required|min_length[5]|max_length[255]']])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        return $this->verifikasi($id, false, trim((string) $this->request->getPost('alasan')));
    }

    private function verifikasi(int $id, bool $diterima, ?string $alasan)
    {
        $bayar = model(PembayaranModel::class)->select('pembayaran.*, pesanan.kode')
            ->join('pesanan', 'pesanan.id = pembayaran.pesanan_id')
            ->where('pembayaran.id', $id)->first()
            ?? throw PageNotFoundException::forPageNotFound();

        try {
            (new AlurPesanan())->verifikasi((int) $bayar['pesanan_id'], user('id'), $diterima, $alasan);
        } catch (RuntimeException $e) {
            return redirect()->to(url_to('panel.pembayaran'))->with('error', $e->getMessage());
        }

        return redirect()->to(url_to('panel.pembayaran'))->with(
            'success',
            $diterima
                ? "Pembayaran {$bayar['kode']} diterima. Pesanan masuk antrean pengiriman."
                : "Pembayaran {$bayar['kode']} ditolak. Pelanggan diminta mengunggah ulang.",
        );
    }
}
