<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Cek login dan role sebelum controller dijalankan.
 * Pemakaian di route: 'filter' => 'auth' (cukup login) atau 'auth:admin' / 'auth:customer'.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            // Diingat 5 menit supaya setelah login pelanggan kembali ke halaman ini
            $session->setTempdata('tujuan', current_url(), 300);

            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! empty($arguments) && ! in_array($session->get('role'), $arguments, true)) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
