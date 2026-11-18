# Week 11 - Sistem Autentikasi CodeIgniter 4: ShopEase

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  
**Kelas**: PBKK D

| | |
| :-- | :-- |
| Kode | folder [`shopease/`](./shopease) (CodeIgniter 4.7.6, PHP 8.3), lanjutan dari [Week 10](../Week-10/README.md) |
| Menjalankan | [`run.sh`](./run.sh) (Docker: PHP 8.3 + Apache, MariaDB 11) |
| Tampilan | Bootstrap 5.3, Bootstrap Icons (dari CDN jsDelivr) |

## Soal: Authentication System

Tugas minggu ini adalah membuat sistem autentikasi: registrasi, login, logout, dan pembatasan halaman berdasarkan role. ShopEase dari Week 10 sudah punya login sederhana, jadi minggu ini bagian itu dibongkar dan dibuat ulang dengan lebih lengkap:

| No | Kebutuhan | Penerapan di ShopEase |
| :-: | :-- | :-- |
| 1 | Registrasi | `/daftar`: nama, email unik, nomor HP Indonesia, sandi 8-72 karakter berisi huruf dan angka, konfirmasi sandi, centang persetujuan. Role selalu `customer` |
| 2 | Login | `/masuk`: sandi dicek dengan `password_verify` terhadap hash bcrypt, pesan error sama untuk email tidak terdaftar dan sandi salah, batas 5 percobaan per menit |
| 3 | Ingat saya | Cookie 30 hari berisi token acak. Database hanya menyimpan hash-nya, token diganti setiap kali dipakai |
| 4 | Logout | Hanya lewat POST dengan token CSRF. Session diganti dan token "Ingat saya" perangkat itu dihapus |
| 5 | Hak akses per role | Filter `auth`, `auth:customer`, `auth:admin,gudang`, `auth:admin`. Role yang salah mendapat halaman 403 |
| 6 | Akun saya | `/akun` untuk semua role: ubah profil, ganti sandi, daftar perangkat yang diingat, riwayat masuk |
| 7 | Kelola pengguna | `/panel/pengguna` (admin): cari, filter, bekukan/aktifkan akun, ubah role, keluarkan dari semua perangkat |
| 8 | Riwayat masuk | `/panel/log-masuk` (admin): setiap percobaan masuk dicatat, termasuk yang gagal dan yang ditahan |

## Pendahuluan

Autentikasi menjawab pertanyaan "siapa yang membuka halaman ini", sedangkan otorisasi menjawab "apakah dia boleh membukanya". Di CodeIgniter 4 keduanya bisa dipisah dengan rapi:

- **Model** menyimpan user dan mengubah sandi menjadi hash sebelum disimpan.
- **Library** `Autentikasi` mengurus siapa yang sedang login: session, cookie "Ingat saya", dan pengecekan ulang ke database.
- **Filter** menjaga route sebelum controller dijalankan. Controller tidak perlu memeriksa login atau role sama sekali.
- **Session** di server menyimpan data login, dan setiap form dilindungi token CSRF.

Di Week 10, data login hanya dibaca dari session. Akibatnya, kalau admin mengubah role seseorang atau user itu mengganti sandi, session yang sudah ada tetap berlaku sampai kedaluwarsa. Week 11 menutup celah itu. Perbandingannya:

| | Week 10 | Week 11 |
| :-- | :-- | :-- |
| Isi session | `logged_in`, `role`, `user` (tiga kunci) | Satu kunci `user` berisi id, nama, email, role, versi sesi |
| Pengecekan login | Hanya membaca session | Filter global `identitas` membaca ulang user dari database di setiap request |
| Akun dibekukan / role diubah | Baru berlaku setelah user logout | Berlaku pada request berikutnya |
| Ganti sandi | Tidak ada | Wajib sandi lama, session lain dan token "Ingat saya" dicabut |
| Tebak sandi beruntun | Tidak dibatasi | 5 percobaan per menit per (IP, email) dan 20 per menit per IP |
| Email tidak terdaftar | Waktu respons lebih cepat daripada sandi salah | `password_verify` tetap dijalankan dengan hash pengganti, waktunya sama |
| Logout | `session()->destroy()`, tanpa pesan | Session diganti, token perangkat dihapus, ada pesan "Anda sudah keluar" |
| Kelola akun | Tidak ada | Panel pengguna dan riwayat masuk untuk admin |
| Halaman privat | Tidak ada header cache khusus | Header `Cache-Control: no-store`, tidak bisa dibuka lagi dari cache browser setelah logout |

Referensi dari teman memakai tiga filter (`pintu`, `tamu`, `peran`) dan mengecek database langsung di dalam filter. Di ShopEase pengecekan itu dipindah ke satu library yang dipanggil sekali per request, sehingga filter, controller, dan view memakai hasil yang sama tanpa query berulang. Fitur yang tidak ada di referensi: "Ingat saya", versi sesi untuk mengeluarkan perangkat lain, ubah role dari panel, riwayat masuk, dan pembatas per IP selain per email.

## Struktur Project

Hanya file yang baru atau berubah dari Week 10 yang ditandai:

