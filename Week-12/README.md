# Week 12 - Melengkapi Toko Online ShopEase: Pengaturan Toko, Buku Alamat, dan Riwayat Pesanan

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  
**Kelas**: PBKK D

| | |
| :-- | :-- |
| Kode | folder [`shopease/`](./shopease) (CodeIgniter 4.7.6, PHP 8.3), lanjutan dari [Week 11](../Week-11/README.md) |
| Menjalankan | [`run.sh`](./run.sh) (Docker: PHP 8.3 + Apache, MariaDB 11) |
| Tampilan | Bootstrap 5.3, Bootstrap Icons, Chart.js 4 (dari CDN jsDelivr) |

## Soal: ShopEase Lengkap

Minggu ini ShopEase dilengkapi sampai alurnya bisa dijalankan seperti toko sungguhan, mengikuti PRD studi kasus ShopEase. Week 10 sudah menyediakan katalog, keranjang, checkout, pembayaran manual, dan pengiriman, sedangkan Week 11 menyediakan autentikasi. Yang masih kurang adalah hal-hal yang di toko nyata diatur oleh admin atau dipakai berulang oleh pelanggan:

| No | Kebutuhan | Penerapan di ShopEase |
| :-: | :-- | :-- |
| 1 | Pengaturan toko | `/panel/pengaturan`: ongkir tetap, batas gratis ongkir, rekening tujuan, instruksi pembayaran, kontak toko. Checkout tertutup selama belum ada rekening |
| 2 | Buku alamat | `/akun/alamat`: tambah, ubah, hapus, dan pilih alamat utama. Alamat utama otomatis terpilih saat checkout |
| 3 | Harga di keranjang | Keranjang menyimpan harga saat produk dimasukkan. Jika admin mengubah harga, pelanggan harus menyetujui harga baru sebelum checkout |
| 4 | Riwayat status pesanan | Setiap perubahan status dicatat: dari status apa, ke status apa, oleh siapa, kapan, dan catatannya |
| 5 | Kategori aktif/nonaktif | Kategori nonaktif menyembunyikan semua produknya dari toko tanpa mengubah status produk |
| 6 | Produk unggulan | Ditandai dari form produk, tampil di carousel beranda yang bergeser otomatis |
| 7 | Antrean gudang | Staf gudang hanya melihat pesanan yang sudah dibayar (Diproses, Dikirim, Selesai) |
| 8 | Tampilan mobile | Menu panel bisa dibuka-tutup di layar kecil, layout toko dan panel dicek di lebar 390 px |

Hak akses setelah Week 12:

| Fitur | Tamu | Pelanggan | Staf gudang | Admin |
| :-- | :-: | :-: | :-: | :-: |
| Beranda, katalog, detail produk, kontak | ✓ | ✓ | ✓ | ✓ |
| Daftar dan masuk | ✓ | | | |
| Akun saya (profil, sandi, perangkat) | | ✓ | ✓ | ✓ |
| Keranjang, buku alamat, checkout, pesanan sendiri | | ✓ | | |
| Dashboard panel | | | ✓ | ✓ |
| Pesanan Diproses, Dikirim, Selesai: input resi, tandai selesai | | | ✓ | ✓ |
| Semua pesanan, verifikasi bayar, batal pesanan | | | | ✓ |
| Kategori, produk, pengguna, riwayat masuk, laporan, pengaturan | | | | ✓ |

## Pendahuluan

Referensi dari teman memakai ShopEase dengan nama tabel dan route berbahasa Inggris (`orders`, `/admin/settings`, `order_status_events`). Di sini struktur yang sudah ada sejak Week 10 dipertahankan dan fiturnya ditambahkan di atasnya, jadi tidak ada tabel yang dibuat ulang. Perubahan dari Week 11:

| | Week 11 | Week 12 |
| :-- | :-- | :-- |
| Ongkir | Konstanta di kode (`ONGKIR = 15000`) | Tabel `pengaturan`, diubah admin dari panel |
| Rekening tujuan | Ditulis langsung di dua view | Dari pengaturan; checkout ditutup kalau kosong |
| Alamat | Hanya bisa ditambah dari halaman checkout | Buku alamat lengkap dengan satu alamat utama |
| Perubahan harga | Keranjang langsung memakai harga baru tanpa pemberitahuan | Harga lama dicoret, checkout ditahan sampai pelanggan menyetujui |
| Status pesanan | Hanya status terakhir | Linimasa lengkap dengan pelaku dan catatan |
| Kategori | Hanya bisa dihapus (dan ditolak kalau berisi produk) | Bisa dinonaktifkan |
| Beranda | Hero statis | Carousel produk unggulan |
| Panel untuk gudang | Melihat semua pesanan, termasuk yang belum dibayar | Hanya antrean pengiriman |
| Panel di HP | Sidebar memanjang di atas konten | Menu lipat |

