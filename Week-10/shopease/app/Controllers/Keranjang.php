<?php

namespace App\Controllers;

use App\Libraries\AlurPesanan;
use App\Models\KeranjangModel;
use App\Models\ProdukModel;

class Keranjang extends BaseController
{
    private KeranjangModel $keranjang;

    public function __construct()
    {
        $this->keranjang = model(KeranjangModel::class);
    }

    public function index(): string
    {
        $isi = $this->keranjang->isi(user('id'));

        return view('keranjang/index', $isi + [
            'title'  => 'Keranjang',
            'ongkir' => AlurPesanan::ongkir($isi['subtotal']),
        ]);
    }

    public function tambah()
    {
        $produkId = (int) $this->request->getPost('produk_id');
        $qty      = (int) $this->request->getPost('qty');

        if ($qty < 1) {
            return redirect()->back()->with('error', 'Jumlah minimal 1.');
        }

        // Produk nonaktif ditolak walaupun ID-nya dikirim manual
        $produk = model(ProdukModel::class)->aktif($produkId);
        if ($produk === null) {
            return redirect()->back()->with('error', 'Produk tidak tersedia.');
        }

        // Stok dicek terhadap jumlah SETELAH digabung dengan isi keranjang
        $ada      = $this->keranjang->baris(user('id'), $produkId);
        $diKeranjang = (int) ($ada['qty'] ?? 0);
        $total    = $diKeranjang + $qty;
        if ($total > $produk['stok']) {
            $pesan = "Stok {$produk['nama']} tidak mencukupi (tersisa {$produk['stok']}).";
            if ($diKeranjang > 0) {
                $pesan .= " Di keranjang sudah ada {$diKeranjang}, jadi maksimal tambah " . max(0, $produk['stok'] - $diKeranjang) . ' lagi.';
            }

            return redirect()->back()->with('error', $pesan);
        }

        $ada === null
            ? $this->keranjang->insert(['user_id' => user('id'), 'produk_id' => $produkId, 'qty' => $qty])
            : $this->keranjang->update($ada['id'], ['qty' => $total]);

        return redirect()->to(url_to('keranjang'))->with('success', "{$produk['nama']} ditambahkan ke keranjang.");
    }

    public function ubah()
    {
        $peringatan = [];

        foreach ((array) $this->request->getPost('qty') as $id => $qty) {
            // Hanya baris milik user sendiri yang bisa diubah
            $baris = $this->keranjang->where(['id' => (int) $id, 'user_id' => user('id')])->first();
            if ($baris === null) {
                continue;
            }

            $qty  = (int) $qty;
            $stok = (int) model(ProdukModel::class)->find($baris['produk_id'])['stok'];
            if ($qty < 1) {
                $this->keranjang->delete($baris['id']);
            } elseif ($qty > $stok) {
                $peringatan[] = "jumlah dibatasi stok ({$stok})";
                $this->keranjang->update($baris['id'], ['qty' => max(1, $stok)]);
            } else {
                $this->keranjang->update($baris['id'], ['qty' => $qty]);
            }
        }

        return redirect()->to(url_to('keranjang'))->with(
            $peringatan ? 'warning' : 'success',
            $peringatan ? 'Keranjang diperbarui, tetapi ' . implode(', ', array_unique($peringatan)) . '.' : 'Keranjang diperbarui.',
        );
    }

    public function hapus(int $id)
    {
        $this->keranjang->where(['id' => $id, 'user_id' => user('id')])->delete();

        return redirect()->to(url_to('keranjang'))->with('success', 'Produk dihapus dari keranjang.');
    }
}
