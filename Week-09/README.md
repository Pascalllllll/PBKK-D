# Week 09 - Pengenalan Framework CodeIgniter 4: SIAKAD dan Toko Online

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  
**Kelas**: PBKK D

| | |
| :-- | :-- |
| Kode | folder [`siakad/`](./siakad) dan [`toko-online/`](./toko-online) (CodeIgniter 4.7.6) |
| Menjalankan | [`run.sh`](./run.sh) (Docker: PHP 8.3 + Apache, MariaDB 11) |

## Soal: Latihan 1 dan Latihan 2

Latihan 1 meminta penerapan materi pengenalan CodeIgniter. Setiap topik dikerjakan langsung di dalam aplikasi Latihan 2:

| No | Topik Latihan 1 | Penerapan |
| :-: | :-- | :-- |
| 1 | Instalasi dan konfigurasi CI | Instalasi CI 4.7.6 lewat Composer, konfigurasi `.env` (baseURL, database), migration dan seeder ([Bagian 1](#bagian-1-instalasi-dan-konfigurasi-codeigniter-4)). |
| 2 | Router dan Controller | `app/Config/Routes.php` dengan route group, namespace `Admin`, parameter `(:num)`, dan filter per group. |
| 3 | View Bootstrap | Beranda, login, register, dan etalase toko memakai Bootstrap 4. View dipecah menjadi layout dan partial. |
| 4 | SB Admin | Layout `layouts/admin.php` memakai template SB Admin 2: sidebar per role, topbar, card, dan tabel. |
| 5 | Model dan CRUD | Model CI4 dengan validasi, query builder, join, dan paginasi. CRUD lengkap untuk semua master data. |
| 6 | File upload | Upload foto mahasiswa dan gambar produk dengan validasi `is_image`, `max_size`, dan `mime_in`. |
| 7 | Hak akses user | Login dengan password ter-hash, session, dan `AuthFilter` berparameter role (`auth:admin`). |

Latihan 2 meminta dua aplikasi CodeIgniter: SIAKAD (Sistem Informasi Akademik) dan Toko Online.

## Pendahuluan

CodeIgniter 4 adalah framework PHP berpola MVC (Model-View-Controller). Request masuk lewat `public/index.php`, lalu dicocokkan oleh Router. Sebelum sampai ke Controller, request melewati Filter (CSRF, cek login, cek role). Controller mengambil data lewat Model dan mengirimkannya ke View.

SIAKAD punya tiga role. Admin mengelola data mahasiswa, dosen, dan matakuliah. Mahasiswa mengisi KRS dan melihat KHS beserta IP/IPK. Dosen menginput nilai untuk matakuliah yang diampunya.

Toko Online menampilkan katalog produk per kategori. Pengunjung bisa mengisi keranjang tanpa login, lalu checkout dan melihat riwayat pesanan setelah login. Admin mengelola produk, kategori, dan status pesanan.

Lingkungan pengembangan saya Linux (Debian), jadi ada penyesuaian dari modul yang memakai XAMPP di Windows:

| Modul | Yang saya pakai | Alasan |
| :-- | :-- | :-- |
| XAMPP (Apache + PHP + MySQL) | Container Docker `php:8.3-apache` + `mariadb:11` | Tidak perlu memasang PHP dan MySQL di sistem, dan lingkungannya sama di komputer mana pun |
| `php spark serve` | Apache di dalam container, document root `public/` | Sama seperti hosting sungguhan, `.htaccess` dan `mod_rewrite` ikut jalan |
| phpMyAdmin | `docker exec pbkk-db mariadb ...` | Cukup untuk mengecek isi tabel saat pengujian |

## Struktur Project

```
Week-09/
├── README.md
├── run.sh                      # up | start | stop | reset
├── docker/
│   ├── Dockerfile              # php:8.3-apache + intl, mysqli, gd, mod_rewrite
│   └── init.sql                # membuat database ci_siakad dan ci_toko
├── img/                        # screenshot hasil
├── siakad/
│   ├── env.docker              # disalin run.sh menjadi .env
│   ├── app/
│   │   ├── Config/             # Routes, Filters, Autoload, App, Pager
│   │   ├── Controllers/        # Home, Auth, Dashboard, Krs, Nilai
│   │   │   └── Admin/          # AdminController, Mahasiswa, Dosen, Matakuliah
│   │   ├── Database/
│   │   │   ├── Migrations/
│   │   │   └── Seeds/          # SiakadSeeder + img/ (foto contoh)
│   │   ├── Filters/AuthFilter.php
│   │   ├── Helpers/app_helper.php
│   │   ├── Models/             # User, Dosen, Mahasiswa, Matakuliah, Krs, HasUserAccount
│   │   └── Views/              # layouts/, admin/, dashboard/, krs/, nilai/, auth/, pager/
│   └── public/uploads/foto/
└── toko-online/
    ├── env.docker
    ├── app/
    │   ├── Controllers/        # Toko, Auth, Keranjang, Checkout, Pesanan
    │   │   └── Admin/          # Dashboard, Kategori, Produk, Pesanan
    │   ├── Database/Seeds/     # TokoSeeder + img/ (gambar produk contoh)
    │   ├── Libraries/Cart.php
    │   ├── Models/             # User, Kategori, Produk, Order, OrderItem
    │   └── Views/              # layouts/, toko/, keranjang/, checkout/, pesanan/, admin/
    └── public/uploads/produk/
```

## Bagian 1: Instalasi dan Konfigurasi CodeIgniter 4

### 1. Instalasi CodeIgniter lewat Composer

Saya membuat kedua proyek dari app starter resmi. Composer saya jalankan dari image `composer:2`, jadi Composer tidak perlu dipasang di sistem:

```bash
docker run --rm -u $(id -u):$(id -g) -v "$PWD":/app -w /app composer:2 \
  create-project codeigniter4/appstarter siakad
docker run --rm -u $(id -u):$(id -g) -v "$PWD":/app -w /app composer:2 \
  create-project codeigniter4/appstarter toko-online
```

Hasilnya folder dengan `app/` (kode aplikasi), `public/` (document root), `writable/` (cache, log, session), dan `vendor/` (framework). Saya juga menambahkan paket `codeigniter4/translations` supaya pesan validasi berbahasa Indonesia (`$defaultLocale = 'id'` di `app/Config/App.php`).

### 2. Image PHP

`docker/Dockerfile` memasang ekstensi yang dibutuhkan CI4 (`intl` wajib, `mysqli` untuk database, `gd` untuk gambar), mengaktifkan `mod_rewrite`, dan mengarahkan document root Apache ke `public/`:

```dockerfile
FROM php:8.3-apache
RUN apt-get update \
 && apt-get install -y --no-install-recommends libicu-dev libpng-dev libjpeg-dev libwebp-dev libzip-dev unzip \
 && docker-php-ext-configure gd --with-jpeg --with-webp \
 && docker-php-ext-install intl mysqli gd zip \
 && a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf
```

### 3. Konfigurasi `.env`

`env.docker` disalin menjadi `.env`. Hostname database adalah nama container MariaDB di network Docker. `app.indexPage` dikosongkan supaya URL tidak memakai `index.php`.

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost:8080/'
app.indexPage = ''

database.default.hostname = pbkk-db
database.default.database = ci_siakad
database.default.username = pbkk
database.default.password = pbkk123
database.default.DBDriver = MySQLi
database.default.port = 3306
```

CSRF diaktifkan global di `app/Config/Filters.php`, sehingga setiap form wajib membawa `csrf_field()` (otomatis lewat `form_open()`). Helper `form`, `url`, dan `app` di-autoload lewat `app/Config/Autoload.php`.

```php
public array $aliases = [
    // ...
    'auth' => \App\Filters\AuthFilter::class,
];

public array $globals = [
    'before' => ['csrf'],
];
```

### 4. Menjalankan Aplikasi

Prasyarat cukup Docker. `run.sh up` membangun image, menjalankan MariaDB dan dua container aplikasi, menjalankan `composer install` jika `vendor/` belum ada, lalu `php spark migrate` dan `php spark db:seed`:

```bash
cd Week-09
./run.sh up       # pertama kali
./run.sh start    # menjalankan lagi
./run.sh stop
./run.sh reset    # hapus database dan file upload, lalu up dari awal
```

```
SIAKAD      -> http://localhost:8080
Toko Online -> http://localhost:8081
```

Di dalam `run.sh`, migration dan seeder dijalankan dengan spark seperti biasa:

```bash
docker exec -u www-data pbkk-siakad php spark migrate --all
docker exec -u www-data pbkk-siakad php spark db:seed SiakadSeeder
docker exec -u www-data pbkk-toko   php spark migrate --all
docker exec -u www-data pbkk-toko   php spark db:seed TokoSeeder
```

Folder `vendor/`, file `.env`, dan isi `public/uploads/` tidak ikut di-commit. Foto mahasiswa dan gambar produk contoh disimpan di `app/Database/Seeds/img/` lalu disalin seeder ke folder upload dengan nama acak, sama seperti hasil upload biasa.

Akun untuk mencoba aplikasi:

| Aplikasi | Role | Username / Password |
| :-- | :-- | :-- |
| SIAKAD | Admin | `admin` / `admin123` |
| SIAKAD | Dosen | NIDN / NIDN, contoh `0011223301` |
| SIAKAD | Mahasiswa | NRP / NRP, contoh `5025241177` |
| Toko Online | Admin | `admin` / `admin123` |
| Toko Online | Customer | `budi` / `budi123` (atau daftar akun baru di `/register`) |

### 5. Kendala dan Solusi

| Kendala | Penyebab | Solusi |
| :-- | :-- | :-- |
| `docker compose` tidak dikenali | Plugin Compose tidak terpasang di Docker Debian | Membuat `run.sh` yang memakai `docker network` dan `docker run` biasa. |
| Error 500 saat menyimpan edit: *No validation rules for the placeholder: "id"* | Sejak CI 4.5, placeholder `{id}` di `is_unique[mahasiswa.nrp,id,{id}]` wajib punya aturan validasi sendiri | Menambahkan `'id' => 'permit_empty\|is_natural_no_zero'` di `$validationRules`, dan `id` ikut dikirim saat `update()`. Kolom `id` tetap dibuang oleh `$allowedFields`. |
| Pesan validasi berbahasa Inggris (*The nama field is required*) | Locale bawaan `en` | Memasang `codeigniter4/translations`, mengatur `$defaultLocale = 'id'`, dan memberi `label` pada setiap aturan. |
| Error 500 saat menghapus matakuliah yang sudah ada di KRS, atau kategori yang masih punya produk | FK `RESTRICT` membuat MariaDB menolak `DELETE` dan CI4 melempar `DatabaseException` | Exception ditangkap di controller dan diganti pesan, misalnya "Kategori Elektronik tidak dapat dihapus karena masih memiliki produk." |
| Stok bisa minus jika dua pelanggan checkout bersamaan | Cek stok dan pengurangan stok di dua query terpisah tanpa penguncian | Checkout dijalankan dalam transaksi dengan `SELECT ... FOR UPDATE`. Jumlah di keranjang juga selalu dibatasi stok. |

## Bagian 2: Aplikasi SIAKAD

### 1. Merancang Database

Akun login semua role disimpan di tabel `users`. Data profil disimpan di tabel `dosen` dan `mahasiswa` yang terhubung lewat `user_id`. Tabel `krs` menyimpan matakuliah yang diambil mahasiswa per tahun ajaran beserta nilainya.

```
users 1 ── 1 dosen        (user_id, ON DELETE CASCADE)
users 1 ── 1 mahasiswa    (user_id, ON DELETE CASCADE)
dosen 1 ── N mahasiswa    (dosen_wali_id, ON DELETE SET NULL)
dosen 1 ── N matakuliah   (dosen_id, ON DELETE SET NULL)
mahasiswa 1 ── N krs N ── 1 matakuliah
              (CASCADE)        (RESTRICT: matakuliah yang sudah diambil tidak bisa dihapus)
```

```php
// app/Database/Migrations/2026-11-04-000001_CreateSiakadTables.php
$this->forge->addUniqueKey(['mahasiswa_id', 'matakuliah_id', 'tahun_ajaran']);
$this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
// RESTRICT: matakuliah yang sudah diambil tidak bisa dihapus
$this->forge->addForeignKey('matakuliah_id', 'matakuliah', 'id', 'CASCADE', 'RESTRICT');
$this->forge->createTable('krs');
```

### 2. Router dan Controller

Route dikelompokkan per role, dan setiap group diberi filter. Dengan begitu controller tidak perlu memeriksa hak akses sendiri. Route CRUD admin dibuat dengan perulangan karena tiga master data memakai pola URL yang sama. Aksi ubah, hapus, dan logout memakai POST, jadi selalu dilindungi token CSRF.

```php
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout');
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Admin: CRUD master data dengan pola URL yang sama
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin'], static function ($routes) {
    foreach (['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'matakuliah' => 'Matakuliah'] as $uri => $controller) {
        $routes->get($uri, "{$controller}::index");
        $routes->get("{$uri}/new", "{$controller}::new");
        $routes->post($uri, "{$controller}::create");
        $routes->get("{$uri}/(:num)/edit", "{$controller}::edit/$1");
        $routes->post("{$uri}/(:num)", "{$controller}::update/$1");
        $routes->post("{$uri}/(:num)/delete", "{$controller}::delete/$1");
    }
});

// Mahasiswa: KRS dan KHS
$routes->group('', ['filter' => 'auth:mahasiswa'], static function ($routes) {
    $routes->get('krs', 'Krs::index');
    $routes->post('krs', 'Krs::ambil');
    $routes->post('krs/(:num)/delete', 'Krs::drop/$1');
    $routes->get('khs', 'Krs::khs');
});

// Dosen: input nilai matakuliah yang diampu
$routes->group('nilai', ['filter' => 'auth:dosen'], static function ($routes) {
    $routes->get('/', 'Nilai::index');
    $routes->get('(:num)', 'Nilai::show/$1');
    $routes->post('(:num)', 'Nilai::save/$1');
});
```

Bagian yang sama di ketiga controller admin (cari data atau 404, kembali ke form dengan pesan error, redirect ke index) dikumpulkan di `Admin/AdminController.php`. `Dashboard::index` memilih tampilan sesuai role yang login.

### 3. View Bootstrap dan Template SB Admin 2

Beranda dan login memakai Bootstrap 4 (`layouts/public.php`). Halaman setelah login memakai `layouts/admin.php` berbasis SB Admin 2 (CSS dan JS dari CDN jsDelivr). View lain cukup meng-extend layout dan mengisi section `content`, serta section `actions` untuk tombol di judul halaman.

```php
<!-- app/Views/admin/mahasiswa/index.php -->
<?= $this->extend('layouts/admin') ?>

<?= $this->section('actions') ?>
<a href="<?= site_url('admin/mahasiswa/new') ?>" class="btn btn-sm btn-primary">Tambah Mahasiswa</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
  ... tabel data, form pencarian, $pager->links('default', 'bootstrap') ...
<?= $this->endSection() ?>
```

Partial yang dipakai bersama:

| File | Isi |
| :-- | :-- |
| `layouts/_sidebar.php` | Menu sesuai role (admin: master data, dosen: input nilai, mahasiswa: KRS dan KHS), menu aktif ditandai otomatis |
| `layouts/_topbar.php` | Nama user, badge role, tombol logout (form POST) |
| `layouts/_alerts.php` | Flashdata `success`, `error`, dan daftar error validasi |
| `pager/bootstrap.php` | Template paginasi dengan kelas Bootstrap, didaftarkan di `Config/Pager.php` |

![Beranda SIAKAD dengan layout Bootstrap](./img/siakad-01-beranda.png)
*Beranda SIAKAD dengan layout Bootstrap.*

![Dashboard admin SIAKAD dengan template SB Admin 2](./img/siakad-04-dashboard-admin.png)
*Dashboard admin SIAKAD dengan template SB Admin 2.*

### 4. Model dan CRUD

Setiap tabel punya Model dengan `$allowedFields` (melindungi dari mass assignment), `$validationRules` berlabel, dan timestamp otomatis. Password di-hash otomatis lewat callback `beforeInsert` dan `beforeUpdate`:

```php
class UserModel extends Model
{
    protected $table         = 'users';
    protected $allowedFields = ['username', 'password', 'nama', 'role'];
    protected $beforeInsert  = ['hashPassword'];
    protected $beforeUpdate  = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (! empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['data']['password']);
        }
        return $data;
    }

    public function attempt(string $username, string $password): ?array
    {
        $user = $this->where('username', $username)->first();
        return ($user && password_verify($password, $user['password'])) ? $user : null;
    }
}
```

Dosen dan mahasiswa selalu punya akun login. Trait `HasUserAccount` membuat akun di `users` dan profilnya dalam satu transaksi, dengan username dan password awal sama dengan NIDN/NRP. Saat profil dihapus, cukup akunnya yang dihapus karena profil ikut terhapus lewat FK `CASCADE`.

```php
public function createWithAccount(array $data): int
{
    $users = model(UserModel::class);
    $nomor = $data[$this->nomorInduk()];   // 'nidn' atau 'nrp'

    if ($users->where('username', $nomor)->countAllResults() > 0) {
        throw new RuntimeException("Username {$nomor} sudah dipakai akun lain.");
    }

    $this->db->transException(true)->transStart();
    $data['user_id'] = $users->insert([
        'username' => $nomor, 'password' => $nomor, 'nama' => $data['nama'], 'role' => $this->role(),
    ]);
    $id = $this->insert($data);
    $this->db->transComplete();

    return (int) $id;
}
```

Halaman daftar dilengkapi pencarian (`like` / `orLike` di dalam `groupStart()`), join ke nama dosen, dan paginasi `paginate(10)`. NRP, NIDN, dan kode matakuliah dicek unik dengan `is_unique[...,id,{id}]` supaya data sendiri tidak dianggap duplikat saat diedit.

![Data mahasiswa dengan foto, pencarian, dan paginasi](./img/siakad-05-data-mahasiswa.png)
*Data mahasiswa dengan foto, pencarian, dan paginasi.*

### 5. Upload Foto Mahasiswa

Form mahasiswa memakai `form_open_multipart()`. File divalidasi (gambar, maksimal 1 MB, JPG/PNG/WEBP), lalu disimpan dengan nama acak ke `public/uploads/foto/`. Foto wajib saat tambah data dan opsional saat edit. Foto lama dihapus setelah data baru tersimpan, atau saat mahasiswa dihapus. Di form ada pratinjau gambar sebelum diunggah.

```php
private const FOTO_DIR  = FCPATH . 'uploads/foto/';
private const FOTO_RULE = 'is_image[foto]|max_size[foto,1024]|mime_in[foto,image/jpeg,image/png,image/webp]';