Perbedaan lain dari referensi: pengaturan disimpan sebagai kunci-nilai sehingga menambah pengaturan baru tidak butuh migration, harga diperiksa lagi di dalam transaksi checkout (bukan hanya di halaman keranjang), dan staf gudang yang membuka pesanan belum dibayar mendapat 404, bukan sekadar disembunyikan dari daftar.

## Struktur Project

Yang baru atau berubah dari Week 11:

```
Week-12/
├── README.md
├── run.sh                       # port 8092, container pbkk12-app dan pbkk12-db
├── img/                         # screenshot hasil
└── shopease/app/
    ├── Database/
    │   ├── Migrations/2026-11-24-000001_PelengkapToko.php   # BARU
    │   └── Seeds/ShopEaseSeeder.php                        # + pengaturan, alamat utama, unggulan, riwayat
    ├── Models/
    │   ├── PengaturanModel.php       # BARU: kunci-nilai, ongkir(), rekening(), checkoutSiap()
    │   ├── PesananRiwayatModel.php   # BARU
    │   ├── AlamatModel.php           # tambah(), jadikanUtama(), hapusMilik()
    │   ├── KeranjangModel.php        # harga_lama, setujuiHarga()
    │   ├── KategoriModel.php         # aktif, untukToko()
    │   ├── ProdukModel.php           # unggulan, "aktif" = produk aktif DAN kategori aktif
    │   └── PesananModel.php          # statusUntuk(role), filter 'hanya'
    ├── Libraries/AlurPesanan.php     # ongkir dari pengaturan, cek harga, catat riwayat
    ├── Controllers/
    │   ├── Alamat.php                # BARU: buku alamat
    │   ├── Panel/Pengaturan.php      # BARU
    │   ├── Keranjang.php             # setujuiHarga()
    │   ├── Checkout.php              # cek checkout siap
    │   └── Panel/Kategori.php, Pesanan.php, Produk.php
    ├── Helpers/app_helper.php        # toko(), info_ongkir()
    └── Views/
        ├── akun/alamat.php, alamat_ubah.php   # BARU
        ├── panel/pengaturan/index.php         # BARU
        ├── komponen/form_alamat.php, riwayat.php   # BARU
        ├── toko/beranda.php          # carousel unggulan
        ├── layouts/panel.php         # menu lipat di HP, menu Pengaturan
        └── errors/cli, errors/html   # view error bawaan CI yang ternyata hilang sejak Week 10
```

Alasan pembagiannya:

- **`PengaturanModel` sebagai satu pintu.** Ongkir dipakai di keranjang, checkout, footer, beranda, dan halaman kontak. Model membaca tabel sekali per request lalu menyimpannya di properti, jadi lima pemanggilan tetap satu query.
- **Riwayat dicatat di `AlurPesanan::pindah()`.** Semua perubahan status sudah lewat fungsi itu sejak Week 10, sehingga cukup satu baris tambahan untuk mencatat setiap perpindahan. Tidak ada controller yang perlu ingat menulis riwayat.
- **`komponen/form_alamat.php`.** Form alamat dipakai di checkout, buku alamat, dan halaman ubah alamat. Isinya ditulis sekali.

## Bagian 1: Database

### 1. Migration Lanjutan

Semua perubahan Week 12 ada di satu migration, tanpa mengubah migration lama:

```php
// app/Database/Migrations/2026-11-24-000001_PelengkapToko.php
// Pengaturan toko dalam bentuk kunci-nilai, jadi menambah pengaturan baru tidak perlu migration lagi
$this->forge->addField([
    'kunci'      => ['type' => 'VARCHAR', 'constraint' => 40],
    'nilai'      => ['type' => 'TEXT', 'null' => true],
    'updated_at' => ['type' => 'DATETIME', 'null' => true],
]);
$this->forge->addPrimaryKey('kunci');
$this->forge->createTable('pengaturan');

$this->forge->addColumn('alamat', ['utama' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'kode_pos']]);
// Harga saat produk dimasukkan ke keranjang, untuk mendeteksi perubahan harga sebelum checkout
$this->forge->addColumn('keranjang', ['harga' => ['type' => 'INT', 'unsigned' => true, 'default' => 0, 'after' => 'qty']]);
$this->forge->addColumn('kategori', ['aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'deskripsi']]);
$this->forge->addColumn('produk', ['unggulan' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'aktif']]);

// pesanan_riwayat: pesanan_id, dari, ke, oleh (user), catatan, created_at
$this->forge->addForeignKey('pesanan_id', 'pesanan', 'id', 'CASCADE', 'CASCADE');
$this->forge->addForeignKey('oleh', 'users', 'id', 'SET NULL', 'CASCADE');
$this->forge->createTable('pesanan_riwayat');
```

Relasi setelah Week 12 (tabel Week 10 dan 11 tidak berubah):

```
users    1 ── N alamat           (satu alamat utama per user)
users    1 ── N keranjang N ── 1 produk   (keranjang.harga = harga saat ditambahkan)
kategori 1 ── N produk           (kategori.aktif = 0 menyembunyikan semua produknya)
pesanan  1 ── N pesanan_riwayat  (CASCADE)
users    1 ── N pesanan_riwayat  (oleh, SET NULL)
pengaturan                       (kunci-nilai, tidak berelasi)
```

