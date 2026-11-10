<?php

namespace App\Controllers\Panel;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

/** Bagian yang sama di controller CRUD panel: cari-atau-404, kembali ke form, dan redirect ke daftar. */
abstract class PanelController extends BaseController
{
    protected Model $model;

    /** Nama route daftar, misalnya 'panel.kategori'. */
    protected string $routeDaftar;

    protected function cariAtau404(int $id): array
    {
        return $this->model->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }

    protected function kembaliKeForm(?array $errors = null)
    {
        $errors ??= $this->model->errors() + ($this->validator?->getErrors() ?? []);

        return redirect()->back()->withInput()->with('errors', $errors);
    }

    protected function keDaftar(string $jenis, string $pesan)
    {
        return redirect()->to(url_to($this->routeDaftar))->with($jenis, $pesan);
    }
}
