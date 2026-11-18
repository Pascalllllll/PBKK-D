<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\PesananModel;

class Laporan extends BaseController
{
    public function index(): string
    {
        $f     = $this->filter();
        $model = model(PesananModel::class);
        $rows  = $model->daftar($f)->findAll();
        $lunas = array_filter($rows, static fn ($r) => in_array($r['status'], PesananModel::SUDAH_DIBAYAR, true));

        return view('panel/laporan/index', [
            'title'       => 'Laporan Penjualan',
            'filter'      => $f,
            'rows'        => $rows,
            'omzet'       => array_sum(array_column($lunas, 'total')),
            'ongkir'      => array_sum(array_column($lunas, 'ongkir')),
            'jumlahLunas' => count($lunas),
            'barang'      => array_sum(array_column($lunas, 'jumlah_barang')),
            'perKategori' => $model->perKategori($f['dari'], $f['sampai']),
        ]);
    }

    /** Unduh laporan dalam format CSV (bisa dibuka di Excel). */
    public function csv()
    {
        $f    = $this->filter();
        $rows = model(PesananModel::class)->daftar($f)->findAll();

        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['Kode', 'Tanggal', 'Pelanggan', 'Barang', 'Subtotal', 'Ongkir', 'Total', 'Status'], ';');
        foreach ($rows as $r) {
            fputcsv($out, [$r['kode'], $r['created_at'], $r['pelanggan'], $r['jumlah_barang'], $r['subtotal'], $r['ongkir'], $r['total'], label_status($r['status'])[0]], ';');
        }
        rewind($out);

        return $this->response->download("laporan-{$f['dari']}-{$f['sampai']}.csv", stream_get_contents($out))
            ->setContentType('text/csv');
    }

    /** Periode default: awal bulan sampai hari ini. */
    private function filter(): array
    {
        $tgl = static fn ($v, $d) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $v) ? $v : $d;
        $f   = [
            'dari'   => $tgl($this->request->getGet('dari'), date('Y-m-01')),
            'sampai' => $tgl($this->request->getGet('sampai'), date('Y-m-d')),
            'status' => (string) $this->request->getGet('status'),
        ];
        if ($f['dari'] > $f['sampai']) {
            [$f['dari'], $f['sampai']] = [$f['sampai'], $f['dari']];
        }

        return $f;
    }
}