### 2. Menjalankan

```bash
cd Week-12
./run.sh up       # build, composer install, migrate --all, seed
./run.sh reset    # hapus database dan upload, lalu up dari awal
```

```
Menjalankan semua migrasi baru...
	Berjalan: (App) 2026-11-10-000001_App\Database\Migrations\CreateShopEaseTables
	Berjalan: (App) 2026-11-17-000001_App\Database\Migrations\UpgradeAutentikasi
	Berjalan: (App) 2026-11-24-000001_App\Database\Migrations\PelengkapToko
Migrasi selesai.
Seeded: App\Database\Seeds\ShopEaseSeeder

ShopEase -> http://localhost:8092
```

Seeder sekarang juga mengisi pengaturan awal (ongkir Rp15.000, gratis mulai Rp300.000, tiga rekening), menandai empat produk unggulan, dan membuat riwayat status untuk 8 pesanan contoh. Waktu riwayatnya disesuaikan dengan waktu unggah bukti, verifikasi, dan pengiriman masing-masing pesanan, jadi linimasanya masuk akal.

Akun untuk mencoba sama dengan Week 11:

| Role | Email | Sandi |
| :-- | :-- | :-- |
| Admin | `admin@shopease.test` | `admin123` |
| Staf gudang | `gudang@shopease.test` | `gudang123` |
| Pelanggan | `budi@mail.test`, `sari@mail.test`, `andi@mail.test` | `budi1234`, `sari1234`, `andi1234` |
| Pelanggan (dibekukan) | `dewi@mail.test` | `dewi1234` |

## Bagian 2: Pengaturan Toko

### 1. Model Kunci-Nilai

```php
class PengaturanModel extends Model
{
    /** Nilai bawaan bila kunci belum pernah disimpan. */
    public const BAWAAN = [
        'ongkir'        => '0',
        'gratis_ongkir' => '0',   // 0 = tidak ada gratis ongkir
        'rekening'      => '',    // satu rekening per baris: Bank | Nomor | Atas nama
        'instruksi'     => '',
        'email'         => '',
        'whatsapp'      => '',
    ];

    private ?array $cache = null;

    public function semua(): array
    {
        return $this->cache ??= array_column($this->findAll(), 'nilai', 'kunci') + self::BAWAAN;
    }

    public function simpan(array $data): void
    {
        foreach (array_intersect_key($data, self::BAWAAN) as $kunci => $nilai) {
            $this->upsert(['kunci' => $kunci, 'nilai' => (string) $nilai]);
        }
        $this->cache = null;
    }

    /** Checkout baru dibuka setelah admin mengisi minimal satu rekening tujuan. */
    public function checkoutSiap(): bool
    {
        return $this->rekening() !== [];
    }

    public function ongkir(int $subtotal): int
    {
        $gratis = (int) $this->nilai('gratis_ongkir');

        return $gratis > 0 && $subtotal >= $gratis ? 0 : (int) $this->nilai('ongkir');
    }
}
```

`array_intersect_key` memastikan hanya kunci yang dikenal yang tersimpan. Field tambahan yang dikirim lewat request buatan diabaikan. `+ self::BAWAAN` membuat toko tetap jalan walaupun tabel `pengaturan` masih kosong: ongkir 0 dan checkout tertutup.

View memanggilnya lewat helper:

```php
function toko(?string $kunci = null): mixed
{
    $model = model(\App\Models\PengaturanModel::class);

    return $kunci === null ? $model : $model->nilai($kunci);
}
```

```php
<?php foreach (toko()->rekening() as $r): ?>
    <li><?= esc($r['bank']) ?> <span class="font-monospace"><?= esc($r['nomor']) ?></span> a.n. <?= esc($r['nama']) ?></li>
<?php endforeach ?>
```

### 2. Validasi Rekening

Rekening ditulis satu per baris dengan format `Bank | Nomor | Atas nama`. Aturan bawaan CodeIgniter tidak bisa memeriksa format per baris, jadi pemeriksaannya ditambahkan di controller dan pesannya menyebut baris yang salah:

```php
foreach (preg_split('/\R/', $data['rekening']) as $i => $baris) {
    if ($baris !== '' && count(array_filter(array_map('trim', explode('|', $baris)), 'strlen')) !== 3) {
        $errors['rekening'] = 'Baris ' . ($i + 1) . ' rekening harus berformat "Bank | Nomor | Atas nama".';
        break;
    }
}
```

### 3. Checkout Ditutup

Pengecekan dilakukan di tiga tempat dengan tujuan yang berbeda:

| Tempat | Tujuan |
| :-- | :-- |
| `keranjang/index.php` | Tombol Checkout dinonaktifkan dengan penjelasan, keranjang tetap tersimpan |
| `Checkout::index()` | Membuka `/checkout` langsung dari URL diarahkan kembali ke keranjang |
| `AlurPesanan::checkout()` | Penjaga terakhir. `POST /checkout` buatan tetap ditolak sebelum transaksi dimulai |

