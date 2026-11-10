<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Setiap route diberi nama ('as'), dan view membuat URL dengan url_to('nama', ...).
 * Aksi yang mengubah data selalu POST supaya dilindungi CSRF dan tidak terpicu lewat tautan.
 *
 * @var RouteCollection $routes
 */
$routes->set404Override('App\Controllers\Beranda::tidakDitemukan');

// ---------- Publik ----------
$routes->get('/', 'Beranda::index', ['as' => 'beranda']);
$routes->get('tentang', 'Beranda::tentang', ['as' => 'tentang']);
$routes->get('kontak', 'Beranda::kontak', ['as' => 'kontak']);
$routes->get('katalog', 'Katalog::index', ['as' => 'katalog']);
$routes->get('produk/(:segment)', 'Katalog::detail/$1', ['as' => 'produk.detail']);

// ---------- Tamu (belum login) ----------
$routes->group('', ['filter' => 'tamu'], static function (RouteCollection $routes): void {
    $routes->get('masuk', 'Auth::masuk', ['as' => 'masuk']);
    $routes->post('masuk', 'Auth::prosesMasuk', ['as' => 'masuk.proses']);
    $routes->get('daftar', 'Auth::daftar', ['as' => 'daftar']);
    $routes->post('daftar', 'Auth::prosesDaftar', ['as' => 'daftar.proses']);
});
$routes->post('keluar', 'Auth::keluar', ['as' => 'keluar', 'filter' => 'auth']);

// Bukti transfer: pemilik pesanan, admin, dan gudang (dicek di controller)
$routes->get('bukti/(:segment)', 'Bukti::lihat/$1', ['as' => 'bukti', 'filter' => 'auth']);

// ---------- Pelanggan ----------
$routes->group('', ['filter' => 'auth:customer'], static function (RouteCollection $routes): void {
    $routes->get('keranjang', 'Keranjang::index', ['as' => 'keranjang']);
    $routes->post('keranjang', 'Keranjang::tambah', ['as' => 'keranjang.tambah']);
    $routes->post('keranjang/ubah', 'Keranjang::ubah', ['as' => 'keranjang.ubah']);
    $routes->post('keranjang/(:num)/hapus', 'Keranjang::hapus/$1', ['as' => 'keranjang.hapus']);

    $routes->get('checkout', 'Checkout::index', ['as' => 'checkout']);
    $routes->post('checkout/alamat', 'Checkout::simpanAlamat', ['as' => 'checkout.alamat']);
    $routes->post('checkout', 'Checkout::proses', ['as' => 'checkout.proses']);

    $routes->get('pesanan', 'Pesanan::index', ['as' => 'pesanan']);
    $routes->get('pesanan/(:segment)', 'Pesanan::detail/$1', ['as' => 'pesanan.detail']);
    $routes->post('pesanan/(:segment)/bayar', 'Pesanan::bayar/$1', ['as' => 'pesanan.bayar']);
    $routes->post('pesanan/(:segment)/batal', 'Pesanan::batal/$1', ['as' => 'pesanan.batal']);
    $routes->post('pesanan/(:segment)/diterima', 'Pesanan::diterima/$1', ['as' => 'pesanan.diterima']);
});

// ---------- Panel: admin dan staf gudang ----------
$routes->group('panel', ['namespace' => 'App\Controllers\Panel', 'filter' => 'auth:admin,gudang'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Dashboard::index', ['as' => 'panel']);
    $routes->get('pesanan', 'Pesanan::index', ['as' => 'panel.pesanan']);
    $routes->get('pesanan/(:num)', 'Pesanan::detail/$1', ['as' => 'panel.pesanan.detail']);
    $routes->post('pesanan/(:num)/kirim', 'Pesanan::kirim/$1', ['as' => 'panel.pesanan.kirim']);
    $routes->post('pesanan/(:num)/selesai', 'Pesanan::selesai/$1', ['as' => 'panel.pesanan.selesai']);
});

// ---------- Panel: khusus admin ----------
$routes->group('panel', ['namespace' => 'App\Controllers\Panel', 'filter' => 'auth:admin'], static function (RouteCollection $routes): void {
    foreach (['kategori' => 'Kategori', 'produk' => 'Produk'] as $uri => $controller) {
        $routes->get($uri, "{$controller}::index", ['as' => "panel.{$uri}"]);
        $routes->get("{$uri}/baru", "{$controller}::baru", ['as' => "panel.{$uri}.baru"]);
        $routes->post($uri, "{$controller}::simpan", ['as' => "panel.{$uri}.simpan"]);
        $routes->get("{$uri}/(:num)/ubah", "{$controller}::ubah/$1", ['as' => "panel.{$uri}.ubah"]);
        $routes->post("{$uri}/(:num)", "{$controller}::perbarui/$1", ['as' => "panel.{$uri}.perbarui"]);
        $routes->post("{$uri}/(:num)/hapus", "{$controller}::hapus/$1", ['as' => "panel.{$uri}.hapus"]);
    }

    $routes->post('pesanan/(:num)/batal', 'Pesanan::batal/$1', ['as' => 'panel.pesanan.batal']);

    $routes->get('pembayaran', 'Pembayaran::index', ['as' => 'panel.pembayaran']);
    $routes->post('pembayaran/(:num)/terima', 'Pembayaran::terima/$1', ['as' => 'panel.pembayaran.terima']);
    $routes->post('pembayaran/(:num)/tolak', 'Pembayaran::tolak/$1', ['as' => 'panel.pembayaran.tolak']);

    $routes->get('laporan', 'Laporan::index', ['as' => 'panel.laporan']);
    $routes->get('laporan/csv', 'Laporan::csv', ['as' => 'panel.laporan.csv']);
});
