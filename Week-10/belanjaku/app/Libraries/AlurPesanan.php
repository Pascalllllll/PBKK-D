<?php

namespace App\Libraries;

use App\Models\AlamatModel;
use App\Models\KeranjangModel;
use App\Models\PembayaranModel;
use App\Models\PengirimanModel;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;

/**
 * Semua perubahan pada pesanan lewat kelas ini: checkout, unggah bukti, verifikasi,
 * pengiriman, selesai, dan batal. Setiap langkah berjalan di dalam satu transaksi
 * dan baris pesanan dikunci (FOR UPDATE), jadi dua klik bersamaan tidak bisa
 * memindahkan status dua kali.
 */
class AlurPesanan
{
    public const ONGKIR          = 15000;
    public const GRATIS_ONGKIR   = 300000;

    /** Perpindahan status yang sah. Status selesai dan batal bersifat final. */
    public const ALUR = [
        'menunggu_bayar'      => ['menunggu_verifikasi', 'batal'],
        'menunggu_verifikasi' => ['diproses', 'menunggu_bayar', 'batal'],
        'diproses'            => ['dikirim', 'batal'],
        'dikirim'             => ['selesai'],
        'selesai'             => [],
        'batal'               => [],
    ];

    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public static function ongkir(int $subtotal): int
    {
        return $subtotal >= self::GRATIS_ONGKIR ? 0 : self::ONGKIR;
    }

    /**
     * Membuat pesanan dari isi keranjang user.
     * Urutan: kunci produk -> cek aktif & stok -> hitung ulang harga dari DB -> simpan pesanan,
     * item, dan pembayaran -> kurangi stok -> kosongkan keranjang.
     *
     * @throws RuntimeException jika keranjang kosong, produk nonaktif, atau stok kurang
     */
    public function checkout(int $userId, int $alamatId, ?string $catatan): array
    {
        return $this->transaksi(function () use ($userId, $alamatId, $catatan): array {
            $alamat = model(AlamatModel::class)->where(['id' => $alamatId, 'user_id' => $userId])->first()
                ?? throw new RuntimeException('Pilih alamat pengiriman terlebih dahulu.');

            // FOR UPDATE: checkout kedua yang datang bersamaan menunggu, lalu mendapati keranjang sudah kosong
            $keranjang = $this->db->query('SELECT produk_id, qty FROM keranjang WHERE user_id = ? ORDER BY id FOR UPDATE', [$userId])->getResultArray();
            if ($keranjang === []) {
                throw new RuntimeException('Keranjang kosong.');
            }

            $subtotal = 0;
            $baris    = [];
            foreach ($keranjang as $k) {
                $p = $this->db->query('SELECT id, nama, harga, stok, aktif FROM produk WHERE id = ? FOR UPDATE', [$k['produk_id']])->getRowArray();
                if (! $p['aktif']) {
                    throw new RuntimeException("Produk \"{$p['nama']}\" sudah tidak dijual. Hapus dari keranjang untuk melanjutkan.");
                }
                if ($p['stok'] < $k['qty']) {
                    throw new RuntimeException("Stok \"{$p['nama']}\" tidak mencukupi (tersisa {$p['stok']}).");
                }

                $baris[] = [
                    'produk_id' => $p['id'], 'nama_produk' => $p['nama'], 'harga' => $p['harga'],
                    'qty' => $k['qty'], 'subtotal' => $p['harga'] * $k['qty'],
                ];
                $subtotal += $p['harga'] * $k['qty'];
            }

            $ongkir = self::ongkir($subtotal);
            $kode   = $this->kodeBaru();
            $id     = model(PesananModel::class)->insert([
                'kode'     => $kode,
                'user_id'  => $userId,
                'penerima' => $alamat['penerima'],
                'telepon'  => $alamat['telepon'],
                'alamat'   => AlamatModel::format($alamat),
                'subtotal' => $subtotal,
                'ongkir'   => $ongkir,
                'total'    => $subtotal + $ongkir,
                'catatan'  => $catatan ?: null,
                'status'   => 'menunggu_bayar',
            ], true);

            foreach ($baris as $b) {
                model(PesananItemModel::class)->insert($b + ['pesanan_id' => $id]);
                $this->db->query('UPDATE produk SET stok = stok - ? WHERE id = ?', [$b['qty'], $b['produk_id']]);
            }

            model(PembayaranModel::class)->insert(['pesanan_id' => $id, 'jumlah' => $subtotal + $ongkir, 'status' => 'belum']);
            model(KeranjangModel::class)->where('user_id', $userId)->delete();

            return ['id' => $id, 'kode' => $kode];
        });
    }