## Bagian 3: Pelanggan

### 1. Buku Alamat

Aturannya: setiap pelanggan yang punya alamat selalu punya tepat satu alamat utama.

```php
/** Alamat pertama seorang user otomatis menjadi alamat utama. */
public function tambah(int $userId, array $data): int|false
{
    $pertama = $this->where('user_id', $userId)->countAllResults() === 0;

    return $this->insert($data + ['user_id' => $userId, 'utama' => $pertama ? 1 : 0]);
}

/** Tepat satu alamat utama per user: semua dimatikan lalu satu dinyalakan, dalam satu transaksi. */
public function jadikanUtama(int $id, int $userId): void
{
    $this->db->transStart();
    $this->builder()->where('user_id', $userId)->update(['utama' => 0]);
    $this->builder()->where(['id' => $id, 'user_id' => $userId])->update(['utama' => 1]);
    $this->db->transComplete();
}

/** Menghapus alamat. Jika yang dihapus alamat utama, alamat tertua yang tersisa menggantikannya. */
public function hapusMilik(array $alamat): void
{
    $this->delete($alamat['id']);

    if ($alamat['utama'] && ($pengganti = $this->where('user_id', $alamat['user_id'])->orderBy('id')->first())) {
        $this->jadikanUtama((int) $pengganti['id'], (int) $alamat['user_id']);
    }
}
```

Menghapus alamat tidak mengganggu pesanan lama, karena sejak Week 10 pesanan menyimpan salinan teks alamat, bukan `alamat_id`.

Controller `Alamat` mencari alamat selalu bersama `user_id` pemiliknya. Alamat milik orang lain dijawab 404, bukan 403, supaya ID alamat tidak bisa ditebak satu per satu:

```php
private function milik(int $id): array
{
    return $this->model->milikUser($id, user('id')) ?? throw PageNotFoundException::forPageNotFound();
}
```

### 2. Harga Berubah di Keranjang

Saat produk dimasukkan ke keranjang, harganya disimpan di `keranjang.harga`. `KeranjangModel::isi()` membandingkannya dengan harga produk sekarang:

```php
foreach ($items as &$it) {
    $it['berubah']  = (int) $it['harga'] !== (int) $it['harga_lama'];
    $it['subtotal'] = $it['harga'] * $it['qty'];
    $it['masalah']  = match (true) {
        ! $it['aktif']           => 'Produk sudah tidak dijual.',
        $it['qty'] > $it['stok'] => "Stok tersisa {$it['stok']}.",
        $it['berubah']           => 'Harga berubah dari ' . rupiah($it['harga_lama']) . '.',
        default                  => null,
    };
    // ...
}
```

Subtotal tetap memakai harga baru, karena pelanggan memang akan membayar harga itu. Bedanya, sekarang pelanggan melihat harga lama yang dicoret dan harus menekan **Setujui harga baru** sebelum checkout. Persetujuan itu satu query:

```php
$this->db->query('UPDATE keranjang k JOIN produk p ON p.id = k.produk_id SET k.harga = p.harga WHERE k.user_id = ? AND k.harga <> p.harga', [$userId]);
```

Pemeriksaan yang sama ada di dalam transaksi checkout, setelah baris produk dikunci. Jadi kalau admin mengubah harga tepat di antara pelanggan membuka halaman checkout dan menekan **Buat Pesanan**, pesanan tetap ditolak:

```php
// Harga bisa diubah admin setelah barang masuk keranjang; pelanggan harus menyetujuinya dulu
if ((int) $p['harga'] !== (int) $k['harga']) {
    throw new RuntimeException("Harga \"{$p['nama']}\" berubah dari " . rupiah($k['harga']) . ' menjadi ' . rupiah($p['harga']) . '. Periksa keranjang lalu setujui harga baru.');
}
```

## Bagian 4: Riwayat Status Pesanan

`pindah()` di `AlurPesanan` sekarang mencatat setiap perpindahan:

```php
private function pindah(int $pesananId, string $ke, ?string $catatan = null, ?int $oleh = null): void
{
    $p = $this->db->query('SELECT status FROM pesanan WHERE id = ? FOR UPDATE', [$pesananId])->getRowArray()
        ?? throw new RuntimeException('Pesanan tidak ditemukan.');
    // ... cek ALUR ...
    model(PesananModel::class)->update($pesananId, ['status' => $ke]);
    $this->catat($pesananId, $p['status'], $ke, $catatan, $oleh);
}

private function catat(int $pesananId, ?string $dari, string $ke, ?string $catatan, ?int $oleh): void
{
    model(PesananRiwayatModel::class)->insert([
        'pesanan_id' => $pesananId,
        'dari'       => $dari,
        'ke'         => $ke,
        'oleh'       => $oleh ?? $this->oleh ?? (is_cli() ? null : user('id')),
        'catatan'    => $catatan === null ? null : mb_substr($catatan, 0, 255),
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}
```

