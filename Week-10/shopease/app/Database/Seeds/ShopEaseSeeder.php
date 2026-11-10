<?php

namespace App\Database\Seeds;

use App\Libraries\AlurPesanan;
use App\Models\AlamatModel;
use App\Models\KategoriModel;
use App\Models\KeranjangModel;
use App\Models\PembayaranModel;
use App\Models\ProdukModel;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class ShopEaseSeeder extends Seeder
{
    public function run()
    {
        helper('app');

        $users = model(UserModel::class)->skipValidation(true);
        $admin  = $users->insert(['nama' => 'Admin ShopEase', 'email' => 'admin@shopease.test', 'password' => 'admin123', 'role' => 'admin']);
        $gudang = $users->insert(['nama' => 'Rudi Hartono', 'email' => 'gudang@shopease.test', 'password' => 'gudang123', 'role' => 'gudang']);
        $budi   = $users->insert(['nama' => 'Budi Santoso', 'email' => 'budi@mail.test', 'telepon' => '081234567890', 'password' => 'budi1234', 'role' => 'customer']);
        $sari   = $users->insert(['nama' => 'Sari Wulandari', 'email' => 'sari@mail.test', 'telepon' => '082233445566', 'password' => 'sari1234', 'role' => 'customer']);
        $andi   = $users->insert(['nama' => 'Andi Pratama', 'email' => 'andi@mail.test', 'telepon' => '085711223344', 'password' => 'andi1234', 'role' => 'customer']);

        $alamat = model(AlamatModel::class);
        $alamatBudi = $alamat->insert(['user_id' => $budi, 'label' => 'Kos', 'penerima' => 'Budi Santoso', 'telepon' => '081234567890', 'alamat' => 'Jl. Keputih Tegal Timur No. 12, Sukolilo', 'kota' => 'Surabaya', 'kode_pos' => '60111']);
        $alamat->insert(['user_id' => $budi, 'label' => 'Rumah', 'penerima' => 'Ibu Santoso', 'telepon' => '081398765432', 'alamat' => 'Jl. Veteran No. 8, Klojen', 'kota' => 'Malang', 'kode_pos' => '65111']);
        $alamatSari = $alamat->insert(['user_id' => $sari, 'label' => 'Rumah', 'penerima' => 'Sari Wulandari', 'telepon' => '082233445566', 'alamat' => 'Jl. Arief Rahman Hakim No. 10, Keputih', 'kota' => 'Surabaya', 'kode_pos' => '60111']);
        $alamatAndi = $alamat->insert(['user_id' => $andi, 'label' => 'Asrama', 'penerima' => 'Andi Pratama', 'telepon' => '085711223344', 'alamat' => 'Asrama Mahasiswa ITS Blok C No. 5', 'kota' => 'Surabaya', 'kode_pos' => '60117']);

        $kategori = [];
        foreach ([
            'Elektronik'        => 'Aksesori komputer dan gawai',
            'Fashion'           => 'Kaos, hoodie, dan topi',
            'Buku'              => 'Buku pemrograman dan kuliah',
            'Makanan & Minuman' => 'Kopi, teh, dan camilan',
            'Olahraga'          => 'Perlengkapan olahraga ringan',
            'Alat Tulis'        => 'Buku catatan dan pulpen',
        ] as $nama => $desk) {
            $kategori[$nama] = model(KategoriModel::class)->insert(['nama' => $nama, 'deskripsi' => $desk]);
        }

        $produk = [];
        foreach ([
            ['Elektronik', 'Mouse Wireless Silent', "Mouse nirkabel 2.4 GHz dengan klik senyap.\nBaterai AA tahan hingga 12 bulan, DPI 800/1200/1600.", 129000, 25, 90, 'mouse'],
            ['Elektronik', 'Keyboard Mekanikal TKL', 'Keyboard mekanikal tenkeyless, switch red, lampu RGB, kabel USB-C lepas pasang.', 459000, 10, 850, 'keyboard'],
            ['Elektronik', 'Headset Gaming 7.1', 'Headset dengan surround virtual 7.1 dan mikrofon yang bisa dilipat.', 349000, 8, 400, 'headset'],
            ['Elektronik', 'Power Bank 10.000 mAh', 'Power bank tipis dengan fast charging 20W dan dua port output.', 189000, 3, 220, 'powerbank'],
            ['Fashion', 'Kaos Polos Cotton Combed', 'Kaos cotton combed 30s, nyaman dan tidak panas. Ukuran S sampai XXL.', 69000, 50, 200, 'kaos'],
            ['Fashion', 'Hoodie Informatika', 'Hoodie fleece tebal dengan bordir logo di dada.', 185000, 15, 600, 'hoodie'],
            ['Fashion', 'Topi Baseball', 'Topi baseball dengan pengatur ukuran di belakang.', 55000, 12, 120, 'topi'],
            ['Buku', 'Belajar CodeIgniter 4', 'Panduan membangun aplikasi web MVC dengan CodeIgniter 4 dari dasar.', 98000, 12, 450, 'ci4'],
            ['Buku', 'Clean Code (Terjemahan)', 'Prinsip menulis kode yang mudah dibaca dan dirawat.', 145000, 7, 600, 'cleancode'],
            ['Buku', 'Algoritma dan Struktur Data', 'Buku ajar algoritma dan struktur data dengan contoh dalam C++.', 120000, 9, 550, 'algo'],
            ['Makanan & Minuman', 'Kopi Arabika Gayo 250 g', 'Biji kopi arabika Gayo sangrai medium, cocok untuk V60 dan tubruk.', 85000, 30, 260, 'kopi'],
            ['Makanan & Minuman', 'Teh Hijau Celup', 'Isi 25 kantong teh hijau tanpa gula.', 22000, 40, 80, 'teh'],
            ['Olahraga', 'Matras Yoga 6 mm', 'Matras TPE anti slip dengan tali pengikat.', 110000, 6, 900, 'yoga'],
            ['Olahraga', 'Botol Minum 1 Liter', 'Botol BPA free dengan penanda waktu minum.', 45000, 20, 180, 'botol'],
            ['Alat Tulis', 'Buku Catatan A5 Dotted', 'Buku catatan 120 halaman kertas 100 gsm dengan pola titik.', 35000, 60, 250, 'notebook'],
            ['Alat Tulis', 'Pulpen Gel 0.5 (Isi 5)', 'Pulpen gel tinta hitam, tidak mudah luntur.', 25000, 45, 60, 'pulpen'],
            // Dua produk nonaktif: tidak tampil di katalog dan tidak bisa dibeli
            ['Elektronik', 'Flashdisk 16 GB', 'Sudah tidak diproduksi.', 45000, 4, 20, null],
            ['Alat Tulis', 'Kalender Meja 2025', 'Edisi tahun lalu.', 15000, 30, 150, null],
        ] as [$kat, $nama, $desk, $harga, $stok, $berat, $img]) {
            $produk[$nama] = model(ProdukModel::class)->insert([
                'kategori_id' => $kategori[$kat], 'nama' => $nama, 'deskripsi' => $desk, 'harga' => $harga,
                'stok' => $stok, 'berat' => $berat, 'gambar' => $img ? $this->salinGambar($img) : null, 'aktif' => $img ? 1 : 0,
            ]);
        }

        // Contoh pesanan beberapa hari terakhir, dijalankan lewat AlurPesanan supaya stok dan status konsisten
        $alur = new AlurPesanan();
        $buat = function (int $user, int $alamatId, array $isi, int $hariLalu, string $jam) use ($alur, $produk): array {
            foreach ($isi as $nama => $qty) {
                model(KeranjangModel::class)->insert(['user_id' => $user, 'produk_id' => $produk[$nama], 'qty' => $qty]);
            }
            $p = $alur->checkout($user, $alamatId, null);
            $p['waktu'] = date('Y-m-d', strtotime("-{$hariLalu} days")) . " {$jam}";
            // Kode pesanan ikut memakai tanggal pesanan: SE-yymmdd-XXXX
            $p['kode'] = 'SE-' . date('ymd', strtotime($p['waktu'])) . substr($p['kode'], 9);
            $this->db->table('pesanan')->where('id', $p['id'])->update(['kode' => $p['kode'], 'created_at' => $p['waktu'], 'updated_at' => $p['waktu']]);

            return $p;
        };
        $bayar = function (array $p, string $bank, string $nama, int $menitKemudian) use ($alur): void {
            $t = strtotime($p['waktu']) + $menitKemudian * 60;
            $alur->unggahBukti($p['id'], ['bank_pengirim' => $bank, 'nama_rekening' => $nama, 'bukti' => $this->buatBukti($p['id'], $bank, $nama, $t)]);
            $this->db->table('pembayaran')->where('pesanan_id', $p['id'])->update(['diunggah_at' => date('Y-m-d H:i:s', $t)]);
        };

        $a = $buat($budi, $alamatBudi, ['Belajar CodeIgniter 4' => 1, 'Mouse Wireless Silent' => 1], 6, '09:15:00');
        $bayar($a, 'BCA', 'Budi Santoso', 20);
        $alur->verifikasi($a['id'], $admin, true);
        $alur->kirim($a['id'], $gudang, 'JNE', 'JNE0123456789');
        $alur->selesai($a['id']);

        $b = $buat($sari, $alamatSari, ['Kopi Arabika Gayo 250 g' => 2, 'Teh Hijau Celup' => 3], 5, '13:40:00');
        $bayar($b, 'Mandiri', 'Sari Wulandari', 45);
        $alur->verifikasi($b['id'], $admin, true);
        $alur->kirim($b['id'], $gudang, 'SiCepat', '004512398877');
        $alur->selesai($b['id']);

        $c = $buat($andi, $alamatAndi, ['Keyboard Mekanikal TKL' => 1], 4, '19:05:00');
        $bayar($c, 'BNI', 'Andi Pratama', 10);
        $alur->verifikasi($c['id'], $admin, true);
        $alur->kirim($c['id'], $gudang, 'J&T', 'JT8800123456');

        $d = $buat($budi, $alamatBudi, ['Hoodie Informatika' => 1, 'Topi Baseball' => 1, 'Kaos Polos Cotton Combed' => 2], 3, '10:30:00');
        $bayar($d, 'BCA', 'Budi Santoso', 30);
        $alur->verifikasi($d['id'], $admin, true);

        $e = $buat($sari, $alamatSari, ['Headset Gaming 7.1' => 1], 2, '16:20:00');
        $bayar($e, 'Mandiri', 'Sari Wulandari', 15);

        $f = $buat($andi, $alamatAndi, ['Matras Yoga 6 mm' => 1, 'Botol Minum 1 Liter' => 2], 1, '08:45:00');
        $bayar($f, 'BRI', 'Andi Pratama', 25);

        $g = $buat($sari, $alamatSari, ['Buku Catatan A5 Dotted' => 3], 1, '11:00:00');
        $bayar($g, 'BSI', 'Sari W', 5);
        $alur->verifikasi($g['id'], $admin, false, 'Nominal transfer Rp100.000, kurang dari total pesanan.');

        $h = $buat($andi, $alamatAndi, ['Pulpen Gel 0.5 (Isi 5)' => 2], 2, '21:10:00');
        $alur->batal($h['id']);

        // Waktu verifikasi dan pengiriman disesuaikan dengan tanggal pesanan
        foreach ([$a, $b, $c, $d, $g] as $p) {
            $this->db->query('UPDATE pembayaran SET diverifikasi_at = DATE_ADD(diunggah_at, INTERVAL 2 HOUR) WHERE pesanan_id = ?', [$p['id']]);
            $this->db->query('UPDATE pengiriman SET dikirim_at = DATE_ADD(?, INTERVAL 1 DAY), diterima_at = IF(diterima_at IS NULL, NULL, DATE_ADD(?, INTERVAL 3 DAY)) WHERE pesanan_id = ?', [$p['waktu'], $p['waktu'], $p['id']]);
        }
    }

    /** Gambar contoh di Seeds/img disalin ke folder upload dengan nama acak, sama seperti hasil upload. */
    private function salinGambar(string $nama): string
    {
        $tujuan = FCPATH . 'uploads/produk/';
        is_dir($tujuan) || mkdir($tujuan, 0775, true);
        $file = bin2hex(random_bytes(10)) . '.jpg';
        copy(__DIR__ . "/img/{$nama}.jpg", $tujuan . $file);

        return $file;
    }

    /** Membuat gambar bukti transfer tiruan dengan GD. */
    private function buatBukti(int $pesananId, string $bank, string $nama, int $waktu): string
    {
        $jumlah = (int) model(PembayaranModel::class)->where('pesanan_id', $pesananId)->first()['jumlah'];
        if (str_contains($nama, 'Sari W') && ! str_contains($nama, 'Wulandari')) {
            $jumlah = 100000; // contoh bukti yang nominalnya salah (akan ditolak)
        }

        return self::gambarBukti(WRITEPATH . 'uploads/bukti/', $bank, $nama, $jumlah, $waktu - 60);
    }

    public static function gambarBukti(string $dir, string $bank, string $nama, int $jumlah, int $waktu): string
    {
        is_dir($dir) || mkdir($dir, 0775, true);
        $kecil = imagecreatetruecolor(200, 260);
        $putih = imagecolorallocate($kecil, 255, 255, 255);
        $hijau = imagecolorallocate($kecil, 15, 118, 110);
        $abu   = imagecolorallocate($kecil, 90, 90, 90);
        $hitam = imagecolorallocate($kecil, 20, 20, 20);
        imagefill($kecil, 0, 0, $putih);
        imagefilledrectangle($kecil, 0, 0, 200, 34, $hijau);
        imagestring($kecil, 5, 10, 9, strtoupper($bank) . ' m-Transfer', $putih);
        imagestring($kecil, 4, 10, 48, 'TRANSFER BERHASIL', $hijau);
        $baris = [
            ['Tanggal', date('d/m/Y H:i', $waktu)],
            ['Dari', mb_strimwidth($nama, 0, 16, '.')],
            ['Ke', 'ShopEase'],
            ['No. Rek', '1234567890'],
            ['Jumlah', 'Rp' . number_format($jumlah, 0, ',', '.')],
            ['Ref', strtoupper(bin2hex(random_bytes(4)))],
        ];
        foreach ($baris as $i => [$k, $v]) {
            imagestring($kecil, 2, 10, 80 + $i * 26, $k, $abu);
            imagestring($kecil, 3, 70, 79 + $i * 26, $v, $hitam);
        }
        imageline($kecil, 10, 240, 190, 240, $abu);

        $besar = imagecreatetruecolor(400, 520);
        imagecopyresized($besar, $kecil, 0, 0, 0, 0, 400, 520, 200, 260);
        $file = bin2hex(random_bytes(10)) . '.png';
        imagepng($besar, $dir . $file);

        return $file;
    }
}
