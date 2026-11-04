<?php

namespace App\Controllers;

use App\Libraries\Cart;
use App\Models\OrderModel;
use RuntimeException;

class Checkout extends BaseController
{
    public function index()
    {
        $cart = (new Cart())->items();
        if ($cart['items'] === []) {
            return redirect()->to('/keranjang')->with('error', 'Keranjang masih kosong.');
        }

        return view('checkout/index', ['title' => 'Checkout'] + $cart);
    }

    public function proses()
    {
        $cart = new Cart();
        if ($cart->raw() === []) {
            return redirect()->to('/keranjang')->with('error', 'Keranjang masih kosong.');
        }

        $rules = [
            'nama_penerima' => ['label' => 'Nama penerima', 'rules' => 'required|min_length[3]|max_length[100]'],
            'telepon'       => ['label' => 'Telepon', 'rules' => 'required|regex_match[/^(\+62|0)8[0-9]{8,12}$/]'],
            'alamat'        => ['label' => 'Alamat', 'rules' => 'required|min_length[10]|max_length[500]'],
        ];
        $messages = ['telepon' => ['regex_match' => 'Nomor telepon harus nomor HP Indonesia, contoh 081234567890.']];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $items = [];
        foreach ($cart->raw() as $id => $qty) {
            $items[] = ['id' => (int) $id, 'qty' => (int) $qty];
        }

        try {
            $orderId = model(OrderModel::class)->buat(user('id'), $items, $this->validator->getValidated());
        } catch (RuntimeException $e) {
            // Stok berubah sejak barang masuk keranjang: tidak ada yang tersimpan (rollback)
            return redirect()->to('/keranjang')->with('error', $e->getMessage());
        }

        $cart->clear();

        return redirect()->to("/pesanan/{$orderId}")->with('success', 'Pesanan berhasil dibuat.');
    }
}