Karena dicatat di dalam transaksi yang sama, riwayat tidak mungkin berbeda dengan status pesanan: kalau perpindahan status gagal dan di-rollback, catatan riwayatnya ikut batal.

Catatan diisi otomatis sesuai langkahnya:

| Perpindahan | Pelaku | Catatan |
| :-- | :-- | :-- |
| (baru) → Menunggu Pembayaran | Pelanggan | - |
| → Menunggu Verifikasi | Pelanggan | `Transfer BCA a.n. Budi Santoso` |
| → Diproses | Admin | `Pembayaran diterima` |
| → Menunggu Pembayaran (tolak) | Admin | `Bukti ditolak: <alasan>` |
| → Dikirim | Staf gudang / admin | `SiCepat 004589127733` |
| → Selesai | Pelanggan / petugas | `Barang diterima` |
| → Dibatalkan | Pelanggan / admin | `Dibatalkan admin, stok dikembalikan` |

Linimasa yang sama ditampilkan di halaman pesanan pelanggan dan di panel. Di panel, nama dan role pelaku ikut ditampilkan.

Seeder dijalankan dari CLI, jadi tidak ada session untuk mengetahui pelakunya. Untuk itu ada `sebagai()`:

```php
$p = $alur->sebagai($user)->checkout($user, $alamatId, null);
```

## Bagian 5: Admin dan Gudang

### 1. Kategori dan Produk

"Aktif" bagi pembeli sekarang berarti produknya aktif **dan** kategorinya aktif. Aturan ini ada di satu tempat, `ProdukModel::cari()`, yang dipakai katalog, detail produk, produk serupa, dan tambah ke keranjang:

```php
if (($filter['aktif'] ?? null) === true) {
    $this->where(['produk.aktif' => 1, 'kategori.aktif' => 1]);
}
// Filter panel admin: status produk itu sendiri, tanpa melihat kategori
if (isset($filter['status'])) {
    $this->where('produk.aktif', $filter['status'] ? 1 : 0);
}
```

Status produk tidak diubah saat kategorinya dinonaktifkan. Jadi ketika kategori diaktifkan lagi, produk yang tadinya aktif langsung muncul kembali, dan produk yang memang dinonaktifkan admin tetap tersembunyi.

Produk unggulan ditandai dengan switch di form produk dan tampil di carousel Bootstrap di beranda (bergeser tiap 4,5 detik, dengan panah dan indikator).

### 2. Antrean Gudang

```php
/** Status yang boleh dilihat tiap role di panel. Staf gudang hanya mengurus pesanan yang sudah dibayar. */
public static function statusUntuk(?string $role): array
{
    return $role === 'admin' ? self::STATUS : self::SUDAH_DIBAYAR;
}
```

Batasan ini dipakai di daftar pesanan, dashboard, dan detail pesanan. Di detail, pesanan di luar batas dianggap tidak ada:

```php
// Pesanan di luar status yang boleh dilihat role ini dianggap tidak ada
$p = model(PesananModel::class)->daftar(['hanya' => PesananModel::statusUntuk(user('role'))])->where('pesanan.id', $id)->first()
    ?? throw PageNotFoundException::forPageNotFound();
```

Menu "Pesanan" di sidebar staf gudang berganti nama menjadi "Antrean Pengiriman".

### 3. Panel di HP

Di bawah 992 px, sidebar panel berubah menjadi bar atas dengan tombol menu. Daftar menunya memakai kelas Bootstrap `collapse d-lg-flex`: tersembunyi di HP dan selalu tampil di layar lebar, tanpa JavaScript tambahan.

```php
<button class="btn btn-sm text-white d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#menuPanel" aria-controls="menuPanel" aria-expanded="false" aria-label="Menu"><i class="bi bi-list fs-4"></i></button>
<!-- ... -->
<nav class="nav flex-column gap-1 collapse d-lg-flex mt-2 mt-lg-0" id="menuPanel">
```

## Kendala dan Solusi

| Kendala | Penyebab | Solusi |
| :-- | :-- | :-- |
| `/keranjang` error 500: `Unknown column 'produk.aktif * kategori.aktif'` | Query Builder meng-escape ekspresi di `select()` seolah-olah itu nama kolom | Ekspresi dipindah ke `select('produk.aktif * kategori.aktif AS aktif', false)` |
| Seeder gagal dengan pesan "The error view file was not specified" | Folder `app/Views/errors/cli` dan `html` bawaan CodeIgniter terhapus sejak Week 10, jadi error apa pun tidak bisa ditampilkan | Kedua folder disalin lagi dari `vendor/codeigniter4/framework/app/Views/errors`. Error aslinya kemudian terlihat: checkout ditolak karena pengaturan toko belum ada |
| Seeder tidak bisa membuat pesanan contoh | Pesanan contoh juga lewat `AlurPesanan::checkout()`, yang sekarang menolak checkout tanpa rekening dan tanpa harga di keranjang | Seeder mengisi pengaturan lebih dulu, dan baris keranjang contoh diisi harga produk |
| Riwayat pesanan contoh semuanya bertanggal hari ini | `catat()` memakai waktu saat seeder dijalankan | Seeder menyesuaikan `created_at` riwayat dengan waktu pesanan, unggah bukti, verifikasi, dan pengiriman |
| Filter "Hanya aktif" di daftar produk admin ikut menyembunyikan produk di kategori nonaktif | Filter admin dan katalog sama-sama memakai kunci `aktif` | Panel memakai kunci `status` (hanya `produk.aktif`), katalog tetap memakai `aktif` |

