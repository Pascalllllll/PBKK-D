<?php

namespace App\Database\Seeds;

use App\Models\KategoriModel;
use App\Models\OrderModel;
use App\Models\ProdukModel;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

class TokoSeeder extends Seeder
{
    /** Gambar contoh di Seeds/img disalin ke folder upload dengan nama acak, sama seperti hasil upload. */
    private function salinGambar(string $nama): string
    {
        $tujuan = FCPATH . 'uploads/produk/';
        is_dir($tujuan) || mkdir($tujuan, 0775, true);
        $file = bin2hex(random_bytes(10)) . '.jpg';
        copy(__DIR__ . "/img/{$nama}.jpg", $tujuan . $file);

        return $file;
    }

    public function run()
    {
        $users = model(UserModel::class)->skipValidation(true);
        $users->insert(['username' => 'admin', 'password' => 'admin123', 'nama' => 'Admin Toko', 'email' => 'admin@toko.test', 'role' => 'admin']);
        $budi = $users->insert(['username' => 'budi', 'password' => 'budi123', 'nama' => 'Budi Santoso', 'email' => 'budi@mail.test', 'role' => 'customer']);
        $sari = $users->insert(['username' => 'sari', 'password' => 'sari123', 'nama' => 'Sari Wulandari', 'email' => 'sari@mail.test', 'role' => 'customer']);

        $kategori = [];
        foreach ([
            'Elektronik' => 'fa-laptop', 'Fashion' => 'fa-tshirt', 'Buku' => 'fa-book',
            'Makanan & Minuman' => 'fa-coffee', 'Olahraga' => 'fa-futbol', 'Alat Tulis' => 'fa-pencil-alt',
        ] as $nama => $ikon) {
            $kategori[$nama] = model(KategoriModel::class)->insert(['nama' => $nama, 'ikon' => $ikon]);
        }

        $produk = [];
        foreach ([
            ['Elektronik', 'Mouse Wireless Silent', 'Mouse nirkabel 2.4 GHz dengan klik senyap dan baterai tahan hingga 12 bulan.', 129000, 25, 'mouse'],
            ['Elektronik', 'Keyboard Mekanikal TKL', 'Keyboard mekanikal tenkeyless, switch red, lampu RGB, kabel USB-C lepas pasang.', 459000, 10, 'keyboard'],
            ['Elektronik', 'Headset Gaming 7.1', 'Headset dengan surround virtual 7.1 dan mikrofon yang bisa dilipat.', 349000, 8, 'headset'],
            ['Elektronik', 'Power Bank 10.000 mAh', 'Power bank tipis dengan fast charging 20W dan dua port output.', 189000, 3, 'powerbank'],
            ['Fashion', 'Kaos Polos Cotton Combed', 'Kaos cotton combed 30s, nyaman dan tidak panas. Tersedia ukuran S sampai XXL.', 69000, 50, 'kaos'],
            ['Fashion', 'Hoodie Informatika', 'Hoodie fleece tebal dengan bordir logo di dada.', 185000, 15, 'hoodie'],
            ['Fashion', 'Topi Baseball', 'Topi baseball dengan pengatur ukuran di belakang.', 55000, 0, 'topi'],
            ['Buku', 'Belajar CodeIgniter 4', 'Panduan membangun aplikasi web MVC dengan CodeIgniter 4 dari dasar.', 98000, 12, 'ci4'],
            ['Buku', 'Clean Code (Terjemahan)', 'Prinsip menulis kode yang mudah dibaca dan dirawat.', 145000, 7, 'cleancode'],
            ['Buku', 'Algoritma dan Struktur Data', 'Buku ajar algoritma dan struktur data dengan contoh dalam C++.', 120000, 9, 'algo'],
            ['Makanan & Minuman', 'Kopi Arabika Gayo 250 g', 'Biji kopi arabika Gayo sangrai medium, cocok untuk V60 dan tubruk.', 85000, 30, 'kopi'],
            ['Makanan & Minuman', 'Teh Hijau Celup', 'Isi 25 kantong teh hijau tanpa gula.', 22000, 40, 'teh'],
            ['Olahraga', 'Matras Yoga 6 mm', 'Matras TPE anti slip dengan tali pengikat.', 110000, 6, 'yoga'],
            ['Olahraga', 'Botol Minum 1 Liter', 'Botol BPA free dengan penanda waktu minum.', 45000, 20, 'botol'],
            ['Alat Tulis', 'Buku Catatan A5 Dotted', 'Buku catatan 120 halaman kertas 100 gsm dengan pola titik.', 35000, 60, 'notebook'],
            ['Alat Tulis', 'Pulpen Gel 0.5 (Isi 5)', 'Pulpen gel tinta hitam, tidak mudah luntur.', 25000, 45, 'pulpen'],
        ] as [$kat, $nama, $desk, $harga, $stok, $img]) {
            $produk[$nama] = model(ProdukModel::class)->insert([
                'kategori_id' => $kategori[$kat], 'nama' => $nama, 'deskripsi' => $desk, 'harga' => $harga, 'stok' => $stok,
                'gambar' => $this->salinGambar($img),
            ]);
        }

        // Contoh pesanan supaya dashboard dan riwayat tidak kosong
        $orders = model(OrderModel::class);
        $kirim  = ['nama_penerima' => 'Budi Santoso', 'telepon' => '081234567890', 'alamat' => 'Jl. Teknik Kimia, Keputih, Sukolilo, Surabaya'];
        $a = $orders->buat($budi, [['id' => $produk['Belajar CodeIgniter 4'], 'qty' => 1], ['id' => $produk['Mouse Wireless Silent'], 'qty' => 1]], $kirim);
        $orders->ubahStatus($a, 'dibayar');
        $orders->ubahStatus($a, 'dikirim');
        $orders->ubahStatus($a, 'selesai');

        $b = $orders->buat($sari, [['id' => $produk['Kopi Arabika Gayo 250 g'], 'qty' => 2]], [
            'nama_penerima' => 'Sari Wulandari', 'telepon' => '082233445566', 'alamat' => 'Jl. Arief Rahman Hakim 10, Surabaya',
        ]);
        $orders->ubahStatus($b, 'dibayar');

        $orders->buat($sari, [['id' => $produk['Hoodie Informatika'], 'qty' => 1]], [
            'nama_penerima' => 'Sari Wulandari', 'telepon' => '082233445566', 'alamat' => 'Jl. Arief Rahman Hakim 10, Surabaya',
        ]);
    }
}