```
Week-11/
├── README.md
├── run.sh                       # port 8091, container pbkk11-app dan pbkk11-db
├── docker/                      # sama dengan Week 10
├── img/                         # screenshot hasil
└── shopease/app/
    ├── Config/
    │   ├── Akun.php             # BARU: batas percobaan, lama "Ingat saya", label role
    │   ├── Services.php         # service('autentikasi')
    │   ├── Filters.php          # alias identitas, dipasang global sebelum dan sesudah request
    │   └── Routes.php           # group akun, panel/pengguna, panel/log-masuk
    ├── Libraries/
    │   └── Autentikasi.php      # BARU: masuk, keluar, cookie "Ingat saya", cek ulang akun, pembatas
    ├── Filters/
    │   ├── IdentitasFilter.php  # BARU: global, memastikan session masih sah
    │   ├── AuthFilter.php       # wajib login + cek role, 403
    │   └── GuestFilter.php      # halaman masuk/daftar hanya untuk tamu
    ├── Controllers/
    │   ├── Auth.php             # daftar, masuk, keluar (lebih tipis)
    │   ├── Akun.php             # BARU: profil, ganti sandi, perangkat
    │   └── Panel/Pengguna.php   # BARU: kelola pengguna dan riwayat masuk
    ├── Models/
    │   ├── UserModel.php        # aturan sandi, periksa(), cabutSemuaSesi(), daftar()
    │   ├── TokenIngatModel.php  # BARU
    │   └── LogMasukModel.php    # BARU
    ├── Database/
    │   ├── Migrations/2026-11-17-000001_UpgradeAutentikasi.php   # BARU
    │   └── Seeds/ShopEaseSeeder.php                             # + akun beku
    ├── Helpers/app_helper.php   # sudah_masuk(), label_role(), input_sandi()
    └── Views/
        ├── auth/                # masuk (Ingat saya), daftar (indikator sandi)
        ├── akun/index.php       # BARU
        ├── panel/pengguna/      # BARU: index.php, log.php
        └── komponen/            # BARU: input_sandi.php, tabel_log.php
```

Alasan pembagiannya:

- **Satu library untuk status login.** Filter `auth`, filter `tamu`, controller `Auth` dan `Akun` semuanya perlu tahu siapa yang login. Kalau masing-masing membaca session dan database sendiri, aturan "akun beku dikeluarkan" harus ditulis di beberapa tempat. Dengan `service('autentikasi')` aturannya ada di satu file, dan karena service-nya shared, database hanya dibaca sekali per request.
- **Angka keamanan di `Config/Akun.php`.** Batas percobaan dan lama "Ingat saya" bisa diubah tanpa membuka controller.
- **Migration baru, bukan mengubah yang lama.** Tabel `users` sudah ada sejak Week 10. Kolom baru ditambahkan dengan `addColumn` di migration terpisah, seperti yang dilakukan pada aplikasi yang sudah berjalan.

## Bagian 1: Database

### 1. Migration Lanjutan

Tiga kolom ditambahkan ke `users`, dan dua tabel baru dibuat:

```
users  1 ── N token_ingat   (CASCADE: user dihapus, tokennya ikut hilang)
users  1 ── N log_masuk     (SET NULL: log tetap ada walaupun user dihapus)
```

```php
// app/Database/Migrations/2026-11-17-000001_UpgradeAutentikasi.php
$this->forge->addColumn('users', [
    'aktif'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'role'],
    // Dinaikkan saat sandi diganti atau admin mengeluarkan akun: semua session lama jadi tidak berlaku
    'versi_sesi'     => ['type' => 'INT', 'unsigned' => true, 'default' => 1, 'after' => 'aktif'],
    'login_terakhir' => ['type' => 'DATETIME', 'null' => true, 'after' => 'versi_sesi'],
]);

// Token "Ingat saya". Cookie berisi selector:validator, database hanya menyimpan hash validator.
$this->forge->addField([
    'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
    'user_id'        => ['type' => 'INT', 'unsigned' => true],
    'selector'       => ['type' => 'CHAR', 'constraint' => 24],
    'validator_hash' => ['type' => 'CHAR', 'constraint' => 64],
    'perangkat'      => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
    'kedaluwarsa'    => ['type' => 'DATETIME'],
    // ...
]);
$this->forge->addUniqueKey('selector');
$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
$this->forge->createTable('token_ingat');

// log_masuk: user_id (boleh kosong), email, hasil, ip, perangkat, created_at
'hasil' => ['type' => 'ENUM', 'constraint' => ['berhasil', 'ingat', 'gagal', 'dibekukan', 'dibatasi']],
```

`versi_sesi` adalah inti dari fitur "keluarkan dari semua perangkat". Nilainya ikut disimpan di session saat login. Begitu angka di database dinaikkan, semua session dengan angka lama tidak cocok lagi dan langsung ditolak.

### 2. Menjalankan

```bash
cd Week-11
./run.sh up       # build, composer install, migrate --all, seed
./run.sh reset    # hapus database dan upload, lalu up dari awal
```

```
Menjalankan semua migrasi baru...
	Berjalan: (App) 2026-11-10-000001_App\Database\Migrations\CreateShopEaseTables
	Berjalan: (App) 2026-11-17-000001_App\Database\Migrations\UpgradeAutentikasi
Migrasi selesai.
Seeded: App\Database\Seeds\ShopEaseSeeder

ShopEase -> http://localhost:8091
```

Container Week 11 memakai port 8091 (database 3311), jadi bisa berjalan bersamaan dengan Week 10 di port 8090.

Akun untuk mencoba:

| Role | Email | Sandi | Status |
| :-- | :-- | :-- | :-- |
| Admin | `admin@shopease.test` | `admin123` | aktif |
| Staf gudang | `gudang@shopease.test` | `gudang123` | aktif |
| Pelanggan | `budi@mail.test`, `sari@mail.test`, `andi@mail.test` | `budi1234`, `sari1234`, `andi1234` | aktif |
| Pelanggan | `dewi@mail.test` | `dewi1234` | dibekukan |

## Bagian 2: Model

### 1. Hash Otomatis

Controller cukup mengirim `['password' => 'sandi polos']`. Callback `beforeInsert` dan `beforeUpdate` menggantinya dengan hash bcrypt sebelum query dijalankan, jadi tidak mungkin ada controller yang lupa melakukan hash. Kalau field `password` kosong (misalnya saat mengubah profil), field itu dibuang supaya hash lama tidak tertimpa string kosong.

```php
protected function hashPassword(array $data): array
{
    if (! empty($data['data']['password'])) {
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    } else {
        unset($data['data']['password']);
    }

    return $data;
}
```

