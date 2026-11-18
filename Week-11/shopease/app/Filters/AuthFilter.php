<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Wajib login, dan bila diberi argumen, role harus cocok.
 * Pemakaian di route: 'auth', 'auth:customer', atau 'auth:admin,gudang'.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = service('autentikasi')->user();

        if ($user === null) {
            // Halaman yang diminta (dan role yang boleh membukanya) diingat 5 menit,
            // jadi setelah login langsung kembali ke sana
            if ($request->getMethod() === 'GET') {
                session()->setTempdata('tujuan', ['url' => current_url(true)->__toString(), 'role' => (array) $arguments], 300);
            }
            $redirect = redirect()->route('masuk');

            // Jika baru saja dikeluarkan (akun dibekukan / sandi diganti), alasan itu yang ditampilkan
            return session('warning') ? $redirect : $redirect->with('info', 'Silakan masuk terlebih dahulu untuk melanjutkan.');
        }

        if (! empty($arguments) && ! in_array($user['role'], $arguments, true)) {
            // Sudah login tetapi role tidak sesuai: 403, URL tidak berubah
            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/terlarang', [
                    'title'     => 'Akses Ditolak',
                    'path'      => '/' . ltrim($request->getUri()->getPath(), '/'),
                    'diizinkan' => $arguments,
                ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
