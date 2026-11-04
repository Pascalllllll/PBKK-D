<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

/** Kerangka bersama untuk CRUD master data admin. */
abstract class AdminController extends BaseController
{
    protected Model $model;

    /** Segmen URL, misalnya 'dosen' untuk /admin/dosen. */
    protected string $uri;

    protected function findOr404(int $id): array
    {
        return $this->model->find($id) ?? throw PageNotFoundException::forPageNotFound();
    }

    protected function q(): string
    {
        return trim((string) $this->request->getGet('q'));
    }

    /** Kembali ke form dengan input lama dan pesan validasi. */
    protected function back(?array $errors = null)
    {
        $errors ??= $this->model->errors() + ($this->validator?->getErrors() ?? []);

        return redirect()->back()->withInput()->with('errors', $errors);
    }

    protected function toIndex(string $type, string $message)
    {
        return redirect()->to('/admin/' . $this->uri)->with($type, $message);
    }
}
