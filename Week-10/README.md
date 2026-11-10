# Week 10 - Routing, Controller, dan View CodeIgniter 4: Toko Online ShopEase

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  
**Kelas**: PBKK D

| | |
| :-- | :-- |
| Kode | folder [`shopease/`](./shopease) (CodeIgniter 4.7.6, PHP 8.3) |
| Menjalankan | [`run.sh`](./run.sh) (Docker: PHP 8.3 + Apache, MariaDB 11) |
| Tampilan | Bootstrap 5.3, Bootstrap Icons, Chart.js 4 (dari CDN jsDelivr) |

## Soal: Toko Online dengan Tiga Role

Minggu ini materi berfokus pada tiga lapisan CodeIgniter yang dilewati setiap request: **Routing**, **Controller**, dan **View**. Materi tersebut diterapkan pada aplikasi toko online yang alurnya lengkap, dari katalog sampai pesanan selesai:

| No | Kebutuhan | Penerapan di ShopEase |
| :-: | :-- | :-- |
| 1 | Halaman publik | Beranda, tentang, kontak, katalog (cari, filter kategori, urutkan, paginasi), detail produk |
| 2 | Autentikasi | Daftar akun pelanggan, masuk, keluar. Password di-hash, ID session diganti saat login |
| 3 | Pelanggan | Keranjang (disimpan di database), checkout dengan buku alamat, unggah bukti transfer, riwayat dan detail pesanan, konfirmasi barang diterima |
| 4 | Admin | Dashboard dan grafik, CRUD kategori, CRUD produk + upload gambar, verifikasi pembayaran, laporan penjualan + unduh CSV |
| 5 | Staf gudang | Melihat pesanan, mengisi kurir dan nomor resi, menandai pesanan selesai |
| 6 | Hak akses | Filter per role di level route, halaman 403 dan 404 kustom |
| 7 | Routing | Route group per role, named route, parameter `(:num)` dan `(:segment)`, aksi ubah data hanya lewat POST |
| 8 | Controller | Controller per tanggung jawab, namespace `Panel`, validasi input, transaksi database |
| 9 | View | Dua layout (toko dan panel), partial, komponen yang dipakai ulang, section `content`/`aksi`/`scripts` |

## Pendahuluan

ShopEase adalah toko online yang menjual perlengkapan kuliah dan kebutuhan harian. Alurnya mengikuti toko yang memakai transfer bank manual:

1. Pelanggan memasukkan produk ke keranjang lalu checkout dengan memilih alamat.
2. Pelanggan mentransfer dan mengunggah bukti pembayaran.
3. Admin memeriksa bukti. Jika diterima, pesanan masuk antrean pengiriman. Jika ditolak, pelanggan melihat alasannya dan bisa mengunggah ulang.
4. Staf gudang mengemas barang dan mengisi kurir serta nomor resi.
5. Setelah barang sampai, pelanggan menekan **Pesanan Diterima** dan pesanan selesai.

Ada tiga role:

| Role | Hak akses |
| :-- | :-- |
| `customer` | Katalog, keranjang, checkout, unggah bukti, pesanan milik sendiri |
| `admin` | Semua menu panel: dashboard, pesanan, verifikasi bayar, laporan, kategori, produk |
| `gudang` | Dashboard, daftar dan detail pesanan, input resi, tandai selesai |

Week 09 sudah membuat toko online sederhana. Week 10 dibuat sebagai aplikasi baru yang lebih lengkap. Perbedaan utamanya:

| | Week 09 (Toko Online) | Week 10 (ShopEase) |
| :-- | :-- | :-- |
| Role | admin, customer | admin, gudang, customer |
| Keranjang | Session, boleh tanpa login | Tabel `keranjang`, tetap ada setelah logout |
| Pembayaran | Status diubah admin | Unggah bukti transfer, verifikasi terima/tolak dengan alasan |
| Pengiriman | Hanya status | Tabel `pengiriman`: kurir, resi, petugas, waktu kirim dan diterima |
| URL | ID angka (`/produk/3`) | Slug (`/produk/kaos-polos-cotton-combed`) dan kode pesanan (`/pesanan/SE-261110-9H84`) |
| Pembuatan URL di view | `site_url('...')` | Named route: `url_to('pesanan.detail', $kode)` |
| Akses ditolak | Redirect dengan pesan | Halaman 403 dengan status HTTP 403, URL tidak berubah |
| Tampilan | Bootstrap 4 + SB Admin 2 | Bootstrap 5 dengan layout toko dan layout panel sendiri |

## Struktur Project

```
Week-10/
├── README.md
├── run.sh                       # up | start | stop | reset
├── docker/
│   ├── Dockerfile               # php:8.3-apache + intl, mysqli, gd, mod_rewrite
│   └── init.sql                 # database ci_shopease + user pbkk
├── img/                         # screenshot hasil
└── shopease/
    ├── env.docker               # disalin run.sh menjadi .env
    ├── app/
    │   ├── Config/
    │   │   ├── Routes.php       # semua route, group per role, named route
    │   │   ├── Filters.php      # alias auth dan tamu, CSRF global
    │   │   └── Pager.php        # template paginasi 'bs5'
    │   ├── Controllers/
    │   │   ├── Beranda.php      # beranda, tentang, kontak, halaman 404
    │   │   ├── Katalog.php      # katalog dan detail produk
    │   │   ├── Auth.php         # masuk, daftar, keluar
    │   │   ├── Keranjang.php    # tambah, ubah jumlah, hapus
    │   │   ├── Checkout.php     # ringkasan, simpan alamat, buat pesanan
    │   │   ├── Pesanan.php      # riwayat, detail, unggah bukti, batal, diterima
    │   │   ├── Bukti.php        # menampilkan bukti transfer kepada yang berhak
    │   │   └── Panel/           # Dashboard, Kategori, Produk, Pesanan, Pembayaran, Laporan
    │   ├── Filters/
    │   │   ├── AuthFilter.php   # cek login dan role, 403 jika role tidak cocok
    │   │   └── GuestFilter.php  # halaman masuk/daftar hanya untuk tamu
    │   ├── Libraries/AlurPesanan.php   # checkout dan semua perpindahan status pesanan
    │   ├── Models/              # 9 model + trait PunyaSlug
    │   ├── Helpers/app_helper.php      # rupiah(), tanggal(), badge_status(), menu_aktif(), ...
    │   ├── Database/
    │   │   ├── Migrations/      # 9 tabel dengan foreign key
    │   │   └── Seeds/           # ShopEaseSeeder + img/ (gambar produk contoh)
    │   └── Views/
    │       ├── layouts/         # toko.php, panel.php, _head, _navbar, _footer, _alerts, _scripts
    │       ├── komponen/        # kartu_produk, item_pesanan, langkah, pager
    │       ├── toko/ auth/ keranjang/ checkout/ pesanan/
    │       ├── panel/           # dashboard, kategori/, produk/, pesanan/, pembayaran/, laporan/
    │       └── errors/          # terlarang.php (403), tidak_ditemukan.php (404)
    ├── public/
    │   ├── css/shopease.css
    │   ├── img/tanpa-gambar.svg
    │   └── uploads/produk/      # gambar produk (publik)
    └── writable/uploads/bukti/  # bukti transfer (tidak bisa diakses langsung)
```

Alasan pembagiannya:

- **Controller per tanggung jawab.** Keranjang, checkout, dan pesanan dipisah karena masing-masing punya aturan sendiri. Checkout punya transaksi database, pesanan punya aturan kepemilikan.
- **Namespace `Panel`.** Semua halaman admin dan gudang ada di `Controllers/Panel/` dan URL `/panel/...`, jadi hak aksesnya cukup dipasang sekali di route group.
- **`Libraries/AlurPesanan.php`.** Status pesanan diubah dari lima tempat (checkout, unggah bukti, verifikasi, input resi, konfirmasi diterima). Semua perpindahan status dikumpulkan di satu kelas supaya aturan dan transaksinya tidak tersebar di banyak controller.
- **Bukti transfer di `writable/`.** Berbeda dengan gambar produk, bukti transfer berisi data pribadi. File disimpan di luar `public/` dan hanya bisa dibuka lewat route `bukti/(:segment)` yang memeriksa pemiliknya.