public function create()
{
    $data = $this->input();
    // Operator | (bukan ||) supaya kedua validasi tetap jalan dan semua pesan error tampil sekaligus
    if (! $this->model->validate($data) | ! $this->validate(['foto' => 'uploaded[foto]|' . self::FOTO_RULE], $this->fotoMessages())) {
        return $this->back();
    }

    $data['foto'] = $this->storeFoto($this->request->getFile('foto'));
    // ...createWithAccount($data)
}

private function storeFoto(UploadedFile $file): string
{
    $name = $file->getRandomName();
    $file->move(self::FOTO_DIR, $name);
    return $name;
}
```

![Form edit mahasiswa dengan pratinjau foto sebelum diunggah](./img/siakad-06-form-mahasiswa-upload.png)
*Form edit mahasiswa dengan pratinjau foto sebelum diunggah.*

![Validasi gagal saat menambah mahasiswa](./img/siakad-06c-validasi-gagal.png)
*Validasi gagal: NRP tidak valid, nama kosong, dan file bukan gambar tampil sekaligus.*

### 6. Hak Akses User dengan Filter

Login menyimpan `logged_in`, `role`, dan data user ke session setelah ID session diganti (`session()->regenerate(true)`) untuk mencegah session fixation. `AuthFilter` menerima argumen role dari route, misalnya `auth:admin`:

```php
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! empty($arguments) && ! in_array($session->get('role'), $arguments, true)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }
}
```

![Mahasiswa membuka /admin/mahasiswa dan diarahkan ke dashboard](./img/siakad-13-akses-ditolak.png)
*Mahasiswa membuka /admin/mahasiswa dan diarahkan ke dashboard.*

### 7. KRS dan KHS Mahasiswa

Mahasiswa memilih matakuliah untuk semester berjalan (2026/2027 Ganjil). `KrsModel::ambil()` menolak matakuliah yang sudah diambil dan total SKS di atas 24:

```php
public function ambil(int $mahasiswaId, array $matakuliah): ?string
{
    $krs = $this->forMahasiswa($mahasiswaId);

    if (in_array($matakuliah['id'], array_column($krs, 'matakuliah_id'))) {
        return 'Matakuliah sudah ada di KRS.';
    }

    $total = array_sum(array_column($krs, 'sks')) + $matakuliah['sks'];
    if ($total > self::MAX_SKS) {
        return 'Total SKS melebihi batas ' . self::MAX_SKS . ' SKS.';
    }

    $this->insert([...]);
    return null;
}
```

Pembatalan hanya berlaku untuk KRS milik sendiri, di semester berjalan, yang belum dinilai. Syarat ini ditulis langsung di query `DELETE`, jadi mengganti ID di URL tidak bisa membatalkan KRS mahasiswa lain:

```php
$this->krs->where([
    'id'           => $id,
    'mahasiswa_id' => $this->mahasiswa['id'],
    'tahun_ajaran' => KrsModel::TAHUN_AJARAN,
])->where('nilai', null)->delete();
```

KHS mengelompokkan nilai per tahun ajaran. IP dan IPK dihitung helper `hitung_ip()` dengan bobot A=4, AB=3,5, B=3, BC=2,5, C=2, D=1, E=0. Matakuliah yang belum dinilai tidak ikut dihitung.

![KRS semester berjalan](./img/siakad-11-krs.png)
*KRS semester berjalan. Matakuliah yang sudah dinilai tidak bisa dibatalkan.*

![KHS dengan IP per semester dan IPK](./img/siakad-12-khs.png)
*KHS dengan IP per semester dan IPK.*

### 8. Input Nilai oleh Dosen

Dosen hanya melihat matakuliah yang diampunya. Membuka matakuliah dosen lain (misalnya `/nilai/2`) menghasilkan 404. Nilai semua peserta dikirim dari satu form, divalidasi dulu terhadap daftar huruf yang sah dan daftar peserta matakuliah itu, baru disimpan sekaligus dalam satu transaksi.

```php
private function matakuliahOr404(int $id): array
{
    return model(MatakuliahModel::class)->where(['id' => $id, 'dosen_id' => $this->dosen['id']])->first()
        ?? throw PageNotFoundException::forPageNotFound();
}
```

![Dosen menyimpan nilai peserta](./img/siakad-16-input-nilai.png)
*Dosen menyimpan nilai peserta.*

## Bagian 3: Aplikasi Toko Online

### 1. Merancang Database

Akun admin dan pelanggan disimpan di tabel `users` dengan kolom `role`. Setiap pesanan (`orders`) punya banyak `order_items`. Setiap item menyimpan harga saat dibeli (snapshot), jadi perubahan harga produk tidak mengubah pesanan lama.

```
kategori 1 ── N produk        (kategori_id, RESTRICT: kategori berisi produk tidak bisa dihapus)
users    1 ── N orders        (user_id, CASCADE)
orders   1 ── N order_items   (order_id, CASCADE)
produk   1 ── N order_items   (produk_id, RESTRICT: produk yang pernah dipesan tidak bisa dihapus)
```

```php
// app/Database/Migrations/2026-11-04-000001_CreateTokoTables.php
// addForeignKey(field, table, column, onUpdate, onDelete)
$this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'CASCADE', 'RESTRICT');
$this->forge->createTable('produk');
// ...
$this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
$this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'RESTRICT');
$this->forge->createTable('order_items');
```

### 2. Router dan Filter Hak Akses

Etalase dan keranjang bersifat publik. Checkout dan riwayat pesanan hanya untuk role `customer`, sedangkan panel admin memakai `auth:admin`. `AuthFilter` sama dengan SIAKAD, ditambah satu hal: URL yang diminta diingat 5 menit (`setTempdata`), jadi pengunjung yang menekan Checkout lalu login langsung kembali ke halaman checkout.

```php
$routes->get('/', 'Toko::index');
$routes->get('produk/(:num)', 'Toko::detail/$1');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');

