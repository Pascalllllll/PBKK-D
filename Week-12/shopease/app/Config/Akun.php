<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/** Pengaturan autentikasi ShopEase. Angka-angka keamanan dikumpulkan di sini, bukan tersebar di controller. */
class Akun extends BaseConfig
{
    /** Percobaan masuk per pasangan (IP, email) dalam satu menit. */
    public int $batasPercobaan = 5;

    /** Percobaan masuk per IP dalam satu menit, untuk menahan tebakan ke banyak email sekaligus. */
    public int $batasPerIp = 20;

    /** Lama cookie "Ingat saya". */
    public int $hariIngat = 30;

    public string $cookieIngat = 'shopease_ingat';

    /** Label tiap role untuk tampilan. */
    public array $role = [
        'customer' => 'Pelanggan',
        'gudang'   => 'Staf Gudang',
        'admin'    => 'Admin',
    ];
}