## Bagian 1: Menjalankan Aplikasi

### 1. Database

Ada sembilan tabel. `pesanan_item` menyimpan nama dan harga produk saat dibeli (snapshot), dan `pesanan` menyimpan salinan alamat, jadi perubahan harga produk atau alamat pelanggan tidak mengubah pesanan lama.

```
users    1 ── N alamat         (CASCADE)
users    1 ── N keranjang N ── 1 produk   (CASCADE, UNIQUE user_id + produk_id)
kategori 1 ── N produk         (RESTRICT: kategori berisi produk tidak bisa dihapus)
users    1 ── N pesanan        (RESTRICT)
pesanan  1 ── N pesanan_item N ── 1 produk   (RESTRICT: produk yang pernah dipesan tidak bisa dihapus)
pesanan  1 ── 1 pembayaran     (diverifikasi_oleh -> users, SET NULL)
pesanan  1 ── 1 pengiriman     (diinput_oleh -> users, SET NULL)
```

```php
// app/Database/Migrations/2026-11-10-000001_CreateShopEaseTables.php
$this->forge->addUniqueKey(['user_id', 'produk_id']);          // satu baris keranjang per produk
$this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'CASCADE');
$this->forge->createTable('keranjang');
// ...
// RESTRICT: produk yang pernah dipesan tidak bisa dihapus (cukup dinonaktifkan)
$this->forge->addForeignKey('produk_id', 'produk', 'id', 'CASCADE', 'RESTRICT');
$this->forge->createTable('pesanan_item');
```

### 2. Konfigurasi dan `run.sh`

Lingkungan sama dengan Week 09: image `php:8.3-apache` dan `mariadb:11`, dijalankan dengan `docker run` biasa karena plugin Compose tidak terpasang. Container Week 10 memakai nama dan port sendiri (`pbkk10-app` di port 8090, `pbkk10-db` di port 3310), jadi bisa berjalan bersamaan dengan Week 09.

```ini
# shopease/env.docker -> .env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8090/'
app.indexPage = ''

database.default.hostname = pbkk10-db
database.default.database = ci_shopease
database.default.username = pbkk
database.default.password = pbkk123
database.default.DBDriver = MySQLi
```

```bash
cd Week-10
./run.sh up       # pertama kali: build, composer install, migrate, seed
./run.sh start    # menjalankan lagi
./run.sh stop
./run.sh reset    # hapus database dan file upload, lalu up dari awal
```

```
ShopEase -> http://localhost:8090
```

Di dalam `run.sh`, migration dan seeder dijalankan dengan spark:

```bash
docker exec -u "$UIDGID" pbkk10-app php spark migrate --all
docker exec -u "$UIDGID" pbkk10-app php spark db:seed ShopEaseSeeder
```

Seeder mengisi 6 kategori, 18 produk (2 di antaranya nonaktif), 5 akun, buku alamat, dan 8 pesanan dalam 7 hari terakhir dengan status berbeda-beda. Pesanan contoh dibuat lewat `AlurPesanan` yang sama dengan yang dipakai aplikasi, jadi stok, pembayaran, dan pengirimannya konsisten. Bukti transfer contoh digambar dengan GD.

Akun untuk mencoba:

| Role | Email | Password |
| :-- | :-- | :-- |
| Admin | `admin@shopease.test` | `admin123` |
| Staf gudang | `gudang@shopease.test` | `gudang123` |
| Pelanggan | `budi@mail.test`, `sari@mail.test`, `andi@mail.test` | `budi1234`, `sari1234`, `andi1234` |

### 3. Kendala dan Solusi

| Kendala | Penyebab | Solusi |
| :-- | :-- | :-- |
| `Cache tidak bisa menulis pada "/var/www/html/writable/cache/"` saat seeding | Folder proyek ada di partisi NTFS (fuseblk). `chmod` tidak berpengaruh, semua file milik UID 1000, sedangkan Apache berjalan sebagai `www-data` (UID 33) | Apache dijalankan dengan UID host (`-e APACHE_RUN_USER="#$(id -u)"`) dan spark dengan `docker exec -u "$(id -u):$(id -g)"`. |
| Checkout dua kali bersamaan bisa membuat dua pesanan dari satu keranjang | Kedua request membaca keranjang sebelum salah satunya mengosongkannya | Baris keranjang dibaca dengan `SELECT ... FOR UPDATE`. Request kedua menunggu, lalu mendapati keranjang sudah kosong. |
| Halaman 403/404 menampilkan path `//panel/produk` | Di CI 4.7 `getUri()->getPath()` sudah diawali `/` | `'/' . ltrim($path, '/')` |
| Pesan "harus berisi nilai yang sudah ada sebelumnya dalam database" saat kategori produk belum dipilih | Dropdown kosong terkirim sebagai `0`, lolos `required` tetapi gagal `is_not_unique` | Pesan khusus "Pilih kategori produk." di `$validationMessages`. |
| Produk yang pernah dipesan tidak bisa dihapus | FK `pesanan_item.produk_id` bersifat `RESTRICT` | Exception ditangkap lalu produk dinonaktifkan: hilang dari katalog, tetapi riwayat pesanan tetap utuh. |

## Bagian 2: Routing

### 1. Prinsip

Tiga aturan yang saya pakai di `app/Config/Routes.php`:

1. **Hak akses ditempel di route group, bukan di controller atau view.** Ada empat group: `tamu` (masuk/daftar), `auth:customer`, `auth:admin,gudang`, dan `auth:admin`. Controller tidak memeriksa role sama sekali.
2. **Setiap route punya nama.** View tidak pernah menulis URL manual. Semua link dan `action` form memakai `url_to('nama.route', ...)`. Jika URL diganti, view tidak perlu diubah.
3. **Aksi yang mengubah data hanya POST.** Tambah keranjang, checkout, unggah bukti, batal, verifikasi, input resi, hapus, dan keluar semuanya `post()`. Request POST selalu membawa token CSRF (filter `csrf` global), dan aksi tidak bisa terpicu lewat link, prefetch browser, atau crawler. Contohnya `GET /keluar` menghasilkan 404.

### 2. `Routes.php`