Aturan sandi ditulis sekali sebagai konstanta dan dipakai di registrasi maupun ganti sandi:

```php
/** Huruf dan angka, 8-72 karakter. bcrypt hanya membaca 72 byte pertama, jadi sisanya ditolak. */
public const ATURAN_SANDI = 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]';
```

Aturan email memakai placeholder `{id}`, sehingga aturan yang sama berlaku untuk registrasi (email harus belum dipakai siapa pun) dan ubah profil (email boleh sama dengan email miliknya sendiri):

```php
'email' => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[100]|is_unique[users.email,id,{id}]'],
```

### 2. Memeriksa Sandi

`periksa()` tetap menjalankan `password_verify` walaupun email tidak ditemukan, memakai hash pengganti. Tanpa ini, login dengan email yang tidak terdaftar selesai lebih cepat (tidak ada bcrypt), dan perbedaan waktu itu bisa dipakai untuk menebak email mana yang terdaftar. Hasil tiga kali pengukuran dengan `curl`: email tidak terdaftar 0,057 / 0,056 / 0,053 detik, sandi salah 0,119 / 0,055 / 0,056 detik.

```php
public function periksa(string $email, string $password): ?array
{
    $user = $this->cariEmail($email);

    if (! password_verify($password, $user['password'] ?? self::HASH_PALSU) || $user === null) {
        return null;
    }

    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        $this->update($user['id'], ['password' => $password]);
    }

    return $user;
}
```

`password_needs_rehash` membuat hash lama otomatis diperbarui saat PHP mengganti algoritma bawaan, tanpa meminta user mengganti sandi.

### 3. Mencabut Semua Sesi

```php
public function cabutSemuaSesi(int $id): void
{
    $this->builder()->set('versi_sesi', 'versi_sesi + 1', false)->where('id', $id)->update();
    model(TokenIngatModel::class)->where('user_id', $id)->delete();
}
```

Fungsi ini dipakai di tiga tempat: ganti sandi, admin membekukan akun, dan tombol "Keluarkan dari semua perangkat".

## Bagian 3: Library `Autentikasi`

### 1. Siapa yang Sedang Login

`user()` hanya bekerja sekali per request. Session diperiksa dulu, lalu cookie "Ingat saya" bila session kosong:

```php
public function user(): ?array
{
    if (! $this->sudahDicek) {
        $this->sudahDicek = true;
        $this->user       = $this->dariSession() ?? $this->dariCookie();
    }

    return $this->user;
}

private function dariSession(): ?array
{
    $sesi = session('user');
    if (! $sesi) {
        return null;
    }

    // Selalu dibaca ulang: admin bisa membekukan akun atau mengubah role kapan saja
    $user   = $this->users->find($sesi['id']);
    $alasan = match (true) {
        $user === null                                     => 'Akun Anda sudah tidak ada.',
        ! $user['aktif']                                   => 'Akun Anda dibekukan admin. Hubungi admin ShopEase.',
        (int) $user['versi_sesi'] !== (int) $sesi['versi'] => 'Sesi Anda diakhiri karena kata sandi diganti atau akun dikeluarkan dari semua perangkat. Silakan masuk lagi.',
        default                                            => null,
    };

    if ($alasan !== null) {
        $this->hapusSesi();
        session()->setFlashdata('warning', $alasan);

        return null;
    }

    session()->set('user', UserModel::payload($user));   // nama dan role terbaru

    return $user;
}
```

### 2. Login

Controller hanya memanggil `coba()`. Hasilnya `null` bila berhasil, atau pesan error:

```php
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

public function mulai(array $user, bool $ingat = false, ?string $hasil = 'berhasil'): void
{
    // ID session diganti untuk mencegah session fixation
    session()->regenerate(true);
    session()->set('user', UserModel::payload($user));
    // ... login_terakhir, catat log, buat token bila $ingat
}
```

Urutan ini disengaja. Pembatas dicek paling awal supaya percobaan yang ditahan tidak sempat menjalankan bcrypt. Status beku baru dicek setelah sandi terbukti benar, jadi orang yang tidak tahu sandinya tidak bisa mengetahui bahwa akun itu dibekukan.

### 3. Pembatas Percobaan

Throttler bawaan CodeIgniter memakai token bucket yang disimpan di cache. Ada dua "ember": per pasangan IP dan email (5 per menit, menahan tebakan ke satu akun) dan per IP (20 per menit, menahan tebakan ke banyak akun sekaligus).

```php
private function terbatas(string $email): ?int
{
    $throttler = service('throttler');
    $ip        = service('request')->getIPAddress();
    $ember     = [
        'masuk-ip-' . md5($ip)           => $this->config->batasPerIp,
        'masuk-' . md5("{$ip}|{$email}") => $this->config->batasPercobaan,
    ];

    foreach ($ember as $kunci => $batas) {
        if ($throttler->check($kunci, $batas, MINUTE) === false) {
            return max(1, $throttler->getTokenTime());
        }
    }

    return null;
}
```

Kunci di-`md5` karena cache CodeIgniter tidak menerima karakter `@` dan `:` yang ada di email dan alamat IPv6.

### 4. Ingat Saya

Cookie berisi `selector:validator`. `selector` dipakai untuk mencari baris di database, sedangkan `validator` dibandingkan dengan hash SHA-256 yang tersimpan. Jika tabel `token_ingat` bocor, isinya tidak bisa dipakai sebagai cookie karena yang tersimpan hanya hash.