// Keranjang di session, boleh tanpa login
$routes->get('keranjang', 'Keranjang::index');
$routes->post('keranjang/tambah', 'Keranjang::tambah');
$routes->post('keranjang/update', 'Keranjang::update');
$routes->post('keranjang/hapus/(:num)', 'Keranjang::hapus/$1');

// Customer: checkout dan pesanan
$routes->group('', ['filter' => 'auth:customer'], static function ($routes) {
    $routes->get('checkout', 'Checkout::index');
    $routes->post('checkout', 'Checkout::proses');
    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::show/$1');
    $routes->post('pesanan/(:num)/batal', 'Pesanan::batal/$1');
});

// Admin: dashboard, CRUD kategori dan produk, kelola pesanan
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    foreach (['kategori' => 'Kategori', 'produk' => 'Produk'] as $uri => $controller) {
        // index, new, create, edit, update, delete (pola sama dengan SIAKAD)
    }
    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::show/$1');
    $routes->post('pesanan/(:num)/status', 'Pesanan::status/$1');
});
```

Registrasi selalu membuat akun `customer`. Kolom role tidak pernah diambil dari input form.

![Pelanggan membuka /admin/produk dan diarahkan ke beranda](./img/toko-10-akses-ditolak.png)
*Pelanggan membuka /admin/produk dan diarahkan ke beranda.*

### 3. Katalog Produk

Beranda menampilkan produk dalam kartu Bootstrap dengan filter kategori (beserta jumlah produknya), pencarian nama, dan paginasi 9 produk per halaman. Query dibangun di `ProdukModel::daftar()` supaya bisa dipakai juga oleh halaman produk admin.

```php
public function index(): string
{
    $q        = trim((string) $this->request->getGet('q'));
    $kategori = (int) $this->request->getGet('kategori');
    $model    = model(ProdukModel::class);

    return view('toko/index', [
        'produk'   => $model->daftar($q, $kategori)->paginate(9),
        'pager'    => $model->pager,
        'kategori' => model(KategoriModel::class)->withJumlahProduk()->findAll(),
        // ...
    ]);
}
```

Produk yang belum punya gambar ditampilkan dengan ikon kategorinya (kolom `ikon` di tabel `kategori`).

![Katalog produk dengan filter kategori dan paginasi](./img/toko-01-katalog.png)
*Katalog produk dengan filter kategori dan paginasi.*

### 4. Keranjang Belanja (Session)

Keranjang disimpan di session dalam bentuk `[produk_id => qty]` melalui library `App\Libraries\Cart`, jadi pengunjung bisa belanja sebelum login. Jumlah selalu dibatasi stok yang tersedia, baik saat menambah maupun saat mengubah jumlah.

```php
public function add(int $id, int $qty): ?string
{
    $produk = model(ProdukModel::class)->find($id);
    if ($produk === null) {
        return 'Produk tidak ditemukan.';
    }
    if ($produk['stok'] < 1) {
        return 'Stok produk habis.';
    }

    $cart      = $this->raw();
    $baru      = ($cart[$id] ?? 0) + max(1, $qty);
    $cart[$id] = min($baru, (int) $produk['stok']);
    session()->set(self::KEY, $cart);

    return $baru > $produk['stok'] ? "Jumlah dibatasi sesuai stok tersedia ({$produk['stok']})." : null;
}
```

![Power bank diminta 5, jumlah di keranjang dibatasi stok (3)](./img/toko-06-keranjang-stok.png)
*Power bank diminta 5, jumlah di keranjang dibatasi stok (3).*

### 5. Checkout dalam Satu Transaksi

Stok bisa berubah antara saat produk masuk keranjang dan saat checkout. Karena itu `OrderModel::buat()` mengunci baris produk dengan `SELECT ... FOR UPDATE`, memeriksa stok, menguranginya, lalu menyimpan order dan item dalam satu transaksi. Jika satu item gagal, semua perubahan di-rollback.

```php
$db->transBegin();
try {
    foreach ($items as $item) {
        $p = $db->query('SELECT id, nama, harga, stok FROM produk WHERE id = ? FOR UPDATE', [$item['id']])->getRowArray();
        if ($p['stok'] < $item['qty']) {
            throw new RuntimeException("Stok \"{$p['nama']}\" tidak mencukupi (tersisa {$p['stok']}).");
        }
        $total  += $p['harga'] * $item['qty'];
        $baris[] = ['produk_id' => $p['id'], 'qty' => $item['qty'], 'harga' => $p['harga']];
        $db->query('UPDATE produk SET stok = stok - ? WHERE id = ?', [$item['qty'], $p['id']]);
    }
    $orderId = $this->insert($pengiriman + ['user_id' => $userId, 'total' => $total, 'status' => 'pending'], true);
    foreach ($baris as $b) {
        model(OrderItemModel::class)->insert($b + ['order_id' => $orderId]);
    }
    $db->transCommit();
} catch (\Throwable $e) {
    $db->transRollback();
    throw $e;
}
```

`Checkout::proses()` memvalidasi nama penerima, nomor HP (format `08...` atau `+628...`), dan alamat, memanggil `buat()`, mengosongkan keranjang, lalu mengarahkan ke detail pesanan dengan pesan "Pesanan berhasil dibuat."

![Checkout berhasil, pesanan berstatus pending dan bisa dibatalkan](./img/toko-08-checkout-berhasil.png)
*Checkout berhasil, pesanan berstatus pending dan bisa dibatalkan.*

### 6. Riwayat dan Pembatalan Pesanan

Pelanggan hanya melihat pesanannya sendiri. Pesanan milik orang lain dianggap tidak ada (404). Pesanan berstatus `pending` bisa dibatalkan, dan pembatalan mengembalikan stok di transaksi yang sama.

![Riwayat pesanan pelanggan](./img/toko-09-riwayat-pesanan.png)
*Riwayat pesanan pelanggan.*

### 7. Panel Admin: CRUD Kategori dan Produk dengan Upload Gambar

Dashboard admin menampilkan jumlah produk, pesanan, pelanggan, total pendapatan (pesanan berstatus dibayar, dikirim, atau selesai), jumlah pesanan per status, dan produk yang stoknya di bawah 5. Pendapatan dihitung lewat builder supaya `selectSum()` tidak bercampur dengan select bawaan model:

```php
public function pendapatan(): int
{
    $row = $this->builder()->selectSum('total')->whereIn('status', ['dibayar', 'dikirim', 'selesai'])->get()->getRowArray();
    return (int) ($row['total'] ?? 0);
}
```

CRUD produk menerima gambar JPG/PNG/WEBP maksimal 2 MB yang disimpan ke `public/uploads/produk` dengan nama acak. Gambar lama dihapus saat diganti. Penghapusan yang melanggar FK `RESTRICT` ditangkap dan ditampilkan sebagai pesan:

```php
private const GAMBAR_RULE = 'is_image[gambar]|mime_in[gambar,image/jpeg,image/png,image/webp]|max_size[gambar,2048]';