```php
$routes->set404Override('App\Controllers\Beranda::tidakDitemukan');

// Publik
$routes->get('/', 'Beranda::index', ['as' => 'beranda']);
$routes->get('katalog', 'Katalog::index', ['as' => 'katalog']);
$routes->get('produk/(:segment)', 'Katalog::detail/$1', ['as' => 'produk.detail']);

// Tamu (belum login)
$routes->group('', ['filter' => 'tamu'], static function (RouteCollection $routes): void {
    $routes->get('masuk', 'Auth::masuk', ['as' => 'masuk']);
    $routes->post('masuk', 'Auth::prosesMasuk', ['as' => 'masuk.proses']);
    // daftar ...
});
$routes->post('keluar', 'Auth::keluar', ['as' => 'keluar', 'filter' => 'auth']);

// Pelanggan
$routes->group('', ['filter' => 'auth:customer'], static function (RouteCollection $routes): void {
    $routes->get('keranjang', 'Keranjang::index', ['as' => 'keranjang']);
    $routes->post('keranjang', 'Keranjang::tambah', ['as' => 'keranjang.tambah']);
    $routes->post('keranjang/(:num)/hapus', 'Keranjang::hapus/$1', ['as' => 'keranjang.hapus']);
    $routes->post('checkout', 'Checkout::proses', ['as' => 'checkout.proses']);
    $routes->get('pesanan/(:segment)', 'Pesanan::detail/$1', ['as' => 'pesanan.detail']);
    $routes->post('pesanan/(:segment)/bayar', 'Pesanan::bayar/$1', ['as' => 'pesanan.bayar']);
    // ...
});

// Panel: admin dan staf gudang
$routes->group('panel', ['namespace' => 'App\Controllers\Panel', 'filter' => 'auth:admin,gudang'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Dashboard::index', ['as' => 'panel']);
    $routes->get('pesanan/(:num)', 'Pesanan::detail/$1', ['as' => 'panel.pesanan.detail']);
    $routes->post('pesanan/(:num)/kirim', 'Pesanan::kirim/$1', ['as' => 'panel.pesanan.kirim']);
    // ...
});

// Panel: khusus admin. CRUD kategori dan produk dibuat dengan perulangan karena polanya sama.
$routes->group('panel', ['namespace' => 'App\Controllers\Panel', 'filter' => 'auth:admin'], static function (RouteCollection $routes): void {
    foreach (['kategori' => 'Kategori', 'produk' => 'Produk'] as $uri => $controller) {
        $routes->get($uri, "{$controller}::index", ['as' => "panel.{$uri}"]);
        $routes->get("{$uri}/baru", "{$controller}::baru", ['as' => "panel.{$uri}.baru"]);
        $routes->post($uri, "{$controller}::simpan", ['as' => "panel.{$uri}.simpan"]);
        $routes->get("{$uri}/(:num)/ubah", "{$controller}::ubah/$1", ['as' => "panel.{$uri}.ubah"]);
        $routes->post("{$uri}/(:num)", "{$controller}::perbarui/$1", ['as' => "panel.{$uri}.perbarui"]);
        $routes->post("{$uri}/(:num)/hapus", "{$controller}::hapus/$1", ['as' => "panel.{$uri}.hapus"]);
    }
    $routes->post('pembayaran/(:num)/terima', 'Pembayaran::terima/$1', ['as' => 'panel.pembayaran.terima']);
    $routes->get('laporan/csv', 'Laporan::csv', ['as' => 'panel.laporan.csv']);
    // ...
});
```

Penjelasan:

- `auth:admin,gudang`: argumen filter dipisah koma dan diterima `AuthFilter` sebagai array role yang diizinkan.
- Prefix `panel` dipakai dua group dengan filter berbeda. Staf gudang bisa membuka `/panel/pesanan`, tetapi `/panel/produk` menghasilkan 403.
- `(:num)` hanya menerima angka (ID di panel). `(:segment)` menerima satu segmen apa pun, dipakai untuk slug produk dan kode pesanan.
- Pelanggan membuka pesanan lewat kode (`/pesanan/SE-261110-9H84`), bukan ID. ID berurutan mudah ditebak, sedangkan kode memakai 4 karakter acak. Walaupun begitu, controller tetap mengecek pemiliknya.
- `set404Override` mengarahkan route yang tidak ada, dan `PageNotFoundException` dari controller, ke `Beranda::tidakDitemukan` yang memakai layout toko.

### 3. Peta Route

| Method | URL | Nama route | Controller::method | Filter | Hasil |
| :-- | :-- | :-- | :-- | :-- | :-- |
| GET | `/` | `beranda` | `Beranda::index` | - | Kategori, produk terlaris, produk terbaru |
| GET | `/tentang`, `/kontak` | `tentang`, `kontak` | `Beranda::tentang`, `::kontak` | - | Halaman statis |
| GET | `/katalog` | `katalog` | `Katalog::index` | - | `?q=`, `?kategori=`, `?urut=`, `?page=` |
| GET | `/produk/{slug}` | `produk.detail` | `Katalog::detail/$1` | - | Produk nonaktif menghasilkan 404 |
| GET/POST | `/masuk` | `masuk`, `masuk.proses` | `Auth::masuk`, `::prosesMasuk` | `tamu` | Login, kembali ke halaman yang tadi diminta |
| GET/POST | `/daftar` | `daftar`, `daftar.proses` | `Auth::daftar`, `::prosesDaftar` | `tamu` | Registrasi, role selalu `customer` |
| POST | `/keluar` | `keluar` | `Auth::keluar` | `auth` | Hapus session |
| GET | `/bukti/{file}` | `bukti` | `Bukti::lihat/$1` | `auth` | Gambar bukti, hanya pemilik/admin/gudang |
| GET | `/keranjang` | `keranjang` | `Keranjang::index` | `auth:customer` | Isi keranjang + ongkir |
| POST | `/keranjang` | `keranjang.tambah` | `Keranjang::tambah` | `auth:customer` | Tambah produk (cek aktif dan stok) |
| POST | `/keranjang/ubah` | `keranjang.ubah` | `Keranjang::ubah` | `auth:customer` | Ubah jumlah beberapa baris sekaligus |
| POST | `/keranjang/{id}/hapus` | `keranjang.hapus` | `Keranjang::hapus/$1` | `auth:customer` | Hapus baris milik sendiri |
| GET | `/checkout` | `checkout` | `Checkout::index` | `auth:customer` | Pilih alamat, ringkasan |
| POST | `/checkout/alamat` | `checkout.alamat` | `Checkout::simpanAlamat` | `auth:customer` | Tambah alamat ke buku alamat |
| POST | `/checkout` | `checkout.proses` | `Checkout::proses` | `auth:customer` | Buat pesanan dalam satu transaksi |
| GET | `/pesanan` | `pesanan` | `Pesanan::index` | `auth:customer` | Riwayat, filter status |
| GET | `/pesanan/{kode}` | `pesanan.detail` | `Pesanan::detail/$1` | `auth:customer` | Pesanan orang lain menghasilkan 404 |
| POST | `/pesanan/{kode}/bayar` | `pesanan.bayar` | `Pesanan::bayar/$1` | `auth:customer` | Unggah bukti transfer |
| POST | `/pesanan/{kode}/batal` | `pesanan.batal` | `Pesanan::batal/$1` | `auth:customer` | Batal selama belum dibayar, stok kembali |
| POST | `/pesanan/{kode}/diterima` | `pesanan.diterima` | `Pesanan::diterima/$1` | `auth:customer` | Dikirim menjadi Selesai |
| GET | `/panel` | `panel` | `Panel\Dashboard::index` | `auth:admin,gudang` | Statistik, grafik 7 hari |
| GET | `/panel/pesanan` | `panel.pesanan` | `Panel\Pesanan::index` | `auth:admin,gudang` | Filter status, cari kode/nama |
| GET | `/panel/pesanan/{id}` | `panel.pesanan.detail` | `Panel\Pesanan::detail/$1` | `auth:admin,gudang` | Item, bukti, form resi |
| POST | `/panel/pesanan/{id}/kirim` | `panel.pesanan.kirim` | `Panel\Pesanan::kirim/$1` | `auth:admin,gudang` | Simpan kurir + resi, status Dikirim |
| POST | `/panel/pesanan/{id}/selesai` | `panel.pesanan.selesai` | `Panel\Pesanan::selesai/$1` | `auth:admin,gudang` | Status Selesai |
| POST | `/panel/pesanan/{id}/batal` | `panel.pesanan.batal` | `Panel\Pesanan::batal/$1` | `auth:admin` | Batal, stok kembali |
| GET, POST | `/panel/kategori[/baru, /{id}/ubah, /{id}, /{id}/hapus]` | `panel.kategori.*` | `Panel\Kategori` | `auth:admin` | CRUD kategori |
| GET, POST | `/panel/produk[/baru, /{id}/ubah, /{id}, /{id}/hapus]` | `panel.produk.*` | `Panel\Produk` | `auth:admin` | CRUD produk + upload gambar |
| GET | `/panel/pembayaran` | `panel.pembayaran` | `Panel\Pembayaran::index` | `auth:admin` | Antrean bukti + riwayat verifikasi |
| POST | `/panel/pembayaran/{id}/terima` | `panel.pembayaran.terima` | `Panel\Pembayaran::terima/$1` | `auth:admin` | Status Diproses |
| POST | `/panel/pembayaran/{id}/tolak` | `panel.pembayaran.tolak` | `Panel\Pembayaran::tolak/$1` | `auth:admin` | Wajib alasan, kembali Menunggu Pembayaran |
| GET | `/panel/laporan` | `panel.laporan` | `Panel\Laporan::index` | `auth:admin` | Periode + status, ringkasan, per kategori |
| GET | `/panel/laporan/csv` | `panel.laporan.csv` | `Panel\Laporan::csv` | `auth:admin` | Unduh CSV dengan filter yang sama |