## Pembagian Tugas

Tugas ini dikerjakan secara individu:

| Anggota | Area | Pekerjaan |
| :-- | :-- | :-- |
| Hosea (5025241177) | Database | Migration lanjutan, seeder pengaturan, alamat utama, produk unggulan, waktu riwayat |
| Hosea (5025241177) | Model dan library | `PengaturanModel`, `PesananRiwayatModel`, alamat utama, deteksi harga, riwayat di `AlurPesanan` |
| Hosea (5025241177) | Controller dan route | `Alamat`, `Panel\Pengaturan`, status kategori, antrean gudang |
| Hosea (5025241177) | View | Buku alamat, pengaturan, linimasa riwayat, carousel, menu panel di HP |
| Hosea (5025241177) | Pengujian dan dokumentasi | Skenario `curl`, screenshot desktop dan mobile, laporan |

## Alur Data

Admin menaikkan harga mouse saat mouse ada di keranjang Budi:

| # | Request | Yang terjadi |
| :-: | :-- | :-- |
| 1 | Budi: `POST /keranjang` (mouse) | `keranjang`: qty 1, `harga` 129000 |
| 2 | Admin: `POST /panel/produk/1` (harga 139000) | `produk.harga` = 139000 |
| 3 | Budi: `GET /keranjang` | `isi()`: `harga_lama` 129000 ≠ 139000 → baris kuning, harga lama dicoret, tombol Checkout nonaktif |
| 4 | Budi: `GET /checkout` | `bermasalah` → redirect `/keranjang` |
| 5 | Budi: `POST /checkout` (request buatan) | Transaksi: `FOR UPDATE` produk, harga beda → `RuntimeException`, rollback, tidak ada pesanan |
| 6 | Budi: `POST /keranjang/harga` | `UPDATE keranjang ... SET k.harga = p.harga`, 1 baris |
| 7 | Budi: `POST /checkout` | Pesanan dibuat, riwayat: `(baru) → menunggu_bayar` oleh Budi |

Satu perpindahan status beserta riwayatnya:

```
Admin klik "Terima" di /panel/pembayaran
      │  POST /panel/pembayaran/5/terima
      ▼
csrf → identitas → auth:admin → Panel\Pembayaran::terima(5)
      ▼
AlurPesanan::verifikasi(pesanan 5, admin 1, diterima)      [transBegin]
      │  SELECT pembayaran: status 'menunggu' ✓
      │  pindah(5, 'diproses', 'Pembayaran diterima', 1)
      │     SELECT status FROM pesanan WHERE id = 5 FOR UPDATE   → menunggu_verifikasi
      │     ALUR['menunggu_verifikasi'] berisi 'diproses' ✓
      │     UPDATE pesanan SET status = 'diproses'
      │     INSERT pesanan_riwayat (5, menunggu_verifikasi, diproses, 1, 'Pembayaran diterima')
      │  UPDATE pembayaran SET status = 'diterima', diverifikasi_oleh = 1
      ▼                                                    [transCommit]
redirect → /panel/pembayaran
```

## Tanggung Jawab Tiap File

| File | Tanggung jawab |
| :-- | :-- |
| `app/Database/Migrations/2026-11-24-000001_PelengkapToko.php` | Tabel `pengaturan` dan `pesanan_riwayat`, kolom `alamat.utama`, `keranjang.harga`, `kategori.aktif`, `produk.unggulan` |
| `app/Models/PengaturanModel.php` | Membaca dan menyimpan pengaturan, menghitung ongkir, mengurai rekening, status checkout |
| `app/Models/PesananRiwayatModel.php` | Riwayat satu pesanan beserta nama dan role pelaku |
| `app/Models/AlamatModel.php` | Validasi alamat, alamat utama, hapus dengan pengganti |
| `app/Models/KeranjangModel.php` | Isi keranjang dengan harga lama/baru, setujui harga |
| `app/Models/ProdukModel.php`, `KategoriModel.php` | Aturan "aktif untuk pembeli", produk unggulan, kategori untuk toko |
| `app/Models/PesananModel.php` | Batas status per role, filter `hanya` |
| `app/Libraries/AlurPesanan.php` | Checkout (siap, harga, stok), perpindahan status, pencatatan riwayat |
| `app/Controllers/Alamat.php` | Buku alamat pelanggan |
| `app/Controllers/Panel/Pengaturan.php` | Form dan validasi pengaturan toko |
| `app/Controllers/Keranjang.php`, `Checkout.php` | Setujui harga, tutup checkout |
| `app/Controllers/Panel/Kategori.php`, `Pesanan.php`, `Produk.php` | Status kategori, antrean gudang, flag unggulan |
| `app/Helpers/app_helper.php` | `toko()`, `info_ongkir()` |
| `app/Views/komponen/form_alamat.php`, `riwayat.php` | Form alamat dan linimasa yang dipakai ulang |
| `app/Views/akun/alamat*.php`, `panel/pengaturan/index.php` | Halaman baru |
| `app/Views/layouts/panel.php`, `toko/beranda.php` | Menu lipat, menu Pengaturan, carousel |

