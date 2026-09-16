# Week 02 — Implementasi Kalkulator GUI dengan Windows Forms

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  

## 1. Tujuan
Membangun aplikasi desktop berbasis antarmuka grafis (GUI) menggunakan Windows Forms pada platform .NET. Fokus pada pengelolaan interaksi pengguna melalui sistem kendali *event-driven*, modifikasi properti komponen secara terprogram, serta implementasi logika aritmatika standar dan lanjutan.

## 2. Struktur Eksekusi Utama: `CalculatorProgram.cs`
Berkas ini bertindak sebagai titik masuk (*entry point*) aplikasi yang mengatur inisialisasi lingkungan antarmuka sebelum memuat form utama.

**Penjelasan Teknis:**
* `[STAThread]`: Atribut wajib untuk aplikasi Windows Forms yang menginstruksikan sistem operasi agar menggunakan model *Single-Threaded Apartment*, memastikan komponen UI dapat berkomunikasi dengan aman tanpa konflik memori.
* `ApplicationConfiguration.Initialize()`: Menerapkan konfigurasi visual modern bawaan sistem operasi.
* `Application.Run(new Form1())`: Menginstansiasi objek form utama dan memasukkannya ke dalam siklus pemrosesan *message loop* agar program terus berjalan hingga jendela ditutup.

## 3. Pusat Kendali Logika dan UI: `Form1.cs`
Berkas ini menangani seluruh *event handler* dari interaksi tombol serta modifikasi tampilan secara dinamis.

**Pembaruan Fitur Lanjutan:**
* **Modifikasi Tema Dinamis (`ApplyBlueTheme`)**: Fungsi iteratif yang menyisir struktur kendali (`Controls`) pada form saat aplikasi dimuat. Fungsi ini memodifikasi atribut `BackColor`, `ForeColor`, dan `FlatStyle` pada setiap elemen untuk menghasilkan tema visual dominan biru secara seragam tanpa perlu mengubah properti di desainer satu per satu.
* **Kuadrat dan Akar (`Math.Pow` & `Math.Sqrt`)**: Memanfaatkan pustaka matematika bawaan .NET untuk mengeksekusi operasi pangkat dua dan akar kuadrat langsung pada nilai yang tertera di layar. Dilengkapi dengan validasi kondisi (`value >= 0`) untuk mencegah perhitungan akar dari bilangan negatif.
* **Persentase (`%`)**: Mengkonversi nilai yang sedang aktif di layar menjadi representasi desimal (dibagi 100) tanpa memerlukan operan kedua.
* **Toggling Negasi (`+/-`)**: Memanipulasi string secara langsung menggunakan `.StartsWith("-")` dan `.Substring(1)` untuk menginversi tanda bilangan tanpa mengubah nilai absolutnya.

## 4. Penanganan Kesalahan (Exception Handling)
Implementasi blok `try-catch` disertakan pada proses kalkulasi akhir (tombol sama dengan) untuk menangkal penghentian aplikasi secara paksa (*crash*). Kasus spesifik seperti `DivideByZeroException` ditangkap secara manual ketika pengguna mencoba membagi nilai dengan angka 0, lalu merespons dengan memunculkan `MessageBox` berisi pesan peringatan.

## 5. Dokumentasi Pengujian
Berikut adalah hasil kompilasi dan eksekusi program dengan fitur tambahan dan tema biru:

### Tampilan Utama Kalkulator
<kbd><img src="./assets/main-view.png" width="600"></kbd>

### Uji Coba Fitur Tambahan (Akar/Kuadrat/Persen)
<kbd><img src="./assets/advanced-calc.png" width="600"></kbd>

### Penanganan Pembagian dengan Nol
<kbd><img src="./assets/divide-zero.png" width="600"></kbd>

