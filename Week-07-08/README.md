# Week 07 & 08 - ETS: ETC (Elite TC Champions)

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  
**Kelas**: PBKK D

## Tentang Project
ETC (Elite TC Champions) adalah aplikasi desktop untuk mempertemukan mahasiswa yang sedang mencari anggota tim lomba atau proyek dengan mahasiswa yang ingin bergabung. Satu akun mahasiswa bisa membuat rekrutmen sendiri sekaligus melamar ke rekrutmen tim lain. Aplikasi juga punya direktori mentor yang terhubung ke Google Form untuk pengajuan mentoring.

Teknologi: C#/.NET 10, Avalonia, MVVM (CommunityToolkit.Mvvm), ADO.NET (`Microsoft.Data.SqlClient`), dan SQL Server. Cara menjalankan aplikasi ada di README repository.

| | |
| :-- | :-- |
| PRD | [PRD.pdf](https://github.com/abidalfrz/ETC/blob/main/PRD.pdf) |
| Repository | [github.com/abidalfrz/ETC](https://github.com/abidalfrz/ETC) |
| Video Demo | [youtu.be/fST3n_nGM44](https://youtu.be/fST3n_nGM44) |

## Video Demo

[![Demo ETC](https://img.youtube.com/vi/fST3n_nGM44/maxresdefault.jpg)](https://youtu.be/fST3n_nGM44)

---

# Product Requirements Document

## ETC: Elite TC Champions

Sistem informasi desktop untuk rekrutmen tim mahasiswa Teknik Informatika ITS.

| Informasi Dokumen | Deskripsi |
| :-- | :-- |
| Nama Produk | ETC (Elite TC Champions) |
| Jenis Dokumen | Product Requirements Document (PRD) |
| Versi | Draft untuk peninjauan dan penyelarasan rilis demonstrasi |
| Tanggal Penyusunan | 20 Oktober 2026 |
| Platform | Desktop. Ubuntu x64 sebagai target utama, Windows x64 sebagai target tambahan |
| Teknologi Utama | C#, .NET 10, Avalonia, AXAML, MVVM, CommunityToolkit.Mvvm, SQL Server, ADO.NET |
| Target Pengguna | Mahasiswa, sebagai pembuat rekrutmen atau pelamar. Mentor ditampilkan sebagai direktori, bukan peran akun |
| Konteks Penggunaan | Demonstrasi tugas kuliah, bukan layanan publik |

Dokumen ini menetapkan kebutuhan, batas bisnis, pengalaman pengguna, rancangan data, arsitektur, dan kriteria verifikasi ETC.

## 1. Ringkasan Eksekutif
ETC membantu mahasiswa mengumumkan kebutuhan anggota tim lomba atau proyek, mencari posisi yang relevan, mengirim lamaran, dan mengikuti hasil seleksi. Pembuat rekrutmen dan pelamar memakai satu jenis akun mahasiswa. Kewenangan ditentukan oleh kepemilikan data dan konteks tindakan.

Produk juga menyediakan direktori mentor mahasiswa dari data yang sudah disiapkan. Pengajuan mentoring diteruskan ke Google Form lewat browser. ETC tidak mengambil atau menampilkan status respons Google Form.

### 1.1 Tujuan Produk
- Menyediakan informasi kegiatan, posisi, skill, tenggat, kuota, dan kontak dalam satu aplikasi.
- Membantu mahasiswa mengelola rekrutmen dan menyeleksi lamaran sesuai kewenangannya.
- Memberikan riwayat lamaran dan hasil seleksi yang bisa dibaca kembali.
- Membantu mahasiswa menemukan informasi mentor dan membuka formulir mentoring.

### 1.2 Pernyataan Produk
Untuk mahasiswa yang membutuhkan anggota tim atau kesempatan bergabung dalam lomba dan proyek, ETC menyediakan aplikasi desktop dengan informasi posisi, pelamaran, dan seleksi dalam satu alur. Mahasiswa bisa merekrut pada satu kegiatan dan melamar pada kegiatan mahasiswa lain dengan akun yang sama.

## 2. Latar Belakang dan Permasalahan
Kebutuhan awalnya adalah mempertemukan mahasiswa pembuat rekrutmen dengan mahasiswa pencari rekrutmen. Informasi kegiatan perlu menjelaskan posisi dan persyaratan sebelum mahasiswa memutuskan melamar, dan hasil seleksi perlu tersimpan agar pembuat dan pelamar membaca status yang sama.

| ID | Permasalahan | Tanggapan produk |
| :-- | :-- | :-- |
| P-01 | Kebutuhan anggota tim perlu dijelaskan secara terstruktur. | Rekrutmen memuat kegiatan, posisi, skill, kuota, tenggat, biaya, dan kontak. |
| P-02 | Mahasiswa perlu memilah kesempatan yang relevan dan masih tersedia. | Pencarian, filter, detail posisi, bookmark, dan alasan jika pendaftaran tidak tersedia. |
| P-03 | Pembuat perlu meninjau pelamar sesuai posisi yang dilamar. | Daftar pelamar per posisi, profil, pesan, serta keputusan terima atau tolak. |
| P-04 | Pelamar perlu mengetahui hasil dan riwayat pengajuannya. | Halaman Lamaran saya berisi status, waktu pengajuan, dan akses ke detail kegiatan. |
| P-05 | Informasi mentor perlu bisa ditelusuri berdasarkan keahlian. | Direktori, foto, pencarian skill, detail, dan tautan formulir eksternal. |
| P-06 | Demonstrasi perlu bisa dijalankan di komputer lain. | Panduan README, schema tertanam, CLI init/seed, dan pemisahan kredensial SQL dari akun aplikasi. |

Belum ada riset pengguna, angka prevalensi, atau pengukuran waktu kerja sebelum memakai ETC. Pernyataan di atas adalah kebutuhan proyek, bukan hasil survei.

## 3. Sasaran, Indikator, dan Batasan

| Sasaran | Indikator yang bisa diperiksa |
| :-- | :-- |
| Rekrutmen bisa diselesaikan | Pemilik membuat draft, mengisi posisi, mempublikasikan, dan menutup rekrutmen sesuai aturan. |
| Pelamaran dan seleksi konsisten | Akun lain melamar, pemilik memutuskan, dan status terbaca kembali tanpa kuota berlebih. |
| Kepemilikan data terlindungi | Akun tanpa wewenang tidak bisa mengubah rekrutmen, membatalkan lamaran orang lain, atau memutuskan seleksi. |
| Ringkasan bisa dipercaya | Statistik akun sesuai query, nol tetap nol, bookmark dan kuota sesuai data tersimpan. |
| Informasi mentor bisa diakses | Profil, foto, skill, pencarian, pagination, dan tautan formulir tampil sesuai konfigurasi. |
| Setup bisa direproduksi | Source bersih bisa di-restore, di-build, diinisialisasi, di-seed, lalu dipakai login. |
| Interaksi bisa digunakan | Alur inti bisa dijalankan dengan keyboard, form mempertahankan isian saat gagal, layout tidak memotong tindakan utama. |

Indikator di atas adalah kriteria penerimaan rilis, bukan klaim hasil pengujian. Target jumlah pengguna dan waktu respons belum ditetapkan.

**Batasan proyek:** satu jenis akun mahasiswa, tema terang saja, dan koneksi SQL Server. Data diperbarui lewat pemuatan ulang, tanpa jaminan real-time. Layanan publik, pembayaran, komunikasi tim, dan operasional mentoring berada di luar lingkup awal.

## 4. Pengguna dan Hak Akses

| Pengguna | Kebutuhan dan hak akses | Batas kewenangan |
| :-- | :-- | :-- |
| Belum login | Membaca onboarding, register, dan login. | Tidak mengakses data pribadi atau halaman terlindungi. |
| Mahasiswa login | Mengelola profil sendiri, membaca profil publik, mencari rekrutmen, mengelola bookmark dan lamaran sendiri, melihat mentor. | Tidak membaca email login atau password lewat profil publik, tidak mengubah data akun lain. |
| Pembuat rekrutmen | Mengubah posting, melihat pelamar, memutuskan seleksi, dan menutup posting miliknya. | Kewenangan hanya pada rekrutmen miliknya, tidak bisa melamar posting sendiri. |
| Pelamar | Mengirim lamaran ke rekrutmen orang lain, membaca status, dan membatalkan lamaran yang masih menunggu. | Tidak bisa memutuskan seleksi atau mengubah status akhir. |
| Mentor di direktori | Data mentor yang disiapkan lewat sumber data. | Tidak punya akun, portal, atau kewenangan sendiri di rilis awal. |

Satu akun bisa menjadi pembuat pada rekrutmen A dan pelamar pada rekrutmen B. Otorisasi diperiksa di service sebelum membaca data privat atau menyimpan perubahan. Menyembunyikan tombol saja tidak cukup.

## 5. Ruang Lingkup Produk

### 5.1 In Scope: Rilis Awal
- Akun dan profil: onboarding, register/login, konfirmasi logout, sesi, profil, foto, skill, dan profil publik.
- Penemuan kegiatan: dashboard pribadi, pencarian, filter, pagination, detail, dan bookmark.
- Pengelolaan rekrutmen: draft, posisi, kuota, publikasi, pembaruan terkendali, penutupan, dan hapus draft.
- Lamaran dan seleksi: pengajuan, pembatalan, peninjauan, keputusan, kuota atomik, dan riwayat.
- Direktori mentor: kartu dengan foto, pencarian, pagination, detail, dan tombol ke Google Form.
- Kualitas interaksi: validasi, status UI, retry, keyboard, reflow desktop, waktu WIB, dan konfirmasi tindakan.
- Setup dan persistensi: init schema, seed demo, panduan antar-komputer, dan data tetap ada setelah aplikasi ditutup.

### 5.2 Out of Scope: Rilis Berikutnya
- Chat, video call, notifikasi email/push, dan komunikasi real-time.
- Pencocokan otomatis atau penilaian skill berbasis AI. Seleksi dilakukan pembuat rekrutmen.
- Workspace tim, pembagian tugas, dan pelacakan proyek setelah seleksi.
- Pembayaran, penagihan, atau pencairan biaya. Biaya hanya informasi.
- Penjadwalan mentoring, chat mentor, dan sinkronisasi respons Google Form.
- Panel admin, moderasi, verifikasi kampus, dan pengelolaan katalog lewat UI.
- Backend API, multi-tenant, sinkronisasi offline, dan SLA publik.
- Pemulihan password lewat email, SSO, remember-me, dan dark mode.

## 6. Struktur Navigasi dan Halaman

| Halaman | Isi dan tindakan | Akses |
| :-- | :-- | :-- |
| Onboarding | Penjelasan rekrutmen dan mentor, tombol Masuk dan Buat akun. | Belum login |
| Login | Email, password, validasi, dan akses ke register. | Belum login |
| Register | Nama lengkap, email, password, dan akses ke login. | Belum login |
| Dashboard | Empat statistik pribadi, rekrutmen dan lamaran terbaru, serta pintasan. | Mahasiswa login |
| Cari rekrutmen | Pencarian, kategori, posisi, skill, ketersediaan, hasil, dan pagination. | Mahasiswa login |
| Detail rekrutmen | Pemilik, kegiatan, posisi/kuota, tenggat, jadwal, biaya, kontak, dan formulir lamaran. | Mahasiswa (draft hanya pemilik) |
| Rekrutmen saya | Posting milik akun, status, buat/edit, publikasi, pelamar, tutup, dan hapus draft. | Pemilik data |
| Form rekrutmen | Kegiatan, editor posisi, jadwal/biaya, kontak, validasi, simpan draft, dan publikasi. | Pemilik data |
| Pelamar | Daftar per posisi, profil/pesan, kuota, Terima/Tolak, dan status final. | Pemilik rekrutmen |
| Lamaran saya | Kegiatan/posisi, waktu, status, detail, dan pembatalan lamaran pending. | Pemilik lamaran |
| Bookmark | Posting tersimpan, status terkini, buka detail, dan hapus bookmark. | Pemilik bookmark |
| Daftar mentor | Foto, nama, angkatan/fokus, skill, ringkasan, pencarian, dan pagination. | Mahasiswa login |
| Detail mentor | Identitas, foto, skill, pengalaman/pencapaian, portofolio, dan formulir. | Mahasiswa login |
| Profil saya | Identitas, foto, angkatan, skill, bio, pengalaman, dan tautan. | Pemilik profil |
| Profil publik | Nama, skill, pengalaman, foto, dan tautan tanpa email login atau password. | Mahasiswa login |

Menu utama berisi Dashboard, Cari rekrutmen, Rekrutmen saya, Lamaran saya, Bookmark, dan Mentor. Profil saya dan Keluar ada di bagian bawah navbar. Pada jendela sempit, menu berubah menjadi flyout yang bisa ditutup dengan Escape.

## 7. Kebutuhan Fungsional
Semua kebutuhan berikut berprioritas **Must** karena menjabarkan alur utama rilis.

| ID | Fitur | Persyaratan |
| :-- | :-- | :-- |
| FR-01 | Onboarding | Menjelaskan fungsi rekrutmen dan mentor serta menyediakan akses Masuk/Buat akun. |
| FR-02 | Registrasi | Daftar dengan nama, email valid dan unik, password 12 sampai 128 karakter. Password disimpan sebagai hash. |
| FR-03 | Login | Memverifikasi email/password dan menolak kredensial salah dengan pesan aman. |
| FR-04 | Logout | Meminta konfirmasi Keluar. Batal/Escape mempertahankan sesi. |
| FR-05 | Sesi dan otorisasi | Halaman privat hanya untuk sesi aktif, kepemilikan diperiksa di service, hasil async akun lama tidak muncul di akun baru. |
| FR-06 | Profil sendiri | Menyimpan nama, angkatan, skill, bio, pengalaman, foto, dan tautan untuk akun sendiri. |
| FR-07 | Kelengkapan profil | Nama, angkatan, dan minimal satu skill wajib sebelum membuat rekrutmen atau melamar. |
| FR-08 | Profil publik | Menampilkan profil tanpa email login, hash password, atau kontrol edit. |
| FR-09 | Dashboard | Menghitung rekrutmen aktif, lamaran pending, lamaran diterima, dan bookmark milik akun. |
| FR-10 | Cari dan filter | Mencari judul/kegiatan dan memfilter kategori, posisi, skill, serta ketersediaan. |
| FR-11 | Pagination | 12 item per halaman. Ganti filter kembali ke halaman pertama, Kembali dari detail memulihkan konteks daftar. |
| FR-12 | Detail rekrutmen | Menampilkan kegiatan, pemilik, posisi, skill, kuota, tenggat, jadwal, lokasi, biaya, dan kontak. Draft tidak terbaca akun lain. |
| FR-13 | Bookmark | Menambah/menghapus bookmark tanpa duplikasi. Posting yang ditutup tetap bisa dibaca dari bookmark. |
| FR-14 | Draft rekrutmen | Menyimpan dan mengubah draft. Kolom boleh belum lengkap, tapi nilai yang diisi tetap divalidasi. |
| FR-15 | Posisi dan kuota | Nama posisi, kuota bilangan bulat positif, dan skill dari katalog. Publikasi butuh minimal satu posisi. |
| FR-16 | Publikasi | Hanya pemilik yang bisa mempublikasikan draft lengkap dengan tenggat di masa depan. |
| FR-17 | Pembaruan posting | Setelah ada lamaran, posisi, skill, dan kuota terkunci. Perubahan tenggat mengikuti BR-09. |
| FR-18 | Penutupan | Menutup rekrutmen setelah konfirmasi. Lamaran baru berhenti, riwayat tetap ada. |
| FR-19 | Hapus draft | Menghapus draft setelah konfirmasi. Posting terpublikasi tidak bisa dihapus permanen. |
| FR-20 | Kirim lamaran | Memilih satu posisi, mengisi pesan dan portofolio opsional. Ketersediaan diperiksa ulang saat menyimpan. |
| FR-21 | Batas pelamaran | Menolak lamaran ke posting sendiri, duplikat, posisi salah, posting ditutup, tenggat lewat, dan posisi penuh. |
| FR-22 | Pembatalan | Pelamar membatalkan lamaran pending miliknya. Status akhir tidak bisa dibatalkan. |
| FR-23 | Peninjauan pelamar | Hanya pemilik yang membaca daftar pelamar per posisi beserta profil, pesan, dan status. |
| FR-24 | Keputusan seleksi | Pemilik menerima atau menolak lamaran pending setelah konfirmasi. |
| FR-25 | Integritas kuota | Pemeriksaan kuota dan keputusan berjalan atomik agar penerimaan tidak melebihi kapasitas. |
| FR-26 | Lamaran saya | Riwayat lamaran akun aktif dengan posisi, kegiatan, waktu, status, dan aksi sesuai status. |
| FR-27 | Direktori mentor | Kartu seragam dengan foto, identitas, skill, fokus, dan ringkasan pengalaman. |
| FR-28 | Pencarian mentor | Mencari nama/skill, tombol skill sebagai pencarian, reset, dan pagination. |
| FR-29 | Detail mentor | Foto dan informasi lengkap, keterangan jika pengalaman kosong, portofolio jika tersedia. |
| FR-30 | Form mentoring | Tombol Butuh mentor? membuka Google Form di browser. URL kosong menonaktifkan tombol dengan alasan. |
| FR-31 | Validasi input | Kolom wajib, panjang, email, angkatan, tanggal, biaya, skill, foto, dan URL HTTP/HTTPS divalidasi di UI dan service. |
| FR-32 | Loading dan kegagalan | State loading, kosong, error, dan sukses. Mencegah kirim ganda, mempertahankan isian saat gagal, menyediakan retry. |
| FR-33 | Navigasi aksesibel | Label input, fokus terlihat, Tab/Shift+Tab/Enter/Escape, dan reflow desktop. |
| FR-34 | Init dan seed | CLI menginisialisasi schema. Seed hanya menerima database `ETC_Demo`. |
| FR-35 | Persistensi | Data tetap ada setelah aplikasi ditutup. Seed ulang mempertahankan data lama dan memperbarui mentor berdasarkan NRP. |
| FR-36 | Waktu | Waktu disimpan dalam UTC dan ditampilkan dengan label WIB. |
| FR-37 | Status dan alasan | Status rekrutmen dibedakan dari ketersediaan posisi, dengan alasan tenggat lewat, penuh, atau ditutup. |
| FR-38 | Visual konsisten | Tema terang saja, permukaan daftar opaque, ikon berlabel, profil biru dan Keluar merah di navbar. |
| FR-39 | Konfirmasi dan umpan balik | Dialog konfirmasi untuk tindakan penting dan banner Berhasil setelah penyimpanan sukses. |

### 7.1 Aturan Bisnis
| ID | Aturan |
| :-- | :-- |
| BR-01 | Satu jenis akun mahasiswa. Peran pembuat atau pelamar diturunkan dari kepemilikan data. |
| BR-02 | Rekrutmen: Draft, Dibuka, lalu Ditutup. Tidak ada pembukaan kembali di rilis awal. |
| BR-03 | Lamaran: Menunggu, lalu Diterima, Ditolak, atau Dibatalkan. Status akhir tidak bisa diubah. |
| BR-04 | Satu mahasiswa maksimal satu lamaran per rekrutmen, termasuk setelah ditolak atau dibatalkan. |
| BR-05 | Pembuat tidak bisa melamar posting sendiri. Profil wajib lengkap. |
| BR-06 | Lamaran baru sah jika status Dibuka, belum lewat tenggat, dan kuota posisi belum penuh. |
| BR-07 | Tenggat lewat tidak otomatis mengubah status menjadi Ditutup. Posisi penuh tidak menutup posisi lain. |
| BR-08 | Setelah ada lamaran, posisi, skill, dan kapasitas terkunci. Informasi kegiatan dan kontak tetap bisa diubah. |
| BR-09 | Tenggat posting terpublikasi hanya bisa diubah jika masih Dibuka, belum lewat, dan masih ada kuota. |
| BR-10 | Lamaran yang sudah masuk tetap bisa diputuskan setelah posting ditutup, dengan tetap mematuhi kuota. |
| BR-11 | Kuota menghitung lamaran Diterima. Penerimaan memakai transaksi dan penguncian. |
| BR-12 | Skill hanya informasi dan filter. Tidak ada skor kecocokan otomatis. |
| BR-13 | Biaya Gratis disimpan nol, Belum diketahui tidak dianggap nol, Berbayar butuh nilai positif dan keterangan. |
| BR-14 | Profil publik memakai data terkini, tanpa email login dan hash password. |
| BR-15 | Bookmark unik per mahasiswa dan rekrutmen. Menghapus bookmark tidak menghapus posting. |
| BR-16 | Data mentor berasal dari tabel dan foto yang diberikan, tanpa rating atau klaim verifikasi buatan. |
| BR-17 | Google Form dan portofolio dibuka di luar aplikasi, tanpa sinkronisasi respons. |
| BR-18 | Seed pertama membuat akun demo dengan password bawaan. Seed ulang tidak mengganti password akun lama. |

## 8. User Stories dan Kriteria Penerimaan

**US-01: Mendaftar akun**  
Sebagai mahasiswa baru, saya ingin memahami produk dan mendaftar agar punya akun.
- Onboarding memberi akses ke halaman akun.
- Data valid menghasilkan satu akun, email duplikat ditolak.
- Password tidak disimpan sebagai teks biasa.

**US-02: Login dan batas akses**  
Sebagai mahasiswa, saya ingin login dan hanya mengakses data yang menjadi hak saya.
- Kredensial benar membuka sesi, kredensial salah ditolak.
- Halaman privat dan service menolak akses tanpa sesi atau kepemilikan.
- Ganti akun tidak memunculkan data akun lama.

**US-03: Konfirmasi logout**  
Sebagai mahasiswa, saya ingin mengonfirmasi logout agar tidak keluar tanpa sengaja.
- Dialog menampilkan Keluar dan Batal.
- Batal atau Escape tidak mengubah sesi.

**US-04: Melengkapi profil**  
Sebagai mahasiswa, saya ingin melengkapi profil agar bisa merekrut atau melamar.
- Nama, angkatan, dan minimal satu skill wajib.
- Foto atau tautan yang tidak valid ditolak, isian tetap ada.
- Profil belum lengkap menghalangi buat rekrutmen dan melamar, dengan arahan yang jelas.

**US-05: Profil publik**  
Sebagai mahasiswa, saya ingin membaca profil publik untuk menilai pembuat atau pelamar.
- Nama, skill, pengalaman, foto, dan tautan tampil.
- Email login dan password tidak muncul, profil orang lain tidak bisa diedit.

**US-06: Dashboard**  
Sebagai mahasiswa, saya ingin membaca ringkasan aktivitas akun saya.
- Empat statistik sesuai query akun aktif, data kosong tampil sebagai nol.
- Pintasan membuka daftar yang benar.

**US-07: Mencari rekrutmen**  
Sebagai pencari tim, saya ingin mencari dan memfilter posisi yang tersedia.
- Hasil mengikuti kombinasi kata kunci, kategori, skill, posisi, dan ketersediaan.
- Hasil dipaginasi 12 item, pencarian tanpa hasil punya pesan dan tombol Hapus filter.
- Kembali dari detail memulihkan halaman, filter, dan posisi scroll.

**US-08: Detail sebelum melamar**  
Sebagai calon pelamar, saya ingin membaca detail dan alasan ketersediaan sebelum melamar.
- Tenggat pendaftaran terpisah dari jadwal kegiatan, waktu ditampilkan dalam WIB.
- Posisi penuh, tenggat lewat, dan posting ditutup punya alasan yang terlihat.

**US-09: Bookmark**  
Sebagai mahasiswa, saya ingin menyimpan posting untuk dibaca kembali.
- Simpan berulang tidak membuat duplikat.
- Posting yang ditutup tetap bisa dibuka tanpa mengaktifkan lamaran.

**US-10 sampai US-13: Mengelola rekrutmen**  
Sebagai pembuat, saya ingin menyimpan draft, mempublikasikan, memperbarui, dan menutup rekrutmen.
- Draft tidak terlihat oleh akun lain, kapasitas posisi positif, skill dari katalog.
- Hanya pemilik yang bisa publikasi, dan publikasi berhasil muncul di pencarian.
- Setelah ada lamaran, perubahan posisi, skill, dan kuota ditolak.
- Penutupan menolak lamaran baru tapi mempertahankan riwayat. Hanya draft milik sendiri yang bisa dihapus.

**US-14 dan US-15: Melamar dan membatalkan**  
Sebagai pelamar, saya ingin mengirim satu lamaran dan membatalkannya selama belum diputuskan.
- Lamaran valid tersimpan sebagai Menunggu.
- Posting sendiri, duplikat, posisi penuh, tenggat lewat, dan posting ditutup ditolak.
- Hanya lamaran pending milik sendiri yang bisa dibatalkan, dan pembatalan tidak membuka kesempatan melamar ulang.

**US-16 dan US-17: Seleksi pelamar**  
Sebagai pembuat, saya ingin meninjau pelamar per posisi dan memutuskan tanpa melampaui kuota.
- Hanya pemilik yang bisa membaca daftar pelamar.
- Lamaran pending bisa diterima atau ditolak dengan konfirmasi, status final tidak bisa diubah.
- Jika dua penerimaan terjadi untuk satu slot tersisa, maksimal satu yang berhasil.

**US-18: Riwayat lamaran**  
Sebagai pelamar, saya ingin membaca riwayat dan hasil seleksi saya.
- Daftar hanya berisi lamaran akun aktif, hasil terbaru terlihat setelah dimuat ulang.

**US-19 dan US-20: Mentor**  
Sebagai mahasiswa, saya ingin mencari mentor berdasarkan nama atau keahlian dan membuka formulir pengajuan.
- Setiap mentor yang punya foto menampilkan fotonya, ukuran kartu konsisten.
- Tombol skill menjalankan pencarian, reset dan pagination bekerja.
- URL formulir valid membuka browser, URL kosong menampilkan alasan.

**US-21 sampai US-24: Kualitas interaksi**  
Sebagai pengguna, saya ingin tahu input yang salah, bisa melanjutkan setelah gagal, memakai keyboard, dan mendapat kepastian hasil tindakan.
- Kesalahan input ditunjukkan dekat kolomnya, URL berbahaya dan foto di luar batas ditolak.
- Gagal simpan mempertahankan isian dan menyediakan retry.
- Keyboard dan fokus bekerja, status tidak hanya dibedakan dengan warna.
- Banner Berhasil hanya muncul setelah penyimpanan sukses dan bisa ditutup dengan keyboard.

## 9. Kebutuhan UI/UX dan Design System

| Aspek | Ketentuan |
| :-- | :-- |
| Tema | Hanya tema terang, tanpa toggle dark mode. |
| Warna | Navy `#0B1F3A`, aksen kuning `#F8C832`, kanvas `#F1F4F8`, dan permukaan putih `#FFFFFF`. |
| Navbar akun | Profil saya biru `#1D4ED8`, Keluar merah `#B91C1C`, latar transparan seperti menu lain. |
| Warna status | Diterima, Ditolak, Menunggu, dan Dibatalkan selalu punya label teks. Warna hanya membantu. |
| Tipografi | DM Sans dibundel lokal. Judul 32, section 18, isi 14, metadata 12, angka statistik 24 (DIP). |
| Ikon | Fluent System Icons Regular, umumnya 16 DIP, selalu didampingi label. |
| Layout | Sidebar berlabel, top bar, area konten, dan tombol Kembali. |
| Komponen | Kartu dan daftar opaque, form dengan error dekat input, dialog dengan Batal sebagai fokus awal untuk tindakan berisiko. |
| Status UI | Memuat, kosong, gagal membaca, gagal menyimpan, sukses, dan tidak tersedia, masing-masing dengan pesan dan tindakan yang jelas. |
| Motion | Gerak singkat untuk hover, press, atau perubahan konteks. Reduced motion dihormati. |
| Aksesibilitas | Label jelas, fokus terlihat, kontras minimal 4,5:1 untuk teks biasa dan 3:1 untuk teks besar. |
| Responsivitas | Minimum 800 x 560 DIP. Di bawah 1100 DIP navbar menjadi flyout, mulai 1400 DIP dashboard dan detail bisa dua kolom. |

## 10. Kebutuhan Nonfungsional

| ID | Kategori | Persyaratan | Prioritas |
| :-- | :-- | :-- | :-- |
| NFR-01 | Performa UI | Operasi I/O memakai async agar UI tetap responsif saat database lambat. | Must |
| NFR-02 | Performa data | Daftar dipaginasi 12 item. Target latensi belum ditetapkan. | Must |
| NFR-03 | Keamanan akun | Password di-hash dengan PasswordHasher dari Identity. | Must |
| NFR-04 | Keamanan query | Query berparameter, ID, enum, dan URL divalidasi. | Must |
| NFR-05 | Otorisasi | Akses data privat diperiksa di service, termasuk panggilan yang melewati UI. | Must |
| NFR-06 | Privasi | UI tidak menampilkan stack trace, CLI tidak menampilkan password atau connection string. | Must |
| NFR-07 | Integritas | PK, FK, unique, check constraint, dan transaksi mencegah duplikasi dan kuota berlebih. | Must |
| NFR-08 | Persistensi | Data yang dikomit bertahan, transaksi gagal tidak meninggalkan perubahan sebagian. | Must |
| NFR-09 | Keandalan | Gagal baca/simpan tidak menutup aplikasi, retry tidak menggandakan data. | Must |
| NFR-10 | Kompatibilitas | Ubuntu desktop x64 wajib. Windows x64 sebagai target tambahan (Should). | Must |
| NFR-11 | Aksesibilitas | Label, keyboard, fokus, dan kontras diverifikasi saat runtime. | Must |
| NFR-12 | Skala tampilan | Layout pada ukuran minimum dan skala OS tidak memotong input atau tindakan. | Must |
| NFR-13 | Preferensi gerak | Reduced motion menghilangkan gerak yang tidak perlu tanpa menghilangkan fungsi. | Must |
| NFR-14 | Pemeliharaan | View, ViewModel, model, service, dan akses SQL punya tanggung jawab terpisah. | Must |
| NFR-15 | Konfigurasi | Koneksi dibaca dari environment, init/seed gagal dengan exit code nonnol. | Must |
| NFR-16 | Waktu | Batas pendaftaran dihitung dalam UTC, tampilan memakai WIB. | Must |
| NFR-17 | Logging | Usulan log teknis dengan redaksi data sensitif. | Should |
| NFR-18 | Benchmark | Usulan pengukuran waktu baca/simpan sebelum menjanjikan SLA. | Should |
| NFR-19 | Akses SQL | Usulan akun runtime SQL dengan hak terbatas. | Should |
| NFR-20 | Backup | Usulan prosedur backup dan retensi jika data dipakai berkelanjutan. | Should |

## 11. Rancangan Data Awal

| Entitas / tabel | Atribut inti | Catatan |
| :-- | :-- | :-- |
| Students | Id, FullName, Email, NormalizedEmail, PasswordHash, Cohort, Bio, Experience, PortfolioUrl, LinkedInUrl, Photo | Email unik setelah normalisasi, password input 12 sampai 128 karakter. |
| Skills | Id, Name | Nama unik maksimal 80, disiapkan lewat data awal. |
| StudentSkills | StudentId, SkillId | Pasangan unik, minimal satu skill untuk profil lengkap. |
| Recruitments | Id, OwnerId, Title, Category, ActivityName, Description, DeadlineUtc, StartsOn, EndsOn, Mode, Location, FeeKind, FeeAmount, FeeNote, ContactName, ContactChannel, Status | Publikasi mengikuti FR-16 dan BR-13. |
| Positions | Id, RecruitmentId, Name, Capacity | Kapasitas integer positif. Jumlah diterima dihitung dari lamaran. |
| PositionSkills | PositionId, SkillId | Posisi terpublikasi wajib punya skill. |
| Applications | Id, StudentId, RecruitmentId, PositionId, Message, PortfolioUrl, Status, SubmittedAtUtc, DecidedAtUtc | Pasangan mahasiswa dan rekrutmen unik. |
| Bookmarks | StudentId, RecruitmentId | PK gabungan mencegah duplikasi. |
| Mentors | Id, Name, StudentNumber, Cohort, Focus, Photo, Experience, Achievements | NRP unik, dipakai sebagai kunci saat import. |
| MentorSkills | MentorId, SkillId | Mendukung pencarian mentor berdasarkan keahlian. |
| SchemaVersion | Version | Penanda migrasi, script init aman dijalankan ulang. |

### 11.1 Relasi Utama
```
Students       1 ───── * Recruitments     (pemilik rekrutmen)
Recruitments   1 ───── * Positions
Students       1 ───── * Applications
Positions      1 ───── * Applications     (FK gabungan dengan RecruitmentId)
Students       * ───── * Skills           (lewat StudentSkills)
Positions      * ───── * Skills           (lewat PositionSkills)
Students       * ───── * Recruitments     (bookmark, lewat Bookmarks)
Mentors        * ───── * Skills           (lewat MentorSkills)
```

Database memakai SQL Server dengan database `ETC_Demo`. Integration test memakai database terpisah `ETC_Test`. Koneksi tidak disimpan di source. Data seed berisi 3 akun demo, 8 rekrutmen, 7 lamaran, dan 15 mentor beserta foto. Angka ini berasal dari seeder, bukan target pengguna.

## 12. Rekomendasi Arsitektur Teknis

| Lapisan | Tanggung jawab | Contoh |
| :-- | :-- | :-- |
| View | Layout AXAML, binding, dan interaksi visual. | `Features/*/*View.axaml`, `ShellView` |
| ViewModel | State halaman, command, validasi tampilan, dan pemanggilan service. | `ProfileViewModel`, `SearchViewModel`, `ApplicantsViewModel` |
| Model | Record input, proyeksi data, dan enum status. | `ProfileModels`, `RecruitmentModels`, `ApplicationModels` |
| Service | Sesi, hak akses, validasi bisnis, transaksi, dan query. | `AccountService`, `RecruitmentService`, `ApplicationService` |
| Infrastructure | Koneksi, parameter SQL, pemetaan hasil, dan initializer schema. | `SqlConnectionFactory`, `Db`, `DatabaseInitializer` |
| Shared | State sesi, navigasi, error domain, dan guard. | `SessionState`, `ShellViewModel`, `Guard` |
| Composition root | Menyusun dependensi, navigasi, dan dialog konfirmasi. | `CompositionRoot.cs`, `Program.cs` |

Alur data: View → ViewModel → Service → ADO.NET → SQL Server → proyeksi data → ViewModel → binding UI.

Stack: .NET 10, Avalonia 12.1.3, CommunityToolkit.Mvvm 8.4.2, Microsoft.Data.SqlClient 7.1.1, dan SQL Server 2022 Developer di Docker. Konfigurasi aplikasi dibaca dari `ETC_CONNECTION_STRING`, dan `ETC_MENTOR_FORM_URL` bisa mengganti URL Google Form bawaan.

## 13. Alur Pengguna Utama

**Akun dan profil**
1. Mahasiswa membuka onboarding, memilih Buat akun, lalu mengisi nama, email, dan password.
2. Sistem menyimpan akun, lalu mahasiswa masuk lewat login.
3. Mahasiswa melengkapi angkatan dan skill di Profil saya.
4. Setelah profil lengkap, mahasiswa bisa membuat rekrutmen atau melamar.

**Merekrut dan menyeleksi**
1. Pembuat membuka Rekrutmen saya dan memilih Buat rekrutmen.
2. Pembuat menyimpan draft, mengisi posisi, skill, kuota, informasi kegiatan, biaya, kontak, dan tenggat.
3. Pembuat mengonfirmasi publikasi, sistem memvalidasi ulang lalu mengubah status menjadi Dibuka.
4. Pembuat membuka daftar pelamar per posisi, membaca pesan dan profil, lalu memilih Terima atau Tolak.
5. Sistem memeriksa status dan kuota, lalu menyimpan keputusan secara atomik.
6. Pembuat bisa menutup rekrutmen tanpa menghapus riwayat.

**Mencari dan melamar**
1. Mahasiswa membuka Cari rekrutmen. Filter awal hanya menampilkan posisi yang masih menerima lamaran.
2. Mahasiswa membuka detail dan menyimpan bookmark jika perlu.
3. Mahasiswa memilih posisi, mengisi pesan, lalu mengirim lamaran.
4. Status Menunggu terlihat di Lamaran saya dan bisa dibatalkan sebelum diputuskan.
5. Setelah dimuat ulang, mahasiswa membaca hasil Diterima atau Ditolak.

**Mentor**
1. Mahasiswa membuka Mentor, lalu mencari nama atau keahlian.
2. Mahasiswa membuka detail mentor dan memilih Butuh mentor?.
3. Google Form terbuka di browser. Pengisian dan respons formulir berlangsung di luar ETC.

## 14. Rencana Implementasi Bertahap

| Fase | Aktivitas | Deliverable |
| :-- | :-- | :-- |
| Fase 1: Baseline | Menyelaraskan scope, aturan status dan akses, serta pertanyaan terbuka. | PRD ditinjau, referensi FR/US/TC konsisten. |
| Fase 2: Fondasi | Stack, composition root, schema, konfigurasi, seed, dan setup bersih. | Build dan init/seed berhasil di SQL terpisah. |
| Fase 3: Akun dan profil | Akun, sesi, otorisasi, validasi profil, foto, dan profil publik. | US-01 sampai US-05 lulus. |
| Fase 4: Rekrutmen | Pencarian, detail, bookmark, draft, posisi, publikasi, tutup, dan hapus. | US-07 sampai US-13 lulus. |
| Fase 5: Seleksi | Pengajuan, pembatalan, peninjauan, kuota atomik, dan dashboard. | US-06 dan US-14 sampai US-18 lulus. |
| Fase 6: Mentor dan UI | Direktori, foto, detail, formulir, state UI, keyboard, kontras, dan layout. | US-19 sampai US-23 lulus. |
| Fase 7: Verifikasi | Regresi, pengujian lintas komputer, perbaikan, dan README. | Kriteria Must lulus dan bukti tercatat. |

## 15. Strategi Pengujian
Pengujian dilakukan di beberapa tingkat: unit test, integration test SQL di database `ETC_Test`, UI headless, pengujian manual native di Ubuntu, dan setup bersih dari source tanpa `bin`/`obj`. PRD mendefinisikan 62 skenario (TC-01 sampai TC-62), diringkas per area di bawah.

| Area | Skenario minimum | Hasil yang diharapkan |
| :-- | :-- | :-- |
| Akun | Registrasi, email duplikat, batas password, login benar dan salah, logout. | Satu akun per email, password tersimpan sebagai hash, pesan aman. |
| Otorisasi | Akun B mencoba mengedit atau menyeleksi milik akun A lewat service. | Operasi ditolak tanpa perubahan data. |
| Profil | Simpan profil valid, profil belum lengkap, baca profil orang lain. | Profil tersimpan, aksi diblokir jika belum lengkap, email tidak bocor. |
| Pencarian | Kata kunci, filter, karakter wildcard, lebih dari 12 item, Kembali dari detail. | Hasil sesuai filter, pagination benar, konteks daftar pulih. |
| Rekrutmen | Draft, kuota nol/negatif, publikasi lengkap dan tidak lengkap, edit setelah ada lamaran. | Nilai tidak valid ditolak, posisi terkunci setelah ada lamaran. |
| Lamaran | Melamar posting sendiri, lamaran ganda, posting ditutup, pembatalan. | Pelanggaran aturan ditolak tanpa duplikasi. |
| Konkurensi | Dua penerimaan bersamaan untuk satu slot tersisa. | Maksimal satu berhasil, kuota tidak terlampaui. |
| Mentor | Pencarian, kartu dengan nama panjang, URL formulir valid dan kosong. | Kartu seragam, browser hanya terbuka untuk URL sah. |
| Validasi | Kolom wajib, batas panjang, URL non-HTTP, foto rusak atau lebih dari 2 MiB. | Input invalid ditolak dengan pesan yang bisa diperbaiki. |
| Koneksi | SQL diputus atau diperlambat saat membaca dan menyimpan. | UI tetap merespons, isian tidak hilang, tidak ada sukses palsu. |
| Aksesibilitas | Alur inti dengan keyboard, tema OS gelap, tiga ukuran jendela. | Fokus terlihat, aplikasi tetap terang, tidak ada tindakan terpotong. |
| Setup | Restore, build, init, dan seed di SQL baru, seed ke database lain. | Akun demo bisa login, target database salah ditolak. |
| Notifikasi | Simpan berhasil, simpan gagal, dialog dibatalkan. | Banner Berhasil hanya muncul setelah penyimpanan sukses. |

## 16. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
| :-- | :-- | :-- |
| Kredensial SQL berbeda | Init, login, atau seed gagal meski container berjalan. | Tes login SQL, tampilkan kode error aman, samakan environment terminal. |
| Volume SQL lama | Password container baru tidak cocok dengan akun SQL tersimpan. | Dokumentasikan perilaku volume, pakai volume baru untuk setup kosong. |
| Kuota diperebutkan | Dua keputusan bisa melebihi kapasitas. | Transaksi, penguncian, dan pemeriksaan ulang. |
| Otorisasi hanya di UI | Data milik akun lain bisa diakses lewat service. | Periksa sesi dan kepemilikan di service, uji akses negatif. |
| State async usang | Data akun lama muncul setelah logout. | Cancellation dan revisi sesi. |
| Sumber mentor berubah | Nama atau foto salah, profil ganda. | NRP sebagai kunci import, validasi path dan foto. |
| Browser atau form eksternal gagal | Pengguna mengira ETC menyimpan pengajuan. | Validasi URL dan penjelasan bahwa proses berlangsung di luar ETC. |
| Perbedaan OS dan skala | Font, dialog, atau layout berbeda antar komputer. | Aset lokal dan pengujian native per OS. |
| Perluasan scope | Chat, pembayaran, atau admin menggeser fokus. | Batas scope dan persetujuan sebelum menambah fitur. |
| Data test tercampur | Fixture menghapus data aplikasi. | Database `ETC_Test` terpisah dengan guard nama. |

## 17. Asumsi dan Keputusan yang Masih Dibutuhkan
- Dataset benchmark, perangkat acuan, dan target waktu respons.
- Versi Ubuntu dan Windows minimum yang dijanjikan.
- Rubrik kuliah, tenggat, dan identitas pengesah.
- Istilah dan kategori biaya yang dibakukan.
- Apakah demonstrasi memakai SQL per komputer, SQL bersama, atau menjadi produk publik.
- Lokasi, format, dan retensi log persisten.
- Pemilik pembaruan data mentor dan Google Form.
- Kebutuhan backup dan pemulihan jika data dipakai setelah demonstrasi.
- Batas penemuan profil publik di luar konteks rekrutmen.
- Collation dan aturan pencarian huruf besar/kecil lintas instance SQL.

## 18. Definisi Selesai (Definition of Done)
- Ruang lingkup rilis dan keputusan yang memblokir demonstrasi sudah disahkan.
- Semua FR Must punya implementasi, user story, dan skenario uji yang lulus.
- Akun, sesi, privasi, validasi, dan pengujian akses negatif lulus.
- Status rekrutmen dan lamaran, duplikasi, tenggat, kunci posisi, dan kuota atomik memenuhi aturan.
- Dashboard, kuota, dan status berasal dari data, dan data bertahan setelah aplikasi ditutup.
- Mentor, foto, pencarian, dan tautan mengikuti data sumber.
- Loading, kosong, error, retry, konfirmasi, dan notifikasi sukses sudah diuji.
- Tema terang, label, kontras, fokus, reflow, dan reduced motion sudah diverifikasi.
- Source bersih, schema, aset, README, serta init/seed/run diuji di lingkungan tujuan.
- Tidak ada kredensial pribadi di source atau log.
- Dukungan OS dan performa yang belum diuji tidak diklaim lulus.

### Lampiran: Checklist Persetujuan PRD

| Peran | Nama dan Tanggal | Status |
| :-- | :-- | :-- |
| Pemilik Produk | Perlu Keputusan | Belum disahkan |
| Desainer/Developer | Perlu Keputusan | Belum disahkan |
| QA/Perwakilan Pengguna | Perlu Keputusan | Belum disahkan |
