<?php

if (! function_exists('user')) {
    /** Data user yang sedang login (atau satu kolomnya). */
    function user(?string $key = null): mixed
    {
        $user = session('user') ?? [];

        return $key === null ? $user : ($user[$key] ?? null);
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|string|null $angka): string
    {
        return 'Rp' . number_format((int) $angka, 0, ',', '.');
    }
}

if (! function_exists('jumlah_keranjang')) {
    function jumlah_keranjang(): int
    {
        return (new \App\Libraries\Cart())->count();
    }
}

if (! function_exists('gambar_produk')) {
    /** Gambar produk, atau null jika belum ada (view menampilkan ikon kategori). */
    function gambar_produk(?string $gambar): ?string
    {
        return $gambar ? base_url('uploads/produk/' . $gambar) : null;
    }
}

if (! function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $warna = [
            'pending' => 'warning', 'dibayar' => 'info', 'dikirim' => 'primary', 'selesai' => 'success', 'batal' => 'secondary',
        ][$status] ?? 'light';

        return '<span class="badge badge-' . $warna . ' text-capitalize">' . esc($status) . '</span>';
    }
}

if (! function_exists('kode_pesanan')) {
    function kode_pesanan(int|string $id): string
    {
        return 'INV-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}

if (! function_exists('invalid')) {
    /** Kelas Bootstrap is-invalid untuk field yang gagal validasi. */
    function invalid(string $field): string
    {
        return isset(session('errors')[$field]) ? ' is-invalid' : '';
    }
}

if (! function_exists('field_error')) {
    function field_error(string $field): string
    {
        $error = session('errors')[$field] ?? null;

        return $error ? '<div class="invalid-feedback">' . esc($error) . '</div>' : '';
    }
}
