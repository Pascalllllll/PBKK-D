<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;

/** Ongkir, rekening tujuan, instruksi pembayaran, dan kontak toko (khusus admin). */
class Pengaturan extends BaseController
{
    public function index(): string
    {
        return view('panel/pengaturan/index', [
            'title' => 'Pengaturan Toko',
            'nilai' => model(PengaturanModel::class)->semua(),
        ]);
    }

    public function simpan()
    {
        $data = array_map('trim', (array) $this->request->getPost(array_keys(PengaturanModel::BAWAAN)));
        $data['rekening'] = implode("\n", array_filter(array_map('trim', preg_split('/\R/', $data['rekening'] ?? ''))));

        $rules = [
            'ongkir'        => ['label' => 'Ongkos kirim', 'rules' => 'required|is_natural|less_than[1000000]'],
            'gratis_ongkir' => ['label' => 'Batas gratis ongkir', 'rules' => 'required|is_natural|less_than[100000000]'],
            'rekening'      => ['label' => 'Rekening tujuan', 'rules' => 'permit_empty|max_length[1000]'],
            'instruksi'     => ['label' => 'Instruksi pembayaran', 'rules' => 'permit_empty|max_length[500]'],
            'email'         => ['label' => 'Email toko', 'rules' => 'permit_empty|valid_email'],
            'whatsapp'      => ['label' => 'WhatsApp', 'rules' => 'permit_empty|regex_match[/^(\+62|0)8[0-9]{8,12}$/]'],
        ];
        $valid  = $this->validateData($data, $rules, ['whatsapp' => ['regex_match' => 'Nomor WhatsApp harus diawali 08 atau +628.']]);
        $errors = $valid ? [] : $this->validator->getErrors();

        // Setiap baris rekening harus berformat "Bank | Nomor | Atas nama"
        foreach (preg_split('/\R/', $data['rekening']) as $i => $baris) {
            if ($baris !== '' && count(array_filter(array_map('trim', explode('|', $baris)), 'strlen')) !== 3) {
                $errors['rekening'] = 'Baris ' . ($i + 1) . ' rekening harus berformat "Bank | Nomor | Atas nama".';
                break;
            }
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $model = model(PengaturanModel::class);
        $model->simpan($data);

        return redirect()->to(url_to('panel.pengaturan'))->with(
            $model->checkoutSiap() ? 'success' : 'warning',
            $model->checkoutSiap() ? 'Pengaturan disimpan.' : 'Pengaturan disimpan. Checkout pelanggan ditutup sampai ada rekening tujuan.',
        );
    }
}
