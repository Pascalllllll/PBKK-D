<?php

use App\Models\KrsModel;

if (! function_exists('user')) {
    /** Data user yang sedang login (atau satu kolomnya). */
    function user(?string $key = null): mixed
    {
        $user = session('user') ?? [];

        return $key === null ? $user : ($user[$key] ?? null);
    }
}

if (! function_exists('bobot_nilai')) {
    function bobot_nilai(?string $nilai): ?float
    {
        return [
            'A' => 4.0, 'AB' => 3.5, 'B' => 3.0, 'BC' => 2.5, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0,
        ][$nilai] ?? null;
    }
}

if (! function_exists('hitung_ip')) {
    /**
     * Menghitung IP dari baris KRS (kolom sks dan nilai).
     * Matakuliah yang belum dinilai tidak ikut dihitung.
     *
     * @return array{sks: int, ip: float}
     */
    function hitung_ip(array $rows): array
    {
        $sks = 0;
        $mutu = 0.0;

        foreach ($rows as $row) {
            $bobot = bobot_nilai($row['nilai'] ?? null);
            if ($bobot === null) {
                continue;
            }
            $sks += (int) $row['sks'];
            $mutu += $bobot * $row['sks'];
        }

        return ['sks' => $sks, 'ip' => $sks > 0 ? round($mutu / $sks, 2) : 0.0];
    }
}

if (! function_exists('foto_mahasiswa')) {
    function foto_mahasiswa(?string $foto): string
    {
        return $foto ? base_url('uploads/foto/' . $foto) : base_url('img/avatar.svg');
    }
}

if (! function_exists('tahun_ajaran')) {
    function tahun_ajaran(): string
    {
        return KrsModel::TAHUN_AJARAN;
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
