<?php

namespace App\Controllers;

use App\Models\PembayaranModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/** Menampilkan bukti transfer dari writable/uploads/bukti hanya kepada yang berhak. */
class Bukti extends BaseController
{
    public function lihat(string $file)
    {
        $bayar = model(PembayaranModel::class)
            ->select('pembayaran.bukti, pesanan.user_id')
            ->join('pesanan', 'pesanan.id = pembayaran.pesanan_id')
            ->where('pembayaran.bukti', $file)
            ->first();

        $boleh = $bayar !== null && (session('role') !== 'customer' || (int) $bayar['user_id'] === user('id'));
        $path  = WRITEPATH . 'uploads/bukti/' . basename($file);

        if (! $boleh || ! is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody(file_get_contents($path));
    }
}