```php
private function buatToken(int $userId): void
{
    $selector  = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $detik     = $this->config->hariIngat * DAY;

    model(TokenIngatModel::class)->insert([
        'user_id'        => $userId,
        'selector'       => $selector,
        'validator_hash' => hash('sha256', $validator),
        'perangkat'      => $this->perangkat(),          // "Chrome 157 di Linux"
        'kedaluwarsa'    => date('Y-m-d H:i:s', time() + $detik),
    ]);

    service('response')->setCookie($this->config->cookieIngat, "{$selector}:{$validator}", $detik, httponly: true, samesite: 'Lax');
}

private function dariCookie(): ?array
{
    // ... cari baris berdasarkan selector, lalu:
    if ($baris === null || ! hash_equals($baris['validator_hash'], hash('sha256', $validator))) {
        $this->lupakanCookie();

        return null;
    }

    // Token sekali pakai: diganti token baru setiap kali dipakai untuk login otomatis
    $tokens->delete($baris['id']);
    // ... user harus masih aktif
    $this->mulai($user, true, 'ingat');
}
```

`hash_equals` dipakai sebagai ganti `===` agar waktu perbandingannya tidak bergantung pada berapa karakter yang cocok. Karena token diganti setiap kali dipakai, cookie lama yang sempat disalin orang lain tidak bisa dipakai lagi setelah pemiliknya membuka ShopEase.

## Bagian 4: Filter dan Routing

### 1. Tiga Filter

| Alias | Kelas | Dipasang di | Tugas |
| :-- | :-- | :-- | :-- |
| `identitas` | `IdentitasFilter` | Global, sebelum dan sesudah request | Sebelum: memanggil `service('autentikasi')->user()`. Sesudah: memindahkan cookie ke response dan memasang `no-store` |
| `auth` | `AuthFilter` | Route group | Wajib login, dan role harus cocok bila diberi argumen |
| `tamu` | `GuestFilter` | `/masuk`, `/daftar` | User yang sudah login diarahkan ke halaman awalnya |

```php
// app/Config/Filters.php
public array $globals = [
    'before' => [
        'csrf',
        'identitas', // cek ulang session / login dari cookie "Ingat saya"
    ],
    'after' => [
        'identitas',
    ],
];
```

`identitas` dipasang global, bukan hanya di route yang butuh login, karena halaman publik juga perlu tahu siapa yang login. Tanpa itu, pengunjung dengan cookie "Ingat saya" akan melihat tombol "Masuk" di beranda walaupun sebenarnya bisa login otomatis.

Bagian `after` mengatasi satu jebakan di CodeIgniter. Cookie dipasang library ke `service('response')`, tetapi controller yang mengembalikan `redirect()` memakai objek response yang berbeda, sehingga cookie itu ikut hilang. Filter memindahkannya:

```php
public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
{
    if ($response !== service('response')) {
        foreach (service('response')->getCookies() as $cookie) {
            $response->setCookie($cookie);
        }
    }

    // Halaman yang dibuka saat login tidak boleh disimpan browser,
    // supaya tombol Back setelah keluar tidak menampilkan data akun.
    if (session('user') !== null && ! str_starts_with($response->getHeaderLine('Content-Type'), 'image/')) {
        $response->setHeader('Cache-Control', 'no-store, private');
    }
}
```

`AuthFilter` sekarang menyimpan URL tujuan bersama role yang boleh membukanya. Jadi kalau staf gudang login setelah membuka `/keranjang`, ia diarahkan ke `/panel`, bukan ke halaman yang akan memberinya 403:

```php
if ($user === null) {
    if ($request->getMethod() === 'GET') {
        session()->setTempdata('tujuan', ['url' => current_url(true)->__toString(), 'role' => (array) $arguments], 300);
    }
    $redirect = redirect()->route('masuk');

    // Jika baru saja dikeluarkan (akun dibekukan / sandi diganti), alasan itu yang ditampilkan
    return session('warning') ? $redirect : $redirect->with('info', 'Silakan masuk terlebih dahulu untuk melanjutkan.');
}
```

### 2. Route Baru

```php
// ---------- Akun (semua role yang sudah login) ----------
$routes->group('akun', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Akun::index', ['as' => 'akun']);
    $routes->post('profil', 'Akun::profil', ['as' => 'akun.profil']);
    $routes->post('sandi', 'Akun::sandi', ['as' => 'akun.sandi']);
    $routes->post('perangkat/(:num)/lupakan', 'Akun::lupakanPerangkat/$1', ['as' => 'akun.perangkat.lupakan']);
    $routes->post('keluar-semua', 'Akun::keluarSemua', ['as' => 'akun.keluar_semua']);
});

// di dalam group panel dengan filter auth:admin
$routes->get('pengguna', 'Pengguna::index', ['as' => 'panel.pengguna']);
$routes->post('pengguna/(:num)/status', 'Pengguna::status/$1', ['as' => 'panel.pengguna.status']);
$routes->post('pengguna/(:num)/role', 'Pengguna::role/$1', ['as' => 'panel.pengguna.role']);
$routes->post('pengguna/(:num)/cabut', 'Pengguna::cabut/$1', ['as' => 'panel.pengguna.cabut']);
$routes->get('log-masuk', 'Pengguna::log', ['as' => 'panel.log']);
```

Hasil `php spark routes` untuk route autentikasi:

| Method | Route | Handler | Filter sebelum |
| :-- | :-- | :-- | :-- |
| GET, POST | `masuk` | `Auth::masuk`, `Auth::prosesMasuk` | `csrf identitas tamu` |
| GET, POST | `daftar` | `Auth::daftar`, `Auth::prosesDaftar` | `csrf identitas tamu` |
| POST | `keluar` | `Auth::keluar` | `csrf identitas auth` |
| GET | `akun` | `Akun::index` | `csrf identitas auth` |
| POST | `akun/profil`, `akun/sandi`, `akun/keluar-semua`, `akun/perangkat/([0-9]+)/lupakan` | `Akun::*` | `csrf identitas auth` |
| GET | `panel/pengguna`, `panel/log-masuk` | `Panel\Pengguna::index`, `::log` | `csrf identitas auth:admin` |
| POST | `panel/pengguna/([0-9]+)/status`, `/role`, `/cabut` | `Panel\Pengguna::*` | `csrf identitas auth:admin` |

