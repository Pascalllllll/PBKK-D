<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Cek login dan role sebelum controller dijalankan.
 * Pemakaian di route: 'auth' (cukup login), 'auth:customer', atau 'auth:admin,gudang'.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            // Halaman yang diminta diingat 5 menit, jadi setelah login langsung kembali ke sana
            if ($request->getMethod() === 'GET') {
                $session->setTempdata('tujuan', current_url(true)->__toString(), 300);
            }

            return redirect()->route('masuk')->with('info', 'Silakan masuk terlebih dahulu untuk melanjutkan.');
        }

        if (! empty($arguments) && ! in_array($session->get('role'), $arguments, true)) {
            // Sudah login tetapi role tidak sesuai: 403, URL tidak berubah
            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/terlarang', [
                    'title'  => 'Akses Ditolak',
                    'path'   => '/' . ltrim($request->getUri()->getPath(), '/'),
                    'diizinkan' => $arguments,
                ]));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