public function delete(int $id)
{
    $row = $this->findOr404($id);
    try {
        $this->model->delete($id);
    } catch (DatabaseException) {
        // FK order_items -> produk bersifat RESTRICT
        return $this->toIndex('error', "Produk {$row['nama']} tidak dapat dihapus karena sudah pernah dipesan.");
    }
    $this->hapusFile($row['gambar']);
    return $this->toIndex('success', "Produk {$row['nama']} dihapus.");
}
```

![Dashboard admin toko](./img/toko-11-dashboard-admin.png)
*Dashboard admin toko.*

![Form produk dengan pratinjau gambar](./img/toko-15-form-produk-upload.png)
*Form tambah produk dengan pratinjau gambar.*

![Kategori berisi produk tidak bisa dihapus](./img/toko-13-kategori-restrict.png)
*Kategori yang masih punya produk ditolak saat dihapus (FK RESTRICT).*

### 8. Pengelolaan Status Pesanan

Admin mengubah status melalui alur yang ditulis sebagai konstanta di `OrderModel`. Dropdown di halaman detail hanya menampilkan status berikutnya yang sah, dan model menolak perpindahan lain (misalnya dibayar langsung ke selesai). Status `selesai` dan `batal` bersifat final, jadi stok tidak pernah dikembalikan dua kali. Baris order dikunci dengan `FOR UPDATE` saat status diubah.

```php
public const ALUR = [
    'pending' => ['dibayar', 'batal'],
    'dibayar' => ['dikirim', 'batal'],
    'dikirim' => ['selesai'],
    'selesai' => [],
    'batal'   => [],
];
```

![Detail pesanan admin, dropdown hanya berisi status berikutnya yang sah](./img/toko-17-detail-pesanan.png)
*Detail pesanan admin, dropdown hanya berisi status berikutnya yang sah.*

## Pembagian Tugas

Tugas ini dikerjakan secara individu:

| Anggota | Area | Pekerjaan |
| :-- | :-- | :-- |
| Hosea (5025241177) | Lingkungan | Dockerfile, `run.sh`, instalasi CI4 lewat Composer, konfigurasi `.env` |
| Hosea (5025241177) | SIAKAD | Desain database dan migration, autentikasi dan filter role, CRUD master data, upload foto, KRS/KHS, input nilai, pengujian |
| Hosea (5025241177) | Toko Online | Desain database, katalog, keranjang, checkout, riwayat pesanan, panel admin, upload gambar produk, pengujian |
| Hosea (5025241177) | Dokumentasi | Screenshot hasil dan penulisan laporan |

## Alur Data

Contoh alur data saat mahasiswa mengambil matakuliah di halaman KRS:

```
Mahasiswa klik "Ambil" di krs/index.php (form POST + csrf_field)
      │  POST /krs  matakuliah_id=3
      ▼