## Bagian 5: Controller

### 1. `Auth`

Setelah logika login pindah ke library, controller tinggal mengurus input dan arah redirect. Email dirapikan sebelum divalidasi, jadi ` Budi@Mail.Test ` tetap dikenali:

```php
public function prosesMasuk()
{
    // Email dirapikan dulu, jadi " Budi@Mail.Test " tetap dikenali
    $input = [
        'email'    => strtolower(trim((string) $this->request->getPost('email'))),
        'password' => (string) $this->request->getPost('password'),
    ];
    if (! $this->validateData($input, $rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $auth  = service('autentikasi');
    $gagal = $auth->coba($input['email'], $input['password'], $this->request->getPost('ingat') === '1');

    if ($gagal !== null) {
        return redirect()->back()->withInput()->with('error', $gagal);
    }

    $user = $auth->user();

    // Kembali ke halaman yang tadi diminta, asalkan role-nya memang boleh membuka halaman itu
    $tujuan = session()->getTempdata('tujuan');
    session()->removeTempdata('tujuan');
    $boleh = $tujuan && ($tujuan['role'] === [] || in_array($user['role'], $tujuan['role'], true));

    return redirect()->to($boleh ? $tujuan['url'] : halaman_awal($user['role']))->with('success', 'Halo, ' . $user['nama'] . '!');
}

public function keluar()
{
    service('autentikasi')->keluar();

    return redirect()->to(url_to('masuk'))->with('success', 'Anda sudah keluar. Sampai jumpa lagi!');
}
```

Pada registrasi, `role` ditulis langsung `'customer'` dan tidak pernah dibaca dari form. Mengirim `role=admin` lewat request buatan tetap menghasilkan akun pelanggan.

### 2. `Akun`

Ganti sandi mewajibkan sandi lama, menolak sandi baru yang sama dengan sandi lama, lalu mencabut semua sesi lain. Perangkat yang sedang dipakai dimasukkan lagi dengan versi sesi yang baru, jadi pengguna tidak ikut keluar:

```php
public function sandi()
{
    $rules = [
        'sandi_lama'   => ['label' => 'Kata sandi lama', 'rules' => 'required'],
        'sandi_baru'   => ['label' => 'Kata sandi baru', 'rules' => UserModel::ATURAN_SANDI . '|differs[sandi_lama]'],
        'ulangi_sandi' => ['label' => 'Ulangi kata sandi baru', 'rules' => 'required|matches[sandi_baru]'],
    ];
    // ...
    if (! $this->users->cocokSandi($id, (string) $this->request->getPost('sandi_lama'))) {
        return $this->gagal(['sandi_lama' => 'Kata sandi lama salah.'], '#sandi');
    }

    $this->users->update($id, ['password' => $this->request->getPost('sandi_baru')]);
    service('autentikasi')->cabutPerangkatLain();

    return redirect()->to(url_to('akun'))->with('success', 'Kata sandi diganti. Perangkat lain yang masih login sudah dikeluarkan.');
}
```

Mengubah email juga meminta sandi, karena email dipakai untuk login. Di tampilan, kolom sandi baru muncul setelah isi kolom email diubah.

### 3. `Panel\Pengguna`

Admin tidak bisa membekukan atau mengubah role akunnya sendiri. Tanpa aturan ini, satu-satunya admin bisa tidak sengaja mengunci dirinya dari panel. Membekukan akun langsung mencabut semua sesinya:

```php
public function status(int $id)
{
    if ($id === user('id')) {
        return $this->kembali('error', 'Akun Anda sendiri tidak bisa dibekukan atau diubah role-nya, supaya admin tidak terkunci dari panel.');
    }
    $akun = $this->cariAtau404($id);

    $aktif = $akun['aktif'] ? 0 : 1;
    $this->model->update($id, ['aktif' => $aktif]);
    if (! $aktif) {
        // Session yang sedang berjalan ikut berakhir pada request berikutnya
        $this->model->cabutSemuaSesi($id);
    }

    return $this->kembali('success', $akun['nama'] . ($aktif ? ' diaktifkan kembali.' : ' dibekukan dan dikeluarkan dari semua perangkat.'));
}
```

Perubahan role tidak perlu mencabut sesi. Karena `identitas` membaca ulang user setiap request, role baru langsung terbaca pada klik berikutnya (Gambar 25).

## Bagian 6: View

| View | Isi |
| :-- | :-- |
| `auth/masuk.php` | Form masuk, centang "Ingat saya selama 30 hari", tabel akun contoh yang bisa dibuka-tutup |
| `auth/daftar.php` | Form registrasi dengan indikator kekuatan sandi |
| `akun/index.php` | Profil, ganti sandi, perangkat yang diingat, riwayat masuk. Memakai layout toko untuk pelanggan dan layout panel untuk admin/gudang |
| `panel/pengguna/index.php` | Ringkasan per role, filter, tabel pengguna dengan aksi |
| `panel/pengguna/log.php` | Riwayat masuk dengan filter hasil |
| `komponen/input_sandi.php` | Input sandi + tombol intip + indikator kekuatan (opsional) |
| `komponen/tabel_log.php` | Tabel log, dipakai di halaman akun dan panel |

Halaman akun memilih layout berdasarkan role, jadi satu view melayani semua pengguna:

```php
<?php $staf = user('role') !== 'customer'; ?>
<?= $this->extend($staf ? 'layouts/panel' : 'layouts/toko') ?>
```

Input sandi dipanggil lewat helper:

```php
<?= input_sandi('sandi_baru', 'Kata sandi baru', 'new-password', true) ?>
```