## Pengujian

Skenario berikut diuji dengan `curl` (cookie jar per akun, token CSRF diambil dari form) pada database yang baru di-reset. Isi tabel dicek langsung di MariaDB.

| No | Skenario | Hasil |
| :-: | :-- | :-- |
| 1 | Pelanggan membuka beranda, katalog, kontak, keranjang, pesanan, akun, buku alamat | Semua 200 |
| 2 | Staf gudang membuka `/panel/pengaturan`, `/panel/kategori` | 403, 403 |
| 3 | Simpan rekening `BCA 123` | "Baris 1 rekening harus berformat "Bank \| Nomor \| Atas nama"." |
| 4 | Rekening dikosongkan | "Pengaturan disimpan. Checkout pelanggan ditutup sampai ada rekening tujuan." |
| 5 | Checkout saat rekening kosong: `GET /checkout`; `POST /checkout` | Redirect ke keranjang; "Checkout belum dibuka karena rekening tujuan pembayaran belum diatur admin." Jumlah pesanan tidak berubah |
| 6 | Harga mouse dinaikkan Rp129.000 → Rp139.000 saat ada di keranjang | Keranjang: harga lama dicoret, "Perbaiki baris yang ditandai kuning sebelum checkout." `GET /checkout` redirect |
| 7 | `POST /checkout` langsung tanpa menyetujui harga | "Harga "Mouse Wireless Silent" berubah dari Rp129.000 menjadi Rp139.000. Periksa keranjang lalu setujui harga baru." |
| 8 | Setujui harga baru lalu checkout | "Harga terbaru untuk 1 produk disetujui.", `keranjang.harga` 139000, pesanan 8 → 9, riwayat `(baru) → menunggu_bayar` oleh Budi |
| 9 | Tambah alamat Kantor | Tersimpan dengan `utama = 0` (Kos tetap utama) |
| 10 | Jadikan Kantor utama, lalu hapus Kantor | Kantor `utama = 1`, Kos `0`; setelah dihapus, Kos kembali `utama = 1` |
| 11 | Budi membuka/menghapus alamat milik Sari | 404 / 404, alamat Sari tetap ada |
| 12 | Tambah alamat dengan data salah | "Beri label alamat...", "Nama penerima minimal 3 huruf.", "Nomor HP harus diawali 08...", "Alamat terlalu pendek...", "Kota wajib diisi.", "Kode pos terdiri dari 5 angka." |
| 13 | Admin membuka `/akun/alamat` | 403 (buku alamat khusus pelanggan) |
| 14 | Kategori Elektronik dinonaktifkan | Detail mouse 404, "Mouse Wireless" tidak ada di katalog, tambah mouse ke keranjang: "Produk tidak tersedia." |
| 15 | Gudang membuka `/panel/pesanan` | Tab hanya Semua (4), Diproses (1), Dikirim (1), Selesai (2) |
| 16 | Detail pesanan Menunggu Pembayaran: gudang / admin; detail pesanan Diproses: gudang | 404 / 200; 200 |
| 17 | Riwayat pesanan contoh yang ditolak | Pesanan dibuat (Sari) → Menunggu Verifikasi "Transfer BSI a.n. Sari W" (Sari) → Menunggu Pembayaran "Bukti ditolak: Nominal transfer Rp100.000, kurang dari total pesanan." (Admin) |

## Kesimpulan

1. Menyimpan pengaturan di database membuat admin bisa mengubah ongkir dan rekening tanpa menyentuh kode. Format kunci-nilai membuat pengaturan berikutnya cukup ditambahkan di `BAWAAN`.
2. Checkout yang tertutup ketika rekening kosong mencegah pesanan yang tidak bisa dibayar. Pengecekan di `AlurPesanan` memastikan request buatan juga ikut tertolak.
3. Menyimpan harga di keranjang membuat perubahan harga terlihat oleh pelanggan, dan pemeriksaan ulang di dalam transaksi menutup jeda antara halaman checkout dan tombol **Buat Pesanan**.
4. Karena semua perubahan status sudah lewat satu fungsi sejak Week 10, riwayat lengkap cukup ditambahkan di satu tempat dan otomatis berlaku untuk pelanggan, admin, gudang, dan seeder.
5. Aturan "produk aktif untuk pembeli" yang dikumpulkan di `ProdukModel::cari()` membuat kategori nonaktif langsung berlaku di katalog, detail, keranjang, dan checkout.
6. Pembatasan per role sebaiknya berlaku juga untuk akses langsung lewat URL, bukan hanya menyembunyikan baris dari daftar. Staf gudang mendapat 404 untuk pesanan yang belum dibayar.

