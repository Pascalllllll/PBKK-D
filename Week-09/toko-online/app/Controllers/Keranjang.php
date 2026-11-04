<?php

namespace App\Controllers;

use App\Libraries\Cart;

class Keranjang extends BaseController
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function index(): string
    {
        return view('keranjang/index', ['title' => 'Keranjang Belanja'] + $this->cart->items());
    }

    public function tambah()
    {
        $pesan = $this->cart->add((int) $this->request->getPost('produk_id'), (int) $this->request->getPost('qty'));

        return redirect()->to('/keranjang')->with(
            $pesan === null ? 'success' : 'warning',
            $pesan ?? 'Produk ditambahkan ke keranjang.',
        );
    }

    public function update()
    {
        $pesan = $this->cart->update((array) $this->request->getPost('qty'));

        return redirect()->to('/keranjang')->with($pesan === null ? 'success' : 'warning', $pesan ?? 'Keranjang diperbarui.');
    }

    public function hapus(int $id)
    {
        $this->cart->remove($id);

        return redirect()->to('/keranjang')->with('success', 'Produk dihapus dari keranjang.');
    }
}