```php
function input_sandi(string $nama, string $label, string $auto, bool $meter = false, ?string $bantuan = null): string
{
    return view('komponen/input_sandi', compact('nama', 'label', 'auto', 'meter', 'bantuan'), ['saveData' => false]);
}
```

`saveData => false` di sini penting. Secara bawaan, `view()` di CodeIgniter menyimpan data dari pemanggilan sebelumnya. Awalnya komponen ini dipanggil langsung dengan `view()`, dan akibatnya indikator kekuatan serta teks bantuan dari input "Kata sandi" ikut muncul di bawah input "Ulangi kata sandi".

Indikator kekuatan sandi hanya petunjuk di browser. Aturan yang menentukan tetap `ATURAN_SANDI` di server.

## Kendala dan Solusi

| Kendala | Penyebab | Solusi |
| :-- | :-- | :-- |
| Cookie "Ingat saya" tidak terkirim setelah login | `redirect()` membuat objek response baru, cookie yang dipasang di `service('response')` tertinggal | `IdentitasFilter::after()` menyalin cookie ke response yang benar-benar dikirim |
| Login dengan ` BUDI@Mail.Test ` (ada spasi) ditolak "Email tidak valid" | Validasi `valid_email` dijalankan pada input mentah | Email dirapikan dulu, lalu divalidasi dengan `validateData()` |
| Indikator sandi dan teks bantuan muncul di input yang salah | `view()` menyimpan data antar-pemanggilan (`saveData` bawaan `true`) | Komponen dipanggil lewat helper `input_sandi()` dengan `['saveData' => false]` |
| Staf gudang yang login setelah membuka `/keranjang` dibawa ke halaman 403 | URL tujuan disimpan tanpa tahu siapa yang boleh membukanya | Tempdata `tujuan` menyimpan URL dan daftar role; dipakai hanya bila role cocok |
| Uji ubah role untuk Andi gagal login dengan "Terlalu banyak percobaan" | Uji pembatas sebelumnya sudah menghabiskan ember Andi, dan semua request `curl` berasal dari IP yang sama | Bukan bug, pembatasnya bekerja. Di antara skenario, cache dikosongkan dengan `php spark cache:clear` |
| Pesan "Bidang Role harus salah satu dari: customer,gudang,admin." | Pesan bawaan `in_list` | Pesan khusus "Role tidak dikenal." di `$validationMessages` |

## Pembagian Tugas

Tugas ini dikerjakan secara individu:

| Anggota | Area | Pekerjaan |
| :-- | :-- | :-- |
| Hosea (5025241177) | Database | Migration lanjutan (`aktif`, `versi_sesi`, `login_terakhir`, `token_ingat`, `log_masuk`), akun beku di seeder |
| Hosea (5025241177) | Model dan library | Aturan sandi, hash otomatis, hash pengganti, library `Autentikasi`, token "Ingat saya", pembatas |
| Hosea (5025241177) | Filter dan routing | `IdentitasFilter` global, `AuthFilter` dengan tujuan per role, route akun dan pengguna |
| Hosea (5025241177) | Controller dan view | `Auth`, `Akun`, `Panel\Pengguna`, halaman akun, panel pengguna, riwayat masuk |
| Hosea (5025241177) | Pengujian dan dokumentasi | Skenario `curl`, screenshot, laporan |

## Alur Data

Pelanggan yang sudah login membuka `/panel/pengguna`:

```
Browser ── GET /panel/pengguna  (cookie ci_session)
      ▼
Filter global csrf  (GET, lewat)
      ▼
Filter global identitas ─► service('autentikasi')->user()
      │  session('user') ada
      │  UserModel::find(id): aktif = 1, versi_sesi cocok ─► payload session diperbarui
      ▼
Filter auth:admin (AuthFilter)
      │  role 'customer' tidak ada di ['admin']
      ▼
Response 403 + view('errors/terlarang')        ← Panel\Pengguna tidak pernah dijalankan
```

Login dengan "Ingat saya", lalu browser ditutup dan dibuka lagi:

```
POST /masuk (email, password, ingat=1, csrf_test_name)
      ▼
csrf ✓ → identitas (belum login) → tamu ✓ → Auth::prosesMasuk()
      │  validateData ✓
      │  Autentikasi::coba(): terbatas()? tidak → periksa() bcrypt ✓ → aktif ✓
      │  mulai(): regenerate(true), set('user'), login_terakhir, log 'berhasil'
      │  buatToken(): INSERT token_ingat (hash validator), setCookie shopease_ingat
      ▼
redirect /  ── identitas after(): cookie disalin ke RedirectResponse

  ... browser ditutup, cookie ci_session hilang ...

GET /akun  (hanya cookie shopease_ingat)
      ▼
identitas ─► dariSession(): kosong ─► dariCookie()
      │  SELECT token_ingat WHERE selector = ? AND kedaluwarsa > NOW
      │  hash_equals(sha256(validator)) ✓ → DELETE token lama
      │  mulai(user, ingat=true, 'ingat') → token baru + cookie baru
      ▼
auth ✓ → Akun::index() → 200
```

Admin membekukan Sari yang sedang login:

| # | Request | Yang terjadi |
| :-: | :-- | :-- |
| 1 | Sari: `GET /pesanan` | 200, session berisi `versi = 1` |
| 2 | Admin: `POST /panel/pengguna/4/status` | `users.aktif = 0`, `versi_sesi = 2`, token Sari dihapus |
| 3 | Sari: `GET /pesanan` | `identitas`: `aktif = 0` → session dihapus, flash peringatan. `auth`: belum login → redirect `/masuk` |
| 4 | Sari: `GET /masuk` | "Akun Anda dibekukan admin. Hubungi admin ShopEase." |

## Tanggung Jawab Tiap File