## Hasil (Foto)

### Beranda dan Halaman Publik

**Gambar 1.** Beranda dengan carousel produk unggulan dan info ongkir dari pengaturan
![Beranda](./img/w12-01-beranda.png)

**Gambar 2.** Carousel setelah panah kanan ditekan
![Carousel](./img/w12-02-carousel-geser.png)

**Gambar 3.** Halaman kontak: rekening, WhatsApp, email, dan ongkir dari pengaturan
![Kontak](./img/w12-03-kontak.png)

**Gambar 4.** Beranda di HP (390 px)
![Beranda mobile](./img/w12-04-beranda-mobile.png)

### Pengaturan Toko

**Gambar 5.** Pengaturan toko
![Pengaturan](./img/w12-05-pengaturan.png)

**Gambar 6.** Baris rekening keempat salah format
![Validasi pengaturan](./img/w12-06-pengaturan-validasi.png)

**Gambar 7.** Rekening dikosongkan: checkout ditutup, menu Pengaturan diberi tanda
![Checkout ditutup di panel](./img/w12-07-checkout-ditutup-admin.png)

**Gambar 8.** Keranjang pelanggan saat checkout ditutup
![Keranjang checkout ditutup](./img/w12-08-keranjang-checkout-ditutup.png)

### Keranjang dan Checkout

**Gambar 9.** Harga mouse dinaikkan admin: harga lama dicoret, checkout ditahan
![Harga berubah](./img/w12-09-keranjang-harga-berubah.png)

**Gambar 10.** Setelah harga baru disetujui
![Harga disetujui](./img/w12-10-harga-disetujui.png)

**Gambar 11.** Checkout: alamat utama terpilih otomatis, tautan Kelola ke buku alamat
![Checkout alamat utama](./img/w12-15-checkout-alamat-utama.png)

### Buku Alamat

**Gambar 12.** Buku alamat Budi
![Buku alamat](./img/w12-11-buku-alamat.png)

**Gambar 13.** Tambah alamat dengan data salah
![Validasi alamat](./img/w12-12-alamat-validasi.png)

**Gambar 14.** Alamat Kantor dijadikan alamat utama
![Alamat utama](./img/w12-13-alamat-utama-diganti.png)

**Gambar 15.** Ubah alamat
![Ubah alamat](./img/w12-14-ubah-alamat.png)

**Gambar 16.** Buku alamat di HP
![Buku alamat mobile](./img/w12-18-buku-alamat-mobile.png)

### Riwayat Pesanan

**Gambar 17.** Pesanan baru dengan riwayat pertamanya
![Pesanan baru](./img/w12-16-pesanan-baru-riwayat.png)

**Gambar 18.** Pesanan selesai dilihat pelanggan: lima langkah dengan catatan
![Riwayat pesanan selesai](./img/w12-17-riwayat-pesanan-selesai.png)

**Gambar 19.** Pesanan yang buktinya ditolak, dilihat admin: pelaku setiap langkah ikut tampil
![Riwayat di panel](./img/w12-24-riwayat-panel-ditolak.png)

### Kategori dan Produk

**Gambar 20.** Kategori Olahraga dinonaktifkan
![Kategori nonaktif](./img/w12-19-kategori-nonaktif.png)

**Gambar 21.** Katalog tanpa kategori Olahraga (14 produk)
![Katalog tanpa olahraga](./img/w12-20-katalog-tanpa-olahraga.png)

**Gambar 22.** Daftar produk admin: tanda bintang untuk unggulan, label kategori nonaktif
![Produk unggulan](./img/w12-21-produk-unggulan.png)

**Gambar 23.** Switch produk unggulan di form produk
![Form produk](./img/w12-22-form-produk-unggulan.png)

**Gambar 24.** Daftar pesanan admin, filter Menunggu Pembayaran
![Pesanan admin](./img/w12-23-pesanan-admin.png)

### Staf Gudang

**Gambar 25.** Antrean pengiriman: hanya pesanan yang sudah dibayar
![Antrean gudang](./img/w12-25-antrean-gudang.png)

**Gambar 26.** Gudang membuka pesanan yang belum dibayar: 404
![Gudang 404](./img/w12-26-gudang-404.png)

**Gambar 27.** Detail pesanan Diproses dari sisi gudang, dengan form resi dan riwayat
![Gudang detail](./img/w12-27-gudang-detail-diproses.png)

### Panel di HP

**Gambar 28.** Dashboard admin di HP, menu tertutup
![Panel mobile](./img/w12-28-panel-mobile.png)

**Gambar 29.** Menu panel dibuka
![Menu panel mobile](./img/w12-29-panel-mobile-menu.png)
