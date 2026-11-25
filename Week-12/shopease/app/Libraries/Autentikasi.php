<?php

namespace App\Libraries;

use App\Models\LogMasukModel;
use App\Models\TokenIngatModel;
use App\Models\UserModel;
use Config\Akun;

/**
 * Semua urusan "siapa yang sedang login" ada di sini: masuk, keluar, cookie "Ingat saya",
 * dan pengecekan ulang akun ke database. Controller dan filter cukup memanggil service('autentikasi').
 */
class Autentikasi
{
    private ?array $user = null;
    private bool $sudahDicek = false;

    public function __construct(private Akun $config, private UserModel $users)
    {
    }

    /**
     * Baris user yang sedang login, atau null. Dicek sekali per request:
     * session dulu, lalu cookie "Ingat saya" bila session kosong.
     */
    public function user(): ?array
    {
        if (! $this->sudahDicek) {
            $this->sudahDicek = true;
            $this->user       = $this->dariSession() ?? $this->dariCookie();
        }

        return $this->user;
    }

    /**
     * Proses form masuk. Mengembalikan null bila berhasil, atau pesan error untuk ditampilkan.
     */
    public function coba(string $email, string $password, bool $ingat): ?string
    {
        $email = strtolower(trim($email));

        if ($tunggu = $this->terbatas($email)) {
            $this->catat($email, 'dibatasi');

            return "Terlalu banyak percobaan masuk. Coba lagi dalam {$tunggu} detik.";
        }

        $user = $this->users->periksa($email, $password);

        if ($user === null) {
            $this->catat($email, 'gagal', $this->users->cariEmail($email)['id'] ?? null);

            // Pesan sama untuk email tidak terdaftar dan sandi salah
            return 'Email atau kata sandi salah.';
        }

        if (! $user['aktif']) {
            $this->catat($email, 'dibekukan', (int) $user['id']);

            return 'Akun ini sedang dibekukan. Hubungi admin ShopEase.';
        }

        $this->mulai($user, $ingat);

        return null;
    }

    /**
     * Mengisi session untuk $user. $hasil null berarti bukan login baru (misalnya setelah ganti sandi),
     * jadi tidak dicatat di log dan login_terakhir tidak berubah.
     */
    public function mulai(array $user, bool $ingat = false, ?string $hasil = 'berhasil'): void
    {
        // ID session diganti untuk mencegah session fixation
        session()->regenerate(true);
        session()->set('user', UserModel::payload($user));

        if ($hasil !== null) {
            $this->users->update($user['id'], ['login_terakhir' => date('Y-m-d H:i:s')]);
            $this->catat($user['email'], $hasil, (int) $user['id']);
        }
        if ($ingat) {
            $this->buatToken((int) $user['id']);
        }

        $this->user       = $user;
        $this->sudahDicek = true;
    }

    public function keluar(): void
    {
        if ($token = $this->cookie()) {
            model(TokenIngatModel::class)->where('selector', $token[0])->delete();
        }
        $this->lupakanCookie();
        $this->hapusSesi();
    }

    /** Semua session dan token user dicabut, lalu perangkat ini dimasukkan lagi dengan versi sesi baru. */
    public function cabutPerangkatLain(): void
    {
        $id    = (int) $this->user()['id'];
        $ingat = $this->cookie() !== null;

        $this->users->cabutSemuaSesi($id);
        $this->mulai($this->users->find($id), $ingat, null);
    }

    /** Selector token "Ingat saya" milik perangkat ini, untuk menandai "perangkat ini" di halaman akun. */
    public function selectorIni(): ?string
    {
        return $this->cookie()[0] ?? null;
    }

    public function catat(string $email, string $hasil, ?int $userId = null): void
    {
        model(LogMasukModel::class)->insert([
            'user_id'   => $userId,
            'email'     => mb_substr($email, 0, 100),
            'hasil'     => $hasil,
            'ip'        => service('request')->getIPAddress(),
            'perangkat' => $this->perangkat(),
        ]);
    }