    /** Pelanggan mengunggah bukti transfer: menunggu_bayar -> menunggu_verifikasi. */
    public function unggahBukti(int $pesananId, array $data): void
    {
        $this->transaksi(function () use ($pesananId, $data): void {
            $this->pindah($pesananId, 'menunggu_verifikasi');
            model(PembayaranModel::class)->where('pesanan_id', $pesananId)->set($data + [
                'status' => 'menunggu', 'alasan_tolak' => null, 'diunggah_at' => date('Y-m-d H:i:s'),
            ])->update();
        });
    }

    /** Admin menerima bukti: pesanan diproses. Menolak: pesanan kembali menunggu pembayaran. */
    public function verifikasi(int $pesananId, int $adminId, bool $diterima, ?string $alasan = null): void
    {
        $this->transaksi(function () use ($pesananId, $adminId, $diterima, $alasan): void {
            $bayar = model(PembayaranModel::class)->where('pesanan_id', $pesananId)->first();
            if (($bayar['status'] ?? null) !== 'menunggu') {
                throw new RuntimeException('Pembayaran ini sudah diverifikasi sebelumnya.');
            }

            $this->pindah($pesananId, $diterima ? 'diproses' : 'menunggu_bayar');
            model(PembayaranModel::class)->update($bayar['id'], [
                'status'            => $diterima ? 'diterima' : 'ditolak',
                'alasan_tolak'      => $diterima ? null : $alasan,
                'diverifikasi_oleh' => $adminId,
                'diverifikasi_at'   => date('Y-m-d H:i:s'),
            ]);
        });
    }

    /** Admin/gudang mengisi kurir dan resi: diproses -> dikirim. */
    public function kirim(int $pesananId, int $petugasId, string $kurir, string $resi): void
    {
        $this->transaksi(function () use ($pesananId, $petugasId, $kurir, $resi): void {
            $this->pindah($pesananId, 'dikirim');
            model(PengirimanModel::class)->insert([
                'pesanan_id' => $pesananId, 'kurir' => $kurir, 'no_resi' => strtoupper($resi),
                'diinput_oleh' => $petugasId, 'dikirim_at' => date('Y-m-d H:i:s'),
            ]);
        });
    }

    /** Barang sampai (dikonfirmasi pelanggan atau petugas): dikirim -> selesai. */
    public function selesai(int $pesananId): void
    {
        $this->transaksi(function () use ($pesananId): void {
            $this->pindah($pesananId, 'selesai');
            model(PengirimanModel::class)->where('pesanan_id', $pesananId)->set('diterima_at', date('Y-m-d H:i:s'))->update();
        });
    }

    /** Membatalkan pesanan dan mengembalikan stok di transaksi yang sama. */
    public function batal(int $pesananId): void
    {
        $this->transaksi(function () use ($pesananId): void {
            $this->pindah($pesananId, 'batal');
            foreach (model(PesananItemModel::class)->where('pesanan_id', $pesananId)->findAll() as $it) {
                $this->db->query('UPDATE produk SET stok = stok + ? WHERE id = ?', [$it['qty'], $it['produk_id']]);
            }
        });
    }

    /** Kunci baris pesanan, cek ALUR, lalu ubah status. Harus dipanggil di dalam transaksi. */
    private function pindah(int $pesananId, string $ke): void
    {
        $p = $this->db->query('SELECT status FROM pesanan WHERE id = ? FOR UPDATE', [$pesananId])->getRowArray()
            ?? throw new RuntimeException('Pesanan tidak ditemukan.');

        if (! in_array($ke, self::ALUR[$p['status']], true)) {
            [$dari] = label_status($p['status']);
            [$tujuan] = label_status($ke);

            throw new RuntimeException("Status pesanan tidak bisa diubah dari \"{$dari}\" ke \"{$tujuan}\".");
        }

        model(PesananModel::class)->update($pesananId, ['status' => $ke]);
    }

    /** BK-261110-7Q3F: tanggal + 4 karakter acak (tanpa huruf yang mirip angka). */
    private function kodeBaru(): string
    {
        $huruf = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        do {
            $acak = '';
            for ($i = 0; $i < 4; $i++) {
                $acak .= $huruf[random_int(0, strlen($huruf) - 1)];
            }
            $kode = 'BK-' . date('ymd') . '-' . $acak;
        } while (model(PesananModel::class)->where('kode', $kode)->countAllResults() > 0);

        return $kode;
    }

    private function transaksi(callable $fn): mixed
    {
        $this->db->transBegin();

        try {
            $hasil = $fn();
            $this->db->transCommit();

            return $hasil;
        } catch (\Throwable $e) {
            $this->db->transRollback();

            throw $e;
        }
    }
}
