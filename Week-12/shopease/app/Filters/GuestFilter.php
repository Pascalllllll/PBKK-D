<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Halaman masuk dan daftar hanya untuk tamu. User yang sudah login diarahkan ke halaman awalnya. */
class GuestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($user = service('autentikasi')->user()) {
            return redirect()->to(halaman_awal($user['role']));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