| File | Tanggung jawab |
| :-- | :-- |
| `app/Config/Akun.php` | Batas percobaan, lama "Ingat saya", nama cookie, label role |
| `app/Config/Services.php` | Membuat `service('autentikasi')` (shared, satu per request) |
| `app/Config/Filters.php` | Alias `auth`, `tamu`, `identitas`; `csrf` dan `identitas` global |
| `app/Config/Routes.php` | Route tamu, akun, panel pengguna, beserta filternya |
| `app/Libraries/Autentikasi.php` | Status login, login, logout, token "Ingat saya", pembatas, log |
| `app/Filters/IdentitasFilter.php` | Memanggil pengecekan di awal request, menyalin cookie dan memasang `no-store` di akhir |
| `app/Filters/AuthFilter.php` | Wajib login, simpan URL tujuan + role, 403 untuk role yang salah |
| `app/Filters/GuestFilter.php` | Menjauhkan user yang sudah login dari `/masuk` dan `/daftar` |
| `app/Models/UserModel.php` | Validasi, hash otomatis, `periksa()`, `cocokSandi()`, `cabutSemuaSesi()`, daftar dan ringkasan pengguna |
| `app/Models/TokenIngatModel.php` | Token yang masih berlaku, token milik user |
| `app/Models/LogMasukModel.php` | Riwayat masuk dengan nama user dan filter hasil |
| `app/Controllers/Auth.php` | Form dan proses daftar, masuk, keluar |
| `app/Controllers/Akun.php` | Profil, ganti sandi, lupakan perangkat, keluarkan perangkat lain |
| `app/Controllers/Panel/Pengguna.php` | Daftar pengguna, bekukan/aktifkan, ubah role, cabut sesi, riwayat masuk |
| `app/Helpers/app_helper.php` | `user()`, `sudah_masuk()`, `label_role()`, `input_sandi()` |
| `app/Views/auth/*`, `akun/*`, `panel/pengguna/*`, `komponen/*` | Tampilan halaman autentikasi dan akun |
| `app/Database/Migrations/2026-11-17-000001_UpgradeAutentikasi.php` | Kolom baru di `users`, tabel `token_ingat` dan `log_masuk` |

## Pengujian

Skenario berikut diuji dengan `curl` (satu cookie jar per akun, token CSRF diambil dari form) pada database yang baru di-reset. Isi tabel dicek langsung di MariaDB.

| No | Skenario | Hasil |
| :-: | :-- | :-- |
| 1 | Buka `/akun` atau `/panel` tanpa login | Redirect `/masuk`, "Silakan masuk terlebih dahulu untuk melanjutkan." |
| 2 | Login dengan email tidak terdaftar / sandi salah | Pesan sama: "Email atau kata sandi salah." Waktu respons setara (sekitar 0,055 detik) |
| 3 | Login akun beku (`dewi@mail.test`) dengan sandi benar | "Akun ini sedang dibekukan. Hubungi admin ShopEase." |
| 4 | 6 kali sandi salah untuk satu email dalam semenit | Percobaan ke-6: "Terlalu banyak percobaan masuk. Coba lagi dalam 11 detik." Sandi benar pun ditahan. Log: 5 `gagal`, 2 `dibatasi` |
| 5 | Login dengan ` BUDI@Mail.Test ` | Berhasil, diarahkan ke beranda |
| 6 | Pelanggan: `/akun`, `/panel` | 200, 403 |
| 7 | Gudang: `/panel`, `/panel/pengguna`, `/akun` | 200, 403, 200 |
| 8 | Admin: `/panel/pengguna`, `/panel/log-masuk` | 200, 200 |
| 9 | Sudah login lalu membuka `/masuk` / `/daftar` | Redirect ke `/` (pelanggan) / `/panel` (admin) |
| 10 | `GET /keluar`; POST tanpa CSRF; POST dengan CSRF | 404; 403; keluar, `/akun` setelahnya redirect `/masuk` |
| 11 | Header halaman privat | `Cache-Control: no-store, private` |
| 12 | Login dengan "Ingat saya" (1 baris di `token_ingat`), cookie session dihapus, buka `/akun` | 200, token di cookie berganti |
| 13 | Memakai lagi cookie token yang lama | Redirect `/masuk` (token sudah tidak ada) |
| 14 | Logout dari perangkat yang diingat | Baris `token_ingat` milik user menjadi 0 |
| 15 | Admin membekukan Sari yang sedang login | Request Sari berikutnya redirect `/masuk`: "Akun Anda dibekukan admin. Hubungi admin ShopEase." |
| 16 | Admin membekukan akunnya sendiri | Ditolak, `aktif` tetap 1 |
| 17 | Bekukan pengguna dengan id 999 | 404 |
| 18 | Andi (pelanggan, sedang login) diubah menjadi staf gudang | Sebelum: `/panel` 403. Sesudah: `/panel` 200 tanpa login ulang |
| 19 | Ubah role menjadi `superadmin` lewat request buatan | "Role tidak dikenal." |
| 20 | Ganti sandi: sandi lama salah / sama dengan lama / tanpa angka + konfirmasi beda | "Kata sandi lama salah." / "Kata sandi baru harus berbeda dari yang lama." / "Kata sandi baru harus berisi huruf dan angka." + "Ulangi kata sandi baru tidak sama." |
| 21 | Ganti sandi berhasil saat Budi login di dua perangkat | Perangkat ini tetap 200. Perangkat lain redirect `/masuk` dengan pesan "Sesi Anda diakhiri..." walaupun punya cookie "Ingat saya". `versi_sesi` 1 → 2, token 0 |
| 22 | Ganti email tanpa sandi / ke email milik Sari | "Masukkan kata sandi yang benar untuk mengganti email." / "Email ini sudah dipakai akun lain." |
| 23 | Registrasi: nama 2 huruf, email terpakai, sandi tanpa angka, konfirmasi beda, tanpa centang | Lima pesan error di field masing-masing |
| 24 | Registrasi `Rina@Mail.Test` dengan tambahan `role=admin` | Tersimpan `rina@mail.test`, role `customer` |
| 25 | Gudang login setelah membuka `/keranjang`; pelanggan login setelah membuka `/pesanan` | `/panel`; `/pesanan` |

