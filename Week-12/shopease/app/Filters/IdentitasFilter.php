<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter global. Sebelum request: memastikan session masih sah (akun aktif, versi sesi cocok)
 * atau memulihkan login dari cookie "Ingat saya". Sesudahnya: cookie dan header no-store dipasang.
 */
class IdentitasFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        service('autentikasi')->user();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Controller yang mengembalikan redirect() memakai objek response lain,
        // jadi cookie yang dipasang library (token baru / hapus token) dipindahkan ke sini.
        if ($response !== service('response')) {
            foreach (service('response')->getCookies() as $cookie) {
                $response->setCookie($cookie);
            }
        }

        // Halaman yang dibuka saat login tidak boleh disimpan browser,
        // supaya tombol Back setelah keluar tidak menampilkan data akun.
        if (session('user') !== null && ! str_starts_with($response->getHeaderLine('Content-Type'), 'image/')) {
            $response->setHeader('Cache-Control', 'no-store, private');
        }
    }
}