    /** Nama perangkat yang mudah dibaca, misalnya "Chrome 131 di Linux". */
    public function perangkat(): string
    {
        $agent = service('request')->getUserAgent();

        return $agent->getBrowser() !== ''
            ? mb_substr($agent->getBrowser() . ' ' . strtok($agent->getVersion(), '.') . ' di ' . $agent->getPlatform(), 0, 120)
            : (mb_substr((string) $agent, 0, 120) ?: 'Tidak dikenal');
    }

    // ------------------------------------------------------------------

    private function dariSession(): ?array
    {
        $sesi = session('user');
        if (! $sesi) {
            return null;
        }

        // Selalu dibaca ulang: admin bisa membekukan akun atau mengubah role kapan saja
        $user   = $this->users->find($sesi['id']);
        $alasan = match (true) {
            $user === null                                   => 'Akun Anda sudah tidak ada.',
            ! $user['aktif']                                 => 'Akun Anda dibekukan admin. Hubungi admin ShopEase.',
            (int) $user['versi_sesi'] !== (int) $sesi['versi'] => 'Sesi Anda diakhiri karena kata sandi diganti atau akun dikeluarkan dari semua perangkat. Silakan masuk lagi.',
            default                                          => null,
        };

        if ($alasan !== null) {
            $this->hapusSesi();
            session()->setFlashdata('warning', $alasan);

            return null;
        }

        session()->set('user', UserModel::payload($user));

        return $user;
    }

    private function dariCookie(): ?array
    {
        if (! $token = $this->cookie()) {
            return null;
        }

        [$selector, $validator] = $token;
        $tokens = model(TokenIngatModel::class);
        $baris  = $tokens->berlaku($selector);

        if ($baris === null || ! hash_equals($baris['validator_hash'], hash('sha256', $validator))) {
            $this->lupakanCookie();

            return null;
        }

        // Token sekali pakai: diganti token baru setiap kali dipakai untuk login otomatis
        $tokens->delete($baris['id']);
        $user = $this->users->find($baris['user_id']);

        if ($user === null || ! $user['aktif']) {
            $this->lupakanCookie();

            return null;
        }

        $this->mulai($user, true, 'ingat');

        return $user;
    }

    private function buatToken(int $userId): void
    {
        $selector  = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(32));
        $detik     = $this->config->hariIngat * DAY;

        model(TokenIngatModel::class)->insert([
            'user_id'        => $userId,
            'selector'       => $selector,
            'validator_hash' => hash('sha256', $validator),
            'perangkat'      => $this->perangkat(),
            'kedaluwarsa'    => date('Y-m-d H:i:s', time() + $detik),
        ]);

        service('response')->setCookie($this->config->cookieIngat, "{$selector}:{$validator}", $detik, httponly: true, samesite: 'Lax');
    }

    /** [selector, validator] dari cookie, atau null bila tidak ada / formatnya salah. */
    private function cookie(): ?array
    {
        $isi = (string) service('request')->getCookie($this->config->cookieIngat);

        return preg_match('/^([a-f0-9]{24}):([a-f0-9]{64})$/', $isi, $m) ? [$m[1], $m[2]] : null;
    }

    private function lupakanCookie(): void
    {
        if (service('request')->getCookie($this->config->cookieIngat) !== null) {
            service('response')->deleteCookie($this->config->cookieIngat);
        }
    }

    private function hapusSesi(): void
    {
        session()->remove('user');
        session()->regenerate(true);
        $this->user = null;
    }

    /** Detik yang harus ditunggu bila batas percobaan habis, null bila masih boleh mencoba. */
    private function terbatas(string $email): ?int
    {
        $throttler = service('throttler');
        $ip        = service('request')->getIPAddress();
        $ember     = [
            'masuk-ip-' . md5($ip)         => $this->config->batasPerIp,
            'masuk-' . md5("{$ip}|{$email}") => $this->config->batasPercobaan,
        ];

        foreach ($ember as $kunci => $batas) {
            if ($throttler->check($kunci, $batas, MINUTE) === false) {
                return max(1, $throttler->getTokenTime());
            }
        }

        return null;
    }
}