Filter global CSRF  ──(token salah)──► 403
      │
Filter auth:mahasiswa (AuthFilter)
      │  belum login ─► /login      role lain ─► /dashboard + "Anda tidak memiliki akses"
      ▼
Router: Krs::ambil   (initController: user('id') ─► MahasiswaModel::findByUser)
      │  MatakuliahModel::find(3)
      ▼
KrsModel::ambil(mahasiswaId, matakuliah)
      │  cek duplikat ─► "Matakuliah sudah ada di KRS."
      │  cek total SKS > 24 ─► "Total SKS melebihi batas 24 SKS."
      │  INSERT krs (tahun_ajaran = 2026/2027 Ganjil)
      ▼
redirect('/krs')->with('success', ...)  ─► flashdata ditampilkan layouts/_alerts.php
```

## Tanggung Jawab Tiap File

| File | Tanggung jawab |
| :-- | :-- |
| `app/Config/Routes.php` | Memetakan URL ke controller dan memasang filter per group role |
| `app/Config/Filters.php` | Mendaftarkan alias `auth` dan mengaktifkan CSRF global |
| `app/Config/Autoload.php` | Autoload helper `form`, `url`, dan `app` |
| `app/Filters/AuthFilter.php` | Cek login dan role sebelum controller dijalankan |
| `app/Helpers/app_helper.php` | `user()`, `invalid()`/`field_error()` untuk form; SIAKAD: `hitung_ip()`, `bobot_nilai()`; Toko: `rupiah()`, `status_badge()` |
| `siakad/app/Controllers/Auth.php` | Login, logout, dan pengisian session |
| `siakad/app/Controllers/Admin/*.php` | CRUD mahasiswa (dengan upload foto), dosen, dan matakuliah |
| `siakad/app/Controllers/Krs.php` | KRS (ambil/batal) dan KHS mahasiswa |
| `siakad/app/Controllers/Nilai.php` | Daftar matakuliah dosen dan input nilai peserta |
| `siakad/app/Models/*.php` | Akses tabel, validasi, join, aturan KRS, hash password, akun + profil dalam satu transaksi |
| `toko-online/app/Libraries/Cart.php` | Keranjang berbasis session, jumlah dibatasi stok |
| `toko-online/app/Models/OrderModel.php` | Checkout dalam transaksi (`FOR UPDATE`), alur status, pengembalian stok saat batal |
| `toko-online/app/Controllers/Keranjang.php`, `Checkout.php`, `Pesanan.php` | Alur belanja pelanggan |
| `toko-online/app/Controllers/Admin/*.php` | Dashboard, CRUD kategori dan produk (upload gambar), pengelolaan status pesanan |
| `app/Database/Migrations`, `Seeds` | Skema tabel dengan FK, data awal, dan gambar contoh |
| `app/Views/layouts/*.php` | Layout Bootstrap dan SB Admin 2, sidebar, topbar, alert flashdata |

## Pengujian

Selain dicoba lewat browser, setiap skenario diuji dengan `curl` (membawa cookie session dan token CSRF) lalu isi tabel dicek langsung di MariaDB:

| Skenario | Hasil |
| :-- | :-- |
| Login dengan password salah | Kembali ke form, "Username atau password salah." |
| Membuka halaman tanpa login | Redirect ke `/login` |
| Mahasiswa membuka `/admin/mahasiswa`, pelanggan membuka `/admin/produk` | Redirect, "Anda tidak memiliki akses ke halaman tersebut." |
| POST tanpa token CSRF | 403 |
| Tambah mahasiswa tanpa foto, NRP 3 digit, upload file `.txt` | Semua pesan error tampil sekaligus, tidak ada data tersimpan |
| Tambah mahasiswa valid, login dengan NRP | Akun dan profil dibuat, login berhasil |
| Edit mahasiswa dengan foto baru, lalu hapus | Foto lama terhapus dari disk; akun di `users` ikut terhapus |
| Hapus matakuliah yang sudah di KRS | Ditolak dengan pesan, data tetap ada |
| Ambil matakuliah yang sama dua kali / melebihi 24 SKS | "Matakuliah sudah ada di KRS." / "Total SKS melebihi batas 24 SKS." |
| Batalkan KRS milik mahasiswa lain atau KRS semester lalu | "KRS tidak ditemukan atau sudah dinilai.", data tetap ada |
| Dosen membuka `/nilai/2` (bukan matakuliahnya) | 404 |
| Dosen mengirim nilai "Z" | Ditolak, tidak ada nilai yang tersimpan |
| Tambah 5 power bank ke keranjang (stok 3) | Jumlah dibatasi 3 dengan pesan peringatan |
| Stok diubah jadi 1 setelah barang masuk keranjang, lalu checkout | "Stok tidak mencukupi (tersisa 1)", tidak ada order dan stok lain tidak berubah (rollback) |
| Checkout valid lalu batalkan pesanan | Stok berkurang lalu kembali seperti semula; pembatalan kedua ditolak |
| Membuka atau membatalkan pesanan pelanggan lain | 404 |
| Admin mengubah status dibayar ke selesai, atau batal ke dibayar | Ditolak sesuai alur status |
| Hapus kategori berisi produk / produk yang pernah dipesan | Ditolak dengan pesan |
| Register dengan username/email terpakai dan password tidak cocok | Semua pesan error tampil di field masing-masing |

## Kesimpulan

1. Pembagian MVC di CodeIgniter 4 terasa saat menambah fitur: aturan KRS (duplikat, batas 24 SKS) cukup ditulis di `KrsModel`, dan controller hanya meneruskan pesan errornya ke view.
2. Hak akses ditulis sekali di `AuthFilter` lalu dipasang per route group. Tidak ada controller yang memeriksa role sendiri, dan menambah halaman admin baru cukup dengan menaruh route-nya di group `admin`.
3. Foreign key `RESTRICT` menolak penghapusan matakuliah yang sudah ada di KRS dan kategori yang masih punya produk. Controller hanya perlu menangkap `DatabaseException` dan menampilkan pesannya.
4. Validasi model dan `UploadedFile` menangani sebagian besar kasus salah input: NRP ganda, file bukan gambar, ukuran terlalu besar. Yang perlu ditulis sendiri hanya penamaan acak dan penghapusan file lama.
5. Pengujian stok yang diubah di tengah checkout menunjukkan `SELECT ... FOR UPDATE` di dalam transaksi bekerja: checkout ditolak, tidak ada order yang tersimpan, dan stok produk lain tidak berubah.
6. Dengan Docker, `./run.sh reset` membangun ulang database dan data contoh dari nol, sehingga screenshot di laporan ini bisa diulang dengan data yang sama.

## Hasil (Foto)

### SIAKAD

**Gambar 1.** Halaman beranda SIAKAD
![Beranda SIAKAD](./img/siakad-01-beranda.png)

**Gambar 2.** Halaman login
![Login](./img/siakad-02-login.png)

**Gambar 3.** Login gagal (username/password salah)
![Login gagal](./img/siakad-03-login-gagal.png)

**Gambar 4.** Dashboard admin (SB Admin 2)
![Dashboard admin](./img/siakad-04-dashboard-admin.png)

**Gambar 5.** Data mahasiswa dengan foto dan paginasi
![Data mahasiswa](./img/siakad-05-data-mahasiswa.png)

**Gambar 6.** Pencarian mahasiswa (kata kunci "2023")
![Cari mahasiswa](./img/siakad-05b-cari-mahasiswa.png)

**Gambar 7.** Form edit mahasiswa dengan upload dan pratinjau foto
![Form edit mahasiswa](./img/siakad-06-form-mahasiswa-upload.png)

**Gambar 8.** Data mahasiswa setelah foto diperbarui
![Mahasiswa diperbarui](./img/siakad-06b-mahasiswa-diperbarui.png)

**Gambar 9.** Validasi gagal: NRP tidak valid, nama kosong, file bukan gambar
![Validasi gagal](./img/siakad-06c-validasi-gagal.png)

**Gambar 10.** Data dosen
![Data dosen](./img/siakad-07-data-dosen.png)

**Gambar 11.** Data matakuliah
![Data matakuliah](./img/siakad-08-data-matakuliah.png)

**Gambar 12.** Matakuliah yang sudah ada di KRS tidak bisa dihapus (FK RESTRICT)
![Matakuliah restrict](./img/siakad-09-matakuliah-restrict.png)

**Gambar 13.** Dashboard dosen
![Dashboard dosen](./img/siakad-14-dashboard-dosen.png)

**Gambar 14.** Daftar matakuliah yang diampu dosen
![Matakuliah dosen](./img/siakad-15-matakuliah-dosen.png)

**Gambar 15.** Input nilai peserta
![Input nilai](./img/siakad-16-input-nilai.png)

**Gambar 16.** Dosen membuka matakuliah dosen lain, hasilnya 404
![404 matakuliah dosen lain](./img/siakad-16b-matakuliah-dosen-lain-404.png)

**Gambar 17.** Dashboard mahasiswa
![Dashboard mahasiswa](./img/siakad-10-dashboard-mahasiswa.png)

**Gambar 18.** Pengisian KRS
![KRS](./img/siakad-11-krs.png)

**Gambar 19.** KHS dengan IP per semester dan IPK
![KHS](./img/siakad-12-khs.png)

**Gambar 20.** Mahasiswa membuka halaman admin, akses ditolak
![Akses ditolak](./img/siakad-13-akses-ditolak.png)

### Toko Online

**Gambar 21.** Katalog produk (beranda)
![Katalog](./img/toko-01-katalog.png)

**Gambar 22.** Filter produk per kategori
![Filter kategori](./img/toko-02-filter-kategori.png)

**Gambar 23.** Detail produk dengan gambar hasil upload
![Detail produk](./img/toko-03-detail-produk.png)

**Gambar 24.** Keranjang: jumlah dibatasi sesuai stok
![Keranjang](./img/toko-06-keranjang-stok.png)

**Gambar 25.** Checkout tanpa login diarahkan ke halaman login
![Login](./img/toko-04-login.png)

**Gambar 26.** Halaman checkout setelah login
![Checkout](./img/toko-07-checkout.png)

**Gambar 27.** Validasi checkout (nomor HP dan alamat)
![Validasi checkout](./img/toko-07b-checkout-validasi.png)

**Gambar 28.** Checkout berhasil, pesanan dibuat
![Checkout berhasil](./img/toko-08-checkout-berhasil.png)

**Gambar 29.** Riwayat pesanan pelanggan
![Riwayat pesanan](./img/toko-09-riwayat-pesanan.png)

**Gambar 30.** Pelanggan membuka halaman admin, akses ditolak
![Akses ditolak](./img/toko-10-akses-ditolak.png)

**Gambar 31.** Registrasi pelanggan dengan username terpakai dan password tidak cocok
![Register](./img/toko-05-register-validasi.png)

**Gambar 32.** Dashboard admin
![Dashboard admin](./img/toko-11-dashboard-admin.png)

**Gambar 33.** Data kategori
![Data kategori](./img/toko-12-data-kategori.png)

**Gambar 34.** Kategori yang masih memiliki produk tidak bisa dihapus (FK RESTRICT)
![Kategori restrict](./img/toko-13-kategori-restrict.png)

**Gambar 35.** Data produk
![Data produk](./img/toko-14-data-produk.png)

**Gambar 36.** Form produk dengan upload dan pratinjau gambar
![Form produk](./img/toko-15-form-produk-upload.png)

**Gambar 37.** Produk baru tersimpan
![Produk ditambahkan](./img/toko-15b-produk-ditambahkan.png)

**Gambar 38.** Produk baru tampil di katalog (kategori Makanan & Minuman)
![Katalog produk baru](./img/toko-18-katalog-produk-baru.png)

**Gambar 39.** Data pesanan untuk admin
![Data pesanan](./img/toko-16-data-pesanan.png)

**Gambar 40.** Detail pesanan dan pilihan status berikutnya
![Detail pesanan](./img/toko-17-detail-pesanan.png)

**Gambar 41.** Status pesanan diubah ke dibayar
![Status diubah](./img/toko-17b-status-diubah.png)
