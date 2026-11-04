<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Admin: CRUD master data dengan pola URL yang sama
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin'], static function ($routes) {
    foreach (['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'matakuliah' => 'Matakuliah'] as $uri => $controller) {
        $routes->get($uri, "{$controller}::index");
        $routes->get("{$uri}/new", "{$controller}::new");
        $routes->post($uri, "{$controller}::create");
        $routes->get("{$uri}/(:num)/edit", "{$controller}::edit/$1");
        $routes->post("{$uri}/(:num)", "{$controller}::update/$1");
        $routes->post("{$uri}/(:num)/delete", "{$controller}::delete/$1");
    }
});

// Mahasiswa: KRS dan KHS
$routes->group('', ['filter' => 'auth:mahasiswa'], static function ($routes) {
    $routes->get('krs', 'Krs::index');
    $routes->post('krs', 'Krs::ambil');
    $routes->post('krs/(:num)/delete', 'Krs::drop/$1');
    $routes->get('khs', 'Krs::khs');
});

// Dosen: input nilai matakuliah yang diampu
$routes->group('nilai', ['filter' => 'auth:dosen'], static function ($routes) {
    $routes->get('/', 'Nilai::index');
    $routes->get('(:num)', 'Nilai::show/$1');
    $routes->post('(:num)', 'Nilai::save/$1');
});
