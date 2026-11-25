<?php

if (! function_exists('user')) {
    /** Data user yang sedang login (atau satu kolomnya). Isinya sudah dicek ulang oleh IdentitasFilter. */
    function user(?string $key = null): mixed
    {
        $user = session('user') ?? [];

        return $key === null ? $user : ($user[$key] ?? null);
    }
}

if (! function_exists('sudah_masuk')) {
    function sudah_masuk(): bool
    {
        return session('user') !== null;
    }
}

if (! function_exists('label_role')) {
    function label_role(?string $role): string
    {
        return config('Akun')->role[$role] ?? 'Tamu';
    }
}

if (! function_exists('halaman_awal')) {
    /** Halaman tujuan setelah login sesuai role. */
    function halaman_awal(?string $role): string
    {
        return in_array($role, ['admin', 'gudang'], true) ? url_to('panel') : url_to('beranda');
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|string|null $angka): string
    {
        return 'Rp' . number_format((int) $angka, 0, ',', '.');
    }
}

if (! function_exists('tanggal')) {
    /** 2026-11-10 14:05:00 -> 10 Nov 2026, 14.05 */
    function tanggal(?string $waktu, bool $jam = true): string
    {
        if (! $waktu) {
            return '-';
        }
        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $t     = strtotime($waktu);

        return date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t) . ($jam ? ', ' . date('H.i', $t) : '');
    }
}

if (! function_exists('gambar_produk')) {
    function gambar_produk(?string $gambar): string
    {
        return $gambar ? base_url('uploads/produk/' . $gambar) : base_url('img/tanpa-gambar.svg');
    }
}

if (! function_exists('label_status')) {
    /** Label dan warna Bootstrap untuk status pesanan / pembayaran. */
    function label_status(string $status): array
    {
        return [
            // status pesanan
            'menunggu_bayar'      => ['Menunggu Pembayaran', 'warning'],
            'menunggu_verifikasi' => ['Menunggu Verifikasi', 'info'],
            'diproses'            => ['Diproses', 'primary'],
            'dikirim'             => ['Dikirim', 'indigo'],
            'selesai'             => ['Selesai', 'success'],
            'batal'               => ['Dibatalkan', 'secondary'],
            // status pembayaran
            'belum'    => ['Belum Dibayar', 'warning'],
            'menunggu' => ['Menunggu Verifikasi', 'info'],
            'diterima' => ['Diterima', 'success'],
            'ditolak'  => ['Ditolak', 'danger'],
        ][$status] ?? [ucfirst($status), 'light'];
    }
}

if (! function_exists('badge_status')) {
    function badge_status(string $status): string
    {
        [$label, $warna] = label_status($status);

        return '<span class="badge rounded-pill text-bg-' . $warna . '">' . esc($label) . '</span>';
    }
}

if (! function_exists('jumlah_keranjang')) {
    function jumlah_keranjang(): int
    {
        return user('role') === 'customer' ? model(\App\Models\KeranjangModel::class)->jumlah((int) user('id')) : 0;
    }
}

if (! function_exists('menunggu_verifikasi')) {
    /** Jumlah bukti bayar yang belum diperiksa, untuk badge di sidebar admin. */
    function menunggu_verifikasi(): int
    {
        return model(\App\Models\PembayaranModel::class)->where('status', 'menunggu')->countAllResults();
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

if (! function_exists('menu_aktif')) {
    /** ' active' jika path saat ini diawali $awalan. */
    function menu_aktif(string $awalan, bool $persis = false): string
    {
        $path = '/' . trim(service('request')->getUri()->getPath(), '/');

        return ($persis ? $path === $awalan : str_starts_with($path, $awalan)) ? ' active' : '';
    }
}

if (! function_exists('input_sandi')) {
    /**
     * Input kata sandi dengan tombol intip. saveData dimatikan supaya $meter dan $bantuan
     * dari pemanggilan sebelumnya tidak terbawa ke input berikutnya.
     */
    function input_sandi(string $nama, string $label, string $auto, bool $meter = false, ?string $bantuan = null): string
    {
        return view('komponen/input_sandi', compact('nama', 'label', 'auto', 'meter', 'bantuan'), ['saveData' => false]);
    }
}

if (! function_exists('toko')) {
    /** Nilai pengaturan toko, misalnya toko('ongkir'). Tanpa argumen: model pengaturannya. */
    function toko(?string $kunci = null): mixed
    {
        $model = model(\App\Models\PengaturanModel::class);

        return $kunci === null ? $model : $model->nilai($kunci);
    }
}

if (! function_exists('info_ongkir')) {
    /** Kalimat ongkir sesuai pengaturan, misalnya "Ongkir Rp15.000, gratis mulai Rp300.000". */
    function info_ongkir(): string
    {
        $ongkir = (int) toko('ongkir');
        $gratis = (int) toko('gratis_ongkir');
        $teks   = $ongkir ? 'Ongkir ' . rupiah($ongkir) : 'Gratis ongkir';

        return $ongkir && $gratis ? "{$teks}, gratis mulai " . rupiah($gratis) : $teks;
    }
}