Daftar lengkap dari `php spark routes` ada di [Gambar 47](#hasil-foto).

### 4. Filter

`AuthFilter` dijalankan sebelum controller. Tamu diarahkan ke `/masuk` dan URL yang diminta disimpan 5 menit, jadi setelah login pelanggan langsung kembali ke halaman itu. User yang sudah login tetapi role-nya tidak cocok mendapat halaman 403 dengan URL yang tetap sama:

```php
public function before(RequestInterface $request, $arguments = null)
{
    $session = session();

    if (! $session->get('logged_in')) {
        if ($request->getMethod() === 'GET') {
            $session->setTempdata('tujuan', current_url(true)->__toString(), 300);
        }

        return redirect()->route('masuk')->with('info', 'Silakan masuk terlebih dahulu untuk melanjutkan.');
    }

    if (! empty($arguments) && ! in_array($session->get('role'), $arguments, true)) {
        return service('response')->setStatusCode(403)->setBody(view('errors/terlarang', [
            'title' => 'Akses Ditolak', 'path' => '/' . ltrim($request->getUri()->getPath(), '/'), 'diizinkan' => $arguments,
        ]));
    }
}
```

`GuestFilter` (alias `tamu`) melakukan kebalikannya: user yang sudah login dan membuka `/masuk` diarahkan ke beranda (pelanggan) atau `/panel` (admin dan gudang).

![Staf gudang membuka /panel/produk, hasilnya 403](./img/w10-29-gudang-403.png)
*Staf gudang membuka `/panel/produk`. Status HTTP 403 dan URL tidak berubah.*

## Bagian 3: Controller

### 1. Peta Controller

| Kelompok | Controller | Method |
| :-- | :-- | :-- |
| Publik | `Beranda` | `index`, `tentang`, `kontak`, `tidakDitemukan` |
| Publik | `Katalog` | `index`, `detail` |
| Auth | `Auth` | `masuk`, `prosesMasuk`, `daftar`, `prosesDaftar`, `keluar` |
| Pelanggan | `Keranjang` | `index`, `tambah`, `ubah`, `hapus` |
| Pelanggan | `Checkout` | `index`, `simpanAlamat`, `proses` |
| Pelanggan | `Pesanan` | `index`, `detail`, `bayar`, `batal`, `diterima` |
| Login | `Bukti` | `lihat` |
| Panel | `Panel\Dashboard` | `index` |
| Panel | `Panel\Kategori`, `Panel\Produk` | `index`, `baru`, `simpan`, `ubah`, `perbarui`, `hapus` |
| Panel | `Panel\Pesanan` | `index`, `detail`, `kirim`, `selesai`, `batal` |
| Panel | `Panel\Pembayaran` | `index`, `terima`, `tolak` |
| Panel | `Panel\Laporan` | `index`, `csv` |

Pembagian kerjanya: controller membaca input, memvalidasi, memanggil model atau `AlurPesanan`, lalu memilih view atau redirect. Query ada di model (misalnya `ProdukModel::cari()` dipakai katalog dan panel produk), dan perubahan status pesanan ada di `AlurPesanan`. Bagian yang sama di CRUD kategori dan produk (cari-atau-404, kembali ke form dengan error, redirect ke daftar) dikumpulkan di `Panel/PanelController.php`.

### 2. Keranjang: Stok Dicek Setelah Digabung

```php
public function tambah()
{
    $produkId = (int) $this->request->getPost('produk_id');
    $qty      = (int) $this->request->getPost('qty');
    if ($qty < 1) {
        return redirect()->back()->with('error', 'Jumlah minimal 1.');
    }

    // Produk nonaktif ditolak walaupun ID-nya dikirim manual
    $produk = model(ProdukModel::class)->aktif($produkId);
    if ($produk === null) {
        return redirect()->back()->with('error', 'Produk tidak tersedia.');
    }

    // Stok dicek terhadap jumlah SETELAH digabung dengan isi keranjang
    $ada         = $this->keranjang->baris(user('id'), $produkId);
    $diKeranjang = (int) ($ada['qty'] ?? 0);
    if ($diKeranjang + $qty > $produk['stok']) {
        // "Stok ... tidak mencukupi (tersisa 3). Di keranjang sudah ada 2, jadi maksimal tambah 1 lagi."
        return redirect()->back()->with('error', $pesan);
    }

    $ada === null
        ? $this->keranjang->insert(['user_id' => user('id'), 'produk_id' => $produkId, 'qty' => $qty])
        : $this->keranjang->update($ada['id'], ['qty' => $diKeranjang + $qty]);

    return redirect()->to(url_to('keranjang'))->with('success', "{$produk['nama']} ditambahkan ke keranjang.");
}
```

- Harga tidak pernah dibaca dari form. `KeranjangModel::isi()` mengambil harga, stok, dan status aktif terbaru dari tabel `produk`, jadi parameter `harga` yang disisipkan ke form diabaikan.
- Jika stok produk turun atau produk dinonaktifkan setelah masuk keranjang, baris itu ditandai kuning dan tombol checkout dimatikan.
- Ubah jumlah dan hapus selalu memakai syarat `user_id`, jadi ID baris keranjang orang lain tidak bisa diubah.

### 3. Checkout dalam Satu Transaksi

`Checkout::proses()` hanya memvalidasi `alamat_id` lalu memanggil `AlurPesanan::checkout()`:

```php
public function checkout(int $userId, int $alamatId, ?string $catatan): array
{
    return $this->transaksi(function () use ($userId, $alamatId, $catatan): array {
        $alamat = model(AlamatModel::class)->where(['id' => $alamatId, 'user_id' => $userId])->first()
            ?? throw new RuntimeException('Pilih alamat pengiriman terlebih dahulu.');

        // FOR UPDATE: checkout kedua yang datang bersamaan menunggu, lalu mendapati keranjang sudah kosong
        $keranjang = $this->db->query('SELECT produk_id, qty FROM keranjang WHERE user_id = ? ORDER BY id FOR UPDATE', [$userId])->getResultArray();
        if ($keranjang === []) {
            throw new RuntimeException('Keranjang kosong.');
        }

        foreach ($keranjang as $k) {
            $p = $this->db->query('SELECT id, nama, harga, stok, aktif FROM produk WHERE id = ? FOR UPDATE', [$k['produk_id']])->getRowArray();
            if (! $p['aktif']) {
                throw new RuntimeException("Produk \"{$p['nama']}\" sudah tidak dijual. ...");
            }
            if ($p['stok'] < $k['qty']) {
                throw new RuntimeException("Stok \"{$p['nama']}\" tidak mencukupi (tersisa {$p['stok']}).");
            }
            $baris[]   = ['produk_id' => $p['id'], 'nama_produk' => $p['nama'], 'harga' => $p['harga'], 'qty' => $k['qty'], ...];
            $subtotal += $p['harga'] * $k['qty'];
        }

        $ongkir = self::ongkir($subtotal);   // Rp15.000, gratis mulai Rp300.000
        $id     = model(PesananModel::class)->insert([... 'kode' => $this->kodeBaru(), 'status' => 'menunggu_bayar'], true);

        foreach ($baris as $b) {
            model(PesananItemModel::class)->insert($b + ['pesanan_id' => $id]);
            $this->db->query('UPDATE produk SET stok = stok - ? WHERE id = ?', [$b['qty'], $b['produk_id']]);
        }
        model(PembayaranModel::class)->insert(['pesanan_id' => $id, 'jumlah' => $subtotal + $ongkir, 'status' => 'belum']);
        model(KeranjangModel::class)->where('user_id', $userId)->delete();

        return ['id' => $id, 'kode' => $kode];
    });
}
```

Urutannya:

1. Pastikan alamat milik user sendiri.
2. Kunci baris keranjang (`FOR UPDATE`). Ini mencegah checkout ganda jika tombol ditekan dua kali atau dua tab mengirim bersamaan.
3. Kunci setiap baris produk, tolak jika produk nonaktif atau stok kurang.
4. Hitung ulang subtotal dari harga di database, tambahkan ongkir.
5. Simpan `pesanan` (kode `SE-yymmdd-XXXX`), `pesanan_item` (snapshot nama dan harga), kurangi stok.
6. Buat baris `pembayaran` berstatus `belum`, kosongkan keranjang.

Jika satu langkah gagal, `transaksi()` memanggil `transRollback()`: tidak ada pesanan yang setengah jadi, stok tidak berkurang, dan keranjang tetap utuh. Controller menangkap `RuntimeException` dan menampilkan pesannya di halaman keranjang.

![Checkout: pilih alamat dan ringkasan](./img/w10-13-checkout.png)
*Halaman checkout. Total di atas Rp300.000 sehingga ongkir gratis.*

### 4. Alur Status Pesanan

Semua perpindahan status lewat `AlurPesanan::pindah()`, yang mengunci baris pesanan lalu mencocokkannya dengan tabel `ALUR`:

```php
public const ALUR = [
    'menunggu_bayar'      => ['menunggu_verifikasi', 'batal'],
    'menunggu_verifikasi' => ['diproses', 'menunggu_bayar', 'batal'],
    'diproses'            => ['dikirim', 'batal'],
    'dikirim'             => ['selesai'],
    'selesai'             => [],
    'batal'               => [],
];

private function pindah(int $pesananId, string $ke): void
{
    $p = $this->db->query('SELECT status FROM pesanan WHERE id = ? FOR UPDATE', [$pesananId])->getRowArray()
        ?? throw new RuntimeException('Pesanan tidak ditemukan.');

    if (! in_array($ke, self::ALUR[$p['status']], true)) {
        throw new RuntimeException("Status pesanan tidak bisa diubah dari \"{$dari}\" ke \"{$tujuan}\".");
    }

    model(PesananModel::class)->update($pesananId, ['status' => $ke]);
}
```

| Aksi | Siapa | Method | Perubahan |
| :-- | :-- | :-- | :-- |
| Checkout | Pelanggan | `checkout()` | Pesanan baru `menunggu_bayar`, pembayaran `belum` |
| Unggah bukti | Pelanggan | `unggahBukti()` | `menunggu_verifikasi`, pembayaran `menunggu` |
| Terima bukti | Admin | `verifikasi(..., true)` | `diproses`, pembayaran `diterima` + `diverifikasi_oleh` |
| Tolak bukti | Admin | `verifikasi(..., false, $alasan)` | Kembali `menunggu_bayar`, pembayaran `ditolak` + alasan |
| Input resi | Admin / gudang | `kirim()` | `dikirim`, baris `pengiriman` baru |
| Barang sampai | Pelanggan / admin / gudang | `selesai()` | `selesai`, `pengiriman.diterima_at` diisi |
| Batal | Pelanggan (belum bayar) / admin | `batal()` | `batal`, stok dikembalikan di transaksi yang sama |

Karena aturannya di satu tempat, tombol di view cukup mengikuti tabel yang sama. Contohnya tombol **Batalkan pesanan** di panel hanya muncul jika `batal` ada di `ALUR[$status]`. Jika request dikirim manual dengan status yang salah, `pindah()` tetap menolaknya.

![Detail pesanan di panel saat berstatus Diproses](./img/w10-26-detail-pesanan-diproses.png)
*Detail pesanan di panel. Form resi muncul karena pembayaran sudah diterima.*

### 5. Unggah Bukti Transfer

```php
public function bayar(string $kode)
{
    $p = $this->milikSendiri($kode);   // pesanan orang lain -> 404

    $rules = [
        'bank_pengirim' => ['label' => 'Bank pengirim', 'rules' => 'required|max_length[40]'],
        'nama_rekening' => ['label' => 'Nama pemilik rekening', 'rules' => 'required|min_length[3]|max_length[100]'],
        'bukti'         => ['label' => 'Bukti transfer',
            'rules' => 'uploaded[bukti]|is_image[bukti]|mime_in[bukti,image/jpeg,image/png,image/webp]|max_size[bukti,2048]'],
    ];
    if (! $this->validate($rules, $pesan)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $nama = $file->getRandomName();
    $file->move(WRITEPATH . 'uploads/bukti/', $nama);   // di luar public/

    try {
        (new AlurPesanan())->unggahBukti($p['id'], [...]);
    } catch (RuntimeException $e) {
        unlink(self::BUKTI_DIR . $nama);   // status tidak sah: file baru dibuang lagi
        return redirect()->back()->with('error', $e->getMessage());
    }
    // bukti lama yang pernah ditolak dihapus
}
```

Bukti ditampilkan lewat `Bukti::lihat()`. Controller mencari pembayaran dengan nama file itu, lalu mengizinkan admin dan gudang, atau pelanggan pemilik pesanan. Selain itu hasilnya 404. Tamu diarahkan ke halaman masuk oleh filter `auth`.

![Admin memeriksa antrean bukti transfer](./img/w10-25-verifikasi-pembayaran.png)
*Antrean verifikasi diurutkan dari bukti yang paling lama menunggu. Penolakan wajib menyertakan alasan.*

### 6. CRUD Produk dengan Upload Gambar

Pola CRUD sama dengan Week 09. Yang baru:

- **Slug otomatis.** Trait `PunyaSlug` dipasang di `beforeInsert`/`beforeUpdate` model kategori dan produk. Nama "Kaos Polos" menjadi `kaos-polos`, dan jika sudah dipakai menjadi `kaos-polos-2`.
- **Status aktif.** Produk bisa disembunyikan dari katalog tanpa dihapus.
- **Hapus produk yang pernah dipesan** tidak menghasilkan error. FK `RESTRICT` membuat `DELETE` gagal, lalu exception ditangkap dan produk dinonaktifkan:

```php
public function hapus(int $id)
{
    $row = $this->cariAtau404($id);

    try {
        $this->model->delete($id);
    } catch (DatabaseException) {
        $this->model->update($id, ['aktif' => 0]);

        return $this->keDaftar('warning', "Produk {$row['nama']} sudah pernah dipesan, jadi tidak dihapus melainkan dinonaktifkan.");
    }

    $this->hapusGambar($row['gambar']);

    return $this->keDaftar('success', "Produk {$row['nama']} dihapus.");
}
```

![Form tambah produk dengan pratinjau gambar](./img/w10-22-form-produk-upload.png)
*Form tambah produk. Gambar dipratinjau sebelum diunggah.*

### 7. Dashboard dan Laporan

Dashboard menghitung omzet bulan ini, jumlah bukti yang menunggu, pesanan siap kirim, jumlah pelanggan, grafik omzet 7 hari (Chart.js), jumlah pesanan per status, produk terlaris, dan stok yang menipis. `PesananModel::omzetHarian()` mengisi hari tanpa penjualan dengan 0 supaya grafik tetap 7 batang.

Laporan memakai filter periode (default awal bulan sampai hari ini) dan status. Omzet hanya menghitung pesanan `diproses`, `dikirim`, dan `selesai` (`PesananModel::SUDAH_DIBAYAR`), dan penjualan juga dikelompokkan per kategori. Tombol **Unduh CSV** memanggil `Laporan::csv()` dengan filter yang sama dan mengembalikan file lewat `$this->response->download()`. Pemisah kolomnya `;` supaya langsung terbaca di Excel berbahasa Indonesia.

![Laporan penjualan](./img/w10-32-laporan.png)
*Laporan penjualan periode 1–10 November 2026.*

## Bagian 4: View

### 1. Dua Layout

Halaman toko memakai `layouts/toko.php`, dan halaman panel memakai `layouts/panel.php`. Bagian `<head>` dan script disimpan di partial terpisah supaya kedua layout memakai CSS dan JS yang sama.

```php
<!-- app/Views/layouts/toko.php -->
<!doctype html>
<html lang="id">
<head>
    <?= $this->include('layouts/_head') ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?= $this->include('layouts/_navbar') ?>

<main class="flex-grow-1 py-4">
    <div class="container">
        <?= $this->include('layouts/_alerts') ?>
        <?= $this->renderSection('content') ?>
    </div>
</main>

<?= $this->include('layouts/_footer') ?>
<?= $this->include('layouts/_scripts') ?>
<?= $this->renderSection('scripts') ?>
</body>
</html>
```

Layout panel punya sidebar dan topbar, plus dua section tambahan: `subjudul` di bawah judul halaman dan `aksi` untuk tombol di kanan atas. Menu sidebar mengikuti role: staf gudang hanya melihat Dashboard dan Pesanan.

```php
<!-- app/Views/panel/produk/index.php -->
<?= $this->extend('layouts/panel') ?>

<?= $this->section('aksi') ?>
<a href="<?= url_to('panel.produk.baru') ?>" class="btn btn-primary btn-sm">Tambah</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
  ... form filter, tabel produk, $pager->links('default', 'bs5') ...
<?= $this->endSection() ?>
```

| Section | Layout | Isi |
| :-- | :-- | :-- |
| `content` | toko, panel | Isi halaman |
| `scripts` | toko, panel | JS khusus halaman (pratinjau gambar, grafik Chart.js) |
| `subjudul` | panel | Teks kecil di bawah judul, misalnya nama pelanggan dan status |
| `aksi` | panel | Tombol di topbar: Tambah, Unduh CSV, kembali ke daftar |

### 2. Partial dan Komponen

| File | Dipakai di | Isi |
| :-- | :-- | :-- |
| `layouts/_head.php` | kedua layout | Meta, judul `{title} · ShopEase`, font, Bootstrap, ikon, CSS |
| `layouts/_navbar.php` | toko | Menu, pencarian, ikon keranjang dengan jumlah barang, menu akun |
| `layouts/_footer.php` | toko | Tautan, info gratis ongkir, nama dan NRP |
| `layouts/_alerts.php` | kedua layout | Flash `success`/`info`/`warning`/`error` dan daftar error validasi |
| `layouts/_scripts.php` | kedua layout | Bootstrap JS, konfirmasi untuk form ber-`data-confirm` |
| `komponen/kartu_produk.php` | beranda, katalog, detail | Gambar, kategori, nama, harga, label stok/terjual |
| `komponen/item_pesanan.php` | detail pesanan pelanggan dan panel | Tabel item, subtotal, ongkir, total |
| `komponen/langkah.php` | detail pesanan pelanggan dan panel | Progres Bayar, Verifikasi, Diproses, Dikirim, Selesai |
| `komponen/pager.php` | semua daftar | Paginasi Bootstrap 5, didaftarkan sebagai `bs5` di `Config/Pager.php` |
| `errors/terlarang.php`, `errors/tidak_ditemukan.php` | filter, 404 override | Halaman 403 dan 404 dengan layout toko |

Komponen dipanggil dengan `view()` dan data yang dibutuhkan saja:

```php
<?php foreach ($produk as $p): ?>
    <div class="col"><?= view('komponen/kartu_produk', ['p' => $p]) ?></div>
<?php endforeach ?>
```

### 3. Data dari Controller ke View

Controller mengirim satu array ke `view()`. View tidak memanggil model dan tidak menjalankan query. Contohnya `Pesanan::detail()` mengirim empat variabel, dan view hanya memilih cara menampilkannya sesuai status:

```php
return view('pesanan/detail', [
    'title'      => 'Pesanan ' . $p['kode'],
    'pesanan'    => $p,
    'items'      => model(PesananItemModel::class)->untuk($p['id']),
    'pembayaran' => model(PembayaranModel::class)->untuk($p['id']),
    'pengiriman' => model(PengirimanModel::class)->untuk($p['id']),
]);
```

| Status pesanan | Yang tampil di `pesanan/detail.php` |
| :-- | :-- |
| `menunggu_bayar` | Nomor rekening, form unggah bukti (dengan alasan jika sebelumnya ditolak), tombol batal |
| `menunggu_verifikasi` | Data transfer dan gambar bukti |
| `dikirim` | Kurir, nomor resi, tombol **Pesanan Diterima** |
| `selesai` | Waktu kirim dan waktu diterima |

Format yang dipakai berulang ada di `app_helper.php`, misalnya `rupiah()`, `tanggal()` (format Indonesia: 10 Nov 2026, 19.26), `badge_status()`, dan `menu_aktif()` untuk menandai menu yang sedang dibuka.

## Pembagian Tugas

Tugas ini dikerjakan secara individu:

| Anggota | Area | Pekerjaan |
| :-- | :-- | :-- |
| Hosea (5025241177) | Lingkungan | Dockerfile, `run.sh`, konfigurasi `.env`, penyesuaian UID untuk partisi NTFS |
| Hosea (5025241177) | Database | Desain 9 tabel, migration, seeder dengan data pesanan 7 hari |
| Hosea (5025241177) | Routing dan filter | Route group per role, named route, `AuthFilter`, `GuestFilter`, halaman 403/404 |
| Hosea (5025241177) | Controller dan model | Katalog, keranjang, checkout, `AlurPesanan`, pembayaran, pengiriman, CRUD, laporan |
| Hosea (5025241177) | View | Layout toko dan panel, partial, komponen, CSS |
| Hosea (5025241177) | Pengujian dan dokumentasi | Skenario `curl`, screenshot, laporan |

## Alur Data

Contoh lengkap: Budi membeli 1 Keyboard Mekanikal TKL (Rp459.000) dan 2 Mouse Wireless Silent (Rp129.000).

| # | Request | Controller | Yang terjadi di database |
| :-: | :-- | :-- | :-- |
| 1 | `GET /keranjang` (belum login) | `AuthFilter` | Tidak ada. Diarahkan ke `/masuk`, URL tujuan disimpan di tempdata |
| 2 | `POST /masuk` | `Auth::prosesMasuk` | Session baru (`regenerate`), kembali ke `/keranjang` |
| 3 | `POST /keranjang` | `Keranjang::tambah` | `keranjang`: baris keyboard qty 1 |
| 4 | `POST /keranjang` | `Keranjang::tambah` | `keranjang`: baris mouse qty 2 |
| 5 | `POST /keranjang` (power bank 5, stok 3) | `Keranjang::tambah` | Ditolak, tidak ada perubahan |
| 6 | `GET /checkout` | `Checkout::index` | Baca `keranjang` + `produk` + `alamat`. Subtotal Rp717.000, ongkir gratis |
| 7 | `POST /checkout` | `Checkout::proses` → `AlurPesanan::checkout` | Satu transaksi: `pesanan` (SE-261110-9H84), 2 `pesanan_item`, stok keyboard 9 → 8 dan mouse 24 → 22, `pembayaran` (`belum`), `keranjang` dikosongkan |
| 8 | `POST /pesanan/SE-261110-9H84/bayar` | `Pesanan::bayar` → `unggahBukti` | File di `writable/uploads/bukti/`; `pembayaran` → `menunggu`; `pesanan` → `menunggu_verifikasi` |
| 9 | `POST /panel/pembayaran/9/terima` | `Panel\Pembayaran::terima` → `verifikasi` | `pembayaran` → `diterima`, `diverifikasi_oleh` = admin; `pesanan` → `diproses` |
| 10 | `POST /panel/pesanan/9/kirim` | `Panel\Pesanan::kirim` → `kirim` | `pengiriman`: SiCepat 004589127733, `diinput_oleh` = staf gudang; `pesanan` → `dikirim` |
| 11 | `POST /pesanan/SE-261110-9H84/diterima` | `Pesanan::diterima` → `selesai` | `pesanan` → `selesai`; `pengiriman.diterima_at` diisi |

Satu request POST melewati lapisan berikut:

```
Pelanggan klik "Kirim Bukti Pembayaran" (form_open_multipart + csrf_field)
      │  POST /pesanan/SE-261110-9H84/bayar
      ▼
Filter global csrf  ──(token salah/tidak ada)──► 403
      │
Filter auth:customer (AuthFilter)
      │  belum login ─► /masuk        role lain ─► halaman 403
      ▼
Router: Pesanan::bayar('SE-261110-9H84')
      │  milikSendiri(): pesanan bukan milik user ─► 404
      │  validate(): bank, nama rekening, file gambar ≤ 2 MB ─► kembali ke form + errors
      │  move() ke writable/uploads/bukti/<nama acak>.png
      ▼
AlurPesanan::unggahBukti()   [transBegin]
      │  pindah(): SELECT status ... FOR UPDATE, cek ALUR menunggu_bayar ─► menunggu_verifikasi
      │  UPDATE pembayaran SET status = 'menunggu', bukti = ..., diunggah_at = NOW
      ▼                      [transCommit]
redirect(url_to('pesanan.detail', $kode))->with('success', ...)  ─► layouts/_alerts.php
```

Perubahan status lengkap:

```
menunggu_bayar ──(unggah bukti)──► menunggu_verifikasi ──(admin terima)──► diproses ──(input resi)──► dikirim ──(diterima)──► selesai
      ▲                                    │                                   │
      └──────────(admin tolak + alasan)────┘                                   │
      │                                                                        │
      └──(pelanggan/admin batal)──► batal ◄──────(admin batal)─────────────────┘
```

## Tanggung Jawab Tiap File

| File | Tanggung jawab |
| :-- | :-- |
| `app/Config/Routes.php` | Memetakan URL ke controller, memberi nama route, memasang filter per group |
| `app/Config/Filters.php` | Alias `auth` dan `tamu`, CSRF global |
| `app/Config/Pager.php` | Mendaftarkan template paginasi `bs5` |
| `app/Filters/AuthFilter.php` | Cek login (redirect + ingat tujuan) dan role (403) |
| `app/Filters/GuestFilter.php` | Halaman masuk/daftar hanya untuk tamu |
| `app/Controllers/Beranda.php`, `Katalog.php` | Halaman publik dan halaman 404 |
| `app/Controllers/Auth.php` | Login, registrasi (role selalu `customer`), logout |
| `app/Controllers/Keranjang.php`, `Checkout.php`, `Pesanan.php` | Alur belanja pelanggan |
| `app/Controllers/Bukti.php` | Menampilkan bukti transfer hanya kepada yang berhak |
| `app/Controllers/Panel/*.php` | Dashboard, CRUD kategori dan produk, pesanan, verifikasi, laporan |
| `app/Libraries/AlurPesanan.php` | Checkout dan semua perpindahan status dalam transaksi dengan `FOR UPDATE` |
| `app/Models/ProdukModel.php` | Pencarian, filter, urutan, produk aktif, terlaris |
| `app/Models/PesananModel.php` | Daftar pesanan dengan filter, omzet, omzet harian, penjualan per kategori |
| `app/Models/KeranjangModel.php` | Isi keranjang dengan harga dan stok terbaru, tanda baris bermasalah |
| `app/Models/PunyaSlug.php` | Slug unik otomatis untuk kategori dan produk |
| `app/Models/*.php` lainnya | Akses tabel, validasi berlabel, hash password |
| `app/Helpers/app_helper.php` | `user()`, `rupiah()`, `tanggal()`, `badge_status()`, `invalid()`, `field_error()`, `menu_aktif()` |
| `app/Views/layouts/*.php` | Layout toko dan panel serta partial bersama |
| `app/Views/komponen/*.php` | Kartu produk, tabel item pesanan, progres status, paginasi |
| `app/Database/Migrations`, `Seeds` | Skema 9 tabel dan data contoh |

## Pengujian

Selain dicoba lewat browser, skenario berikut diuji dengan `curl` (membawa cookie session dan token CSRF) pada database yang baru di-reset, lalu isi tabel dicek langsung di MariaDB:

| Skenario | Hasil |
| :-- | :-- |
| POST ke `/keranjang` tanpa token CSRF | 403 |
| `GET /keluar` | 404, logout hanya lewat POST |
| Tambah produk nonaktif dengan mengirim `produk_id` manual | "Produk tidak tersedia." |
| Mengirim `harga=1` bersama form tambah keranjang | Diabaikan, keranjang tetap Rp129.000 |
| Tambah 5 power bank (stok 3) | "Stok Power Bank 10.000 mAh tidak mencukupi (tersisa 3)." |
| Hoodie di keranjang, stoknya diubah jadi 0, lalu `POST /checkout` | "Stok "Hoodie Informatika" tidak mencukupi (tersisa 0).", jumlah pesanan tetap 9 → 9, stok kaos tetap 48, keranjang tetap 2 baris (rollback) |
| Dua `POST /checkout` dikirim bersamaan dari session yang sama | Satu menjadi pesanan, satu kembali ke keranjang ("Keranjang kosong."). Pesanan baru hanya 1 |
| Sari membuka atau membatalkan pesanan Budi | 404 |
| Gudang mengisi resi untuk pesanan yang belum dibayar | "Status pesanan tidak bisa diubah dari "Menunggu Pembayaran" ke "Dikirim"." |
| Gudang mengirim POST ke `/panel/pembayaran/1/terima` | 403 |
| Admin menerima pembayaran yang sama dua kali | Kedua kali: "Pembayaran ini sudah diverifikasi sebelumnya." |
| Tolak pembayaran tanpa alasan | "Bidang Alasan penolakan diperlukan." |
| Membuka bukti transfer: tamu / pemilik / admin / lewat `public/uploads/bukti` | Redirect `/masuk` / 200 / 200 `image/png` / 404 |
| Pelanggan membatalkan pesanan yang sudah dibayar | "Pesanan hanya bisa dibatalkan selama belum dibayar." |
| Admin membatalkan pesanan berisi 3 buku catatan | Status Dibatalkan, stok 57 → 60 |
| Hapus kategori Elektronik | "Kategori Elektronik tidak dapat dihapus karena masih memiliki produk." |
| Hapus produk yang pernah dipesan | Tidak dihapus, `aktif = 0`, detail produknya menjadi 404 |
| Unggah file `.txt` sebagai bukti atau gambar produk | "Bukti transfer harus berupa gambar." / "File harus berupa gambar." |
| Registrasi dengan email terpakai, HP salah, sandi pendek, konfirmasi beda, tanpa centang | Enam pesan error tampil sekaligus di field masing-masing |
| Unduh CSV laporan | `Content-Disposition: attachment; filename="laporan-2026-11-01-2026-11-10.csv"` |

## Kesimpulan

1. Menaruh hak akses di route group membuat controller lebih sederhana. Tidak ada satu pun controller yang memeriksa role, dan `php spark routes` langsung memperlihatkan filter setiap URL.
2. Dengan named route, view tidak menyimpan satu pun URL yang ditulis manual. Semua link dan `action` form memakai `url_to()`, jadi mengganti URL (misalnya prefix `/panel`) cukup dilakukan di `Routes.php`.
3. Halaman 403 dengan status HTTP 403 lebih jelas daripada redirect diam-diam. Pengguna tahu halaman mana yang ditolak dan role apa yang dibutuhkan.
4. Mengumpulkan perpindahan status di `AlurPesanan` mencegah status melompat. Uji input resi untuk pesanan yang belum dibayar dan verifikasi ganda sama-sama ditolak oleh satu fungsi `pindah()`.
5. `SELECT ... FOR UPDATE` di dalam transaksi menangani dua masalah sekaligus: stok tidak bisa minus, dan checkout ganda dari satu keranjang hanya menghasilkan satu pesanan.
6. Layout, section, dan komponen membuat view tetap pendek. Tabel item pesanan dan progres status ditulis sekali lalu dipakai di halaman pelanggan maupun panel.

## Hasil (Foto)

### Halaman Publik

**Gambar 1.** Beranda: kategori, produk terlaris, dan produk terbaru
![Beranda](./img/w10-01-beranda.png)

**Gambar 2.** Halaman tentang
![Tentang](./img/w10-02-tentang.png)

**Gambar 3.** Halaman kontak dengan rekening dan FAQ
![Kontak](./img/w10-03-kontak.png)

**Gambar 4.** Katalog: 16 produk aktif, 8 per halaman (2 produk nonaktif tidak tampil)
![Katalog](./img/w10-04-katalog.png)

**Gambar 5.** Pencarian "buku" diurutkan dari harga termurah
![Cari dan urutkan](./img/w10-05-katalog-cari-filter.png)

**Gambar 6.** Filter kategori Elektronik diurutkan dari harga termahal
![Filter kategori](./img/w10-05b-katalog-kategori.png)

**Gambar 7.** Detail produk saat belum login
![Detail produk tamu](./img/w10-06-detail-produk-tamu.png)

### Autentikasi

**Gambar 8.** Registrasi gagal: semua pesan error tampil sekaligus
![Validasi daftar](./img/w10-07-daftar-validasi.png)

**Gambar 9.** Halaman masuk
![Masuk](./img/w10-08-masuk.png)

**Gambar 10.** Masuk dengan kata sandi salah
![Masuk gagal](./img/w10-08b-masuk-gagal.png)

**Gambar 11.** Membuka `/keranjang` tanpa login, diarahkan ke `/masuk`
![Redirect masuk](./img/w10-09-redirect-masuk.png)

### Pelanggan

**Gambar 12.** Detail produk setelah login sebagai pelanggan
![Detail produk pelanggan](./img/w10-10-detail-produk-pelanggan.png)

**Gambar 13.** Menambah 5 power bank padahal stok 3
![Stok tidak cukup](./img/w10-11-stok-tidak-cukup.png)

**Gambar 14.** Keranjang
![Keranjang](./img/w10-12-keranjang.png)

**Gambar 15.** Checkout
![Checkout](./img/w10-13-checkout.png)

**Gambar 16.** Validasi alamat baru di halaman checkout
![Validasi alamat](./img/w10-14-checkout-alamat-validasi.png)

**Gambar 17.** Pesanan dibuat, menunggu pembayaran
![Pesanan dibuat](./img/w10-15-pesanan-dibuat.png)

**Gambar 18.** Unggah file yang bukan gambar
![Validasi bukti](./img/w10-16a-bukti-validasi.png)

**Gambar 19.** Pratinjau bukti sebelum dikirim
![Pratinjau bukti](./img/w10-16b-pratinjau-bukti.png)

**Gambar 20.** Bukti terkirim, status Menunggu Verifikasi
![Bukti terkirim](./img/w10-16-bukti-terkirim.png)

**Gambar 21.** Riwayat pesanan
![Riwayat pesanan](./img/w10-17-riwayat-pesanan.png)

**Gambar 22.** Pesanan dikirim: kurir dan nomor resi tampil
![Pesanan dikirim](./img/w10-30-pesanan-dikirim.png)

**Gambar 23.** Pelanggan menekan Pesanan Diterima, status Selesai
![Pesanan selesai](./img/w10-31-pesanan-selesai.png)

**Gambar 24.** Bukti transfer Sari ditolak, alasan tampil dan form unggah ulang tersedia
![Bukti ditolak](./img/w10-33-bukti-ditolak.png)

### Admin

**Gambar 25.** Dashboard admin
![Dashboard admin](./img/w10-18-dashboard-admin.png)

**Gambar 26.** Dashboard setelah pesanan Budi selesai
![Dashboard setelah transaksi](./img/w10-18b-dashboard-setelah-transaksi.png)

**Gambar 27.** Data kategori
![Kategori](./img/w10-19-kategori.png)

**Gambar 28.** Kategori yang masih punya produk tidak bisa dihapus
![Kategori hapus ditolak](./img/w10-20-kategori-hapus-ditolak.png)

**Gambar 29.** Data produk
![Produk](./img/w10-21-produk.png)

**Gambar 30.** Validasi form produk
![Validasi produk](./img/w10-22a-form-produk-validasi.png)

**Gambar 31.** Form produk dengan upload dan pratinjau gambar
![Form produk](./img/w10-22-form-produk-upload.png)

**Gambar 32.** Produk baru tersimpan
![Produk tersimpan](./img/w10-22b-produk-tersimpan.png)

**Gambar 33.** Menghapus produk yang pernah dipesan: produk dinonaktifkan
![Produk dinonaktifkan](./img/w10-23-produk-dinonaktifkan.png)

**Gambar 34.** Data pesanan dengan filter status
![Pesanan admin](./img/w10-24-pesanan-admin.png)

**Gambar 35.** Antrean verifikasi pembayaran
![Verifikasi pembayaran](./img/w10-25-verifikasi-pembayaran.png)

**Gambar 36.** Pembayaran Budi diterima
![Pembayaran diterima](./img/w10-25b-pembayaran-diterima.png)

**Gambar 37.** Detail pesanan berstatus Diproses dengan form resi
![Detail pesanan diproses](./img/w10-26-detail-pesanan-diproses.png)

**Gambar 38.** Laporan penjualan
![Laporan](./img/w10-32-laporan.png)

### Staf Gudang

**Gambar 39.** Dashboard staf gudang dengan menu terbatas
![Dashboard gudang](./img/w10-27-dashboard-gudang.png)

**Gambar 40.** Pesanan berstatus Diproses yang siap dikirim
![Pesanan diproses](./img/w10-27b-pesanan-diproses-gudang.png)

**Gambar 41.** Validasi nomor resi
![Validasi resi](./img/w10-28a-resi-validasi.png)

**Gambar 42.** Resi tersimpan, pesanan berstatus Dikirim
![Resi tersimpan](./img/w10-28-resi-tersimpan.png)

### Hak Akses dan Error

**Gambar 43.** Staf gudang membuka `/panel/produk` (403)
![Gudang 403](./img/w10-29-gudang-403.png)

**Gambar 44.** Pelanggan membuka `/panel` (403)
![Pelanggan 403](./img/w10-35-pelanggan-403.png)

**Gambar 45.** Sari membuka pesanan milik Budi (404)
![Pesanan orang lain](./img/w10-33b-pesanan-orang-lain-404.png)

**Gambar 46.** Halaman yang tidak ada (404)
![404](./img/w10-34-404.png)

**Gambar 47.** Daftar route dari `php spark routes`
![Spark routes](./img/w10-36-spark-routes.png)