## Kesimpulan

1. Memindahkan semua urusan login ke satu library membuat controller `Auth` jauh lebih pendek, dan aturan seperti "akun beku langsung keluar" cukup ditulis sekali. Filter, controller, dan view memakai hasil yang sama.
2. Membaca ulang user dari database di setiap request membuat perubahan dari admin (bekukan, ubah role) berlaku seketika. Biayanya satu query per request berdasarkan primary key.
3. Kolom `versi_sesi` adalah cara sederhana untuk mengeluarkan semua perangkat tanpa harus menyimpan session di database. Cukup satu angka yang dinaikkan.
4. Hash dilakukan di callback model, sehingga tidak ada jalur yang bisa menyimpan sandi polos, termasuk seeder.
5. Pesan error yang sama, hash pengganti, dan pembatas percobaan bersama-sama mempersulit penyerang menebak email terdaftar maupun sandinya.
6. Token "Ingat saya" yang di-hash dan diganti setiap dipakai membuat cookie curian cepat kedaluwarsa, dan admin atau pemilik akun bisa mencabutnya kapan saja dari halaman akun.

## Hasil (Foto)

### Registrasi

**Gambar 1.** Halaman daftar
![Daftar](./img/w11-02-daftar.png)

**Gambar 2.** Registrasi gagal: semua pesan error tampil di field masing-masing
![Validasi daftar](./img/w11-03-daftar-validasi.png)

**Gambar 3.** Indikator kekuatan sandi dan tombol intip
![Indikator sandi](./img/w11-04-daftar-meter-sandi.png)

**Gambar 4.** Registrasi berhasil, langsung login sebagai pelanggan
![Daftar berhasil](./img/w11-05-daftar-berhasil.png)

**Gambar 5.** Menu akun di navbar: Pesanan Saya, Akun Saya, Keluar
![Menu akun](./img/w11-06-menu-akun.png)

**Gambar 6.** Setelah keluar
![Keluar](./img/w11-07-keluar.png)

### Login

**Gambar 7.** Halaman masuk dengan "Ingat saya" dan daftar akun contoh
![Masuk](./img/w11-01-masuk.png)

**Gambar 8.** Email tidak terdaftar
![Email tidak terdaftar](./img/w11-08-masuk-email-tidak-ada.png)

**Gambar 9.** Sandi salah, pesannya sama dengan Gambar 8
![Sandi salah](./img/w11-08b-masuk-sandi-salah.png)

**Gambar 10.** Akun yang dibekukan
![Akun beku](./img/w11-09-masuk-akun-beku.png)

**Gambar 11.** Percobaan ke-6 dalam semenit ditahan
![Dibatasi](./img/w11-10-masuk-dibatasi.png)

**Gambar 12.** Membuka `/pesanan` tanpa login, diarahkan ke `/masuk`
![Redirect masuk](./img/w11-11-redirect-masuk.png)

**Gambar 13.** Setelah login, kembali ke `/pesanan`
![Kembali ke tujuan](./img/w11-12-kembali-ke-tujuan.png)

### Akun Saya

**Gambar 14.** Cookie session dihapus (seperti browser ditutup), `/akun` tetap terbuka lewat "Ingat saya". Perangkat ditandai "perangkat ini", riwayat mencatat login otomatis
![Akun dengan Ingat saya](./img/w11-13-akun-ingat-saya.png)

**Gambar 15.** Ubah profil: nomor HP salah, dan email diganti tanpa sandi
![Validasi profil](./img/w11-14-profil-validasi.png)

**Gambar 16.** Ganti sandi gagal: sandi baru tanpa angka
![Validasi sandi](./img/w11-15-sandi-validasi.png)

**Gambar 17.** Ganti sandi berhasil
![Sandi diganti](./img/w11-16-sandi-diganti.png)

**Gambar 18.** Perangkat lain milik Budi dikeluarkan setelah sandi diganti
![Perangkat lain keluar](./img/w11-17-perangkat-lain-keluar.png)

**Gambar 19.** Halaman akun untuk admin memakai layout panel
![Akun admin](./img/w11-25-akun-admin.png)

### Kelola Pengguna (Admin)

**Gambar 20.** Daftar pengguna: ringkasan per role, filter, ubah role, keluarkan, bekukan. Baris admin sendiri tidak punya tombol aksi
![Panel pengguna](./img/w11-19-panel-pengguna.png)

**Gambar 21.** Sari dibekukan
![Pengguna dibekukan](./img/w11-20-pengguna-dibekukan.png)

**Gambar 22.** Sari yang sedang login dikeluarkan pada request berikutnya
![Sari dikeluarkan](./img/w11-21-sari-dikeluarkan.png)

**Gambar 23.** Role Andi diubah menjadi Staf Gudang
![Ubah role](./img/w11-22-ubah-role.png)

**Gambar 24.** Filter role Staf Gudang
![Filter role](./img/w11-22b-filter-role.png)

**Gambar 25.** Andi langsung bisa membuka panel tanpa login ulang
![Andi jadi gudang](./img/w11-22c-andi-jadi-gudang.png)

**Gambar 26.** Riwayat masuk
![Riwayat masuk](./img/w11-23-riwayat-masuk.png)

**Gambar 27.** Riwayat masuk yang gagal saja
![Riwayat gagal](./img/w11-24-riwayat-gagal.png)

### Hak Akses

**Gambar 28.** Pelanggan membuka `/panel/pengguna`: 403
![Pelanggan 403](./img/w11-18-pelanggan-403.png)

**Gambar 29.** Staf gudang membuka `/panel/pengguna`: 403
![Gudang 403](./img/w11-26-gudang-403.png)
