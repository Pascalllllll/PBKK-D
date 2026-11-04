<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Toko::index');
$routes->get('produk/(:num)', 'Toko::detail/$1');

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');
$routes->post('logout', 'Auth::logout');

// Keranjang di session, boleh tanpa login
$routes->get('keranjang', 'Keranjang::index');
$routes->post('keranjang/tambah', 'Keranjang::tambah');
$routes->post('keranjang/update', 'Keranjang::update');
$routes->post('keranjang/hapus/(:num)', 'Keranjang::hapus/$1');

// Customer: checkout dan pesanan
$routes->group('', ['filter' => 'auth:customer'], static function ($routes) {
    $routes->get('checkout', 'Checkout::index');
    $routes->post('checkout', 'Checkout::proses');
    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::show/$1');
    $routes->post('pesanan/(:num)/batal', 'Pesanan::batal/$1');
});

// Admin: dashboard, CRUD kategori dan produk, kelola pesanan
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');

    foreach (['kategori' => 'Kategori', 'produk' => 'Produk'] as $uri => $controller) {
        $routes->get($uri, "{$controller}::index");
        $routes->get("{$uri}/new", "{$controller}::new");
        $routes->post($uri, "{$controller}::create");
        $routes->get("{$uri}/(:num)/edit", "{$controller}::edit/$1");
        $routes->post("{$uri}/(:num)", "{$controller}::update/$1");
        $routes->post("{$uri}/(:num)/delete", "{$controller}::delete/$1");
    }

    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::show/$1');
    $routes->post('pesanan/(:num)/status', 'Pesanan::status/$1');
});
