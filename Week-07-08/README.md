# Week 07 & 08 - ETS: ETC (Elite TC Champions)

**Nama**: Hosea Felix Sanjaya  
**NRP**: 5025241177  
**Kelas**: PBKK D


| | |
| :-- | :-- |
| PRD | [PRD.pdf](https://github.com/abidalfrz/ETC/blob/main/PRD.pdf) |
| Repository | [github.com/abidalfrz/ETC](https://github.com/abidalfrz/ETC) |
| Video Demo | [youtu.be/fST3n_nGM44](https://youtu.be/fST3n_nGM44) |

## Video Demo

[![Demo ETC](https://img.youtube.com/vi/fST3n_nGM44/maxresdefault.jpg)](https://youtu.be/fST3n_nGM44)

## 1. Tentang Project
ETC (Elite TC Champions) adalah aplikasi desktop untuk membantu mahasiswa mencari anggota tim, menemukan rekrutmen lomba atau proyek, dan mencari mentor sesuai keahlian. Satu akun bisa membuat rekrutmen sendiri sekaligus melamar ke rekrutmen mahasiswa lain.

Aplikasi dibuat dengan C#, .NET 10, dan Avalonia UI. Target utamanya Ubuntu, dan Windows sebagai target tambahan. Data disimpan di SQL Server dan diakses lewat ADO.NET dengan paket `Microsoft.Data.SqlClient`.

## 2. Fitur Utama
- Registrasi, login, logout, dan pengelolaan profil mahasiswa
- Profil berisi skill, pengalaman, foto, bio, dan tautan portofolio
- Dashboard berisi statistik pribadi
- Membuat, mengedit, mempublikasikan, dan menutup rekrutmen
- Rekrutmen berisi posisi, kuota, kategori lomba atau proyek, jadwal, biaya, dan kontak
- Pencarian, filter, pagination, dan bookmark rekrutmen
- Mengirim dan membatalkan lamaran, lalu pemilik rekrutmen menerima atau menolaknya
- Halaman Lamaran saya dan daftar pelamar untuk rekrutmen sendiri
- Direktori mentor yang bisa dicari berdasarkan nama atau keahlian
- Tombol ke Google Form untuk pengajuan mentoring
- Validasi input, pembatasan akses berdasarkan pemilik data, dan validasi kuota
- Schema dan data demo SQL Server yang disiapkan lewat perintah CLI
- Tampilan memakai Fluent theme dari Avalonia

## 3. Teknologi dan Arsitektur

| Komponen | Teknologi |
| :-- | :-- |
| Bahasa dan runtime | C#, .NET 10 |
| Antarmuka desktop | Avalonia UI |
| Pola aplikasi | MVVM dan data binding |
| Command | CommunityToolkit.Mvvm |
| Database | Microsoft SQL Server |
| Akses data | ADO.NET dan `Microsoft.Data.SqlClient` |
| Infrastruktur lokal | Docker |
| Pengujian | Unit test dan integration test .NET |

Tampilan, state, aturan bisnis, dan akses database dipisah. ViewModel menangani state dan command, service menjalankan proses bisnis, dan Infrastructure mengurus koneksi serta inisialisasi SQL Server.

## 4. Struktur Project

```
ETC/
├── database/
│   ├── 001-schema.sql
│   ├── 002-catalog.sql
│   └── 003-mentor-directory.sql
├── src/Etc.Desktop/
│   ├── Features/              # Akun, profil, dashboard, rekrutmen, lamaran, bookmark, mentor
│   ├── Infrastructure/        # Koneksi dan inisialisasi database
│   ├── Shared/                # Komponen dan state bersama
│   ├── Styles/                # Token dan style Avalonia
│   ├── Assets/                # Foto mentor, ikon, font, dan data pendukung
│   ├── App.axaml
│   ├── MainWindow.axaml
│   └── Program.cs
├── tests/
│   ├── Etc.UnitTests/
│   └── Etc.IntegrationTests/
├── tools/Etc.RenderChecks/
├── Etc.slnx
├── PRD.pdf
├── README.md
├── global.json
└── Directory.Build.props
```

Database utama berisi tabel mahasiswa, skill, rekrutmen, posisi, lamaran, bookmark, dan mentor. File SQL di folder `database/` ditanam ke aplikasi saat build.

## 5. Cara Menjalankan

### Prasyarat
- SDK .NET 10 sesuai `global.json`
- Docker Engine di Ubuntu, atau Docker Desktop di Windows
- Sesi desktop untuk menampilkan window Avalonia

Cek instalasi:

```bash
dotnet --version
docker version
```

### 5.1 Jalankan SQL Server
Contoh di bawah memakai password sementara. Kalau passwordnya diganti, pakai password yang sama di connection string.

```bash
docker run --name etc-sql \
  --env ACCEPT_EULA=Y \
  --env MSSQL_PID=Developer \
  --env MSSQL_SA_PASSWORD='Etc-Sql!2026Aa' \
  --publish 127.0.0.1:1433:1433 \
  --volume etc-sql-data:/var/opt/mssql \
  --detach mcr.microsoft.com/mssql/server:2022-latest
```

Buat database `ETC_Demo`:

```bash
docker exec -it etc-sql /opt/mssql-tools18/bin/sqlcmd \
  -S localhost -U sa -C -b \
  -Q "IF DB_ID(N'ETC_Demo') IS NULL CREATE DATABASE ETC_Demo;"
```

### 5.2 Atur Koneksi dan Siapkan Aplikasi
Di Ubuntu atau Bash:

```bash
export ETC_CONNECTION_STRING='Server=127.0.0.1,1433;Database=ETC_Demo;User ID=sa;Password=Etc-Sql!2026Aa;Encrypt=true;TrustServerCertificate=true'

dotnet restore
dotnet build
dotnet run --project src/Etc.Desktop -- --init-db
dotnet run --project src/Etc.Desktop -- --seed-demo
```

Di PowerShell, atur connection string dulu sebelum menjalankan perintah `dotnet` yang sama:

```powershell
$env:ETC_CONNECTION_STRING = 'Server=127.0.0.1,1433;Database=ETC_Demo;User ID=sa;Password=Etc-Sql!2026Aa;Encrypt=true;TrustServerCertificate=true'
```

### 5.3 Buka Aplikasi

```bash
dotnet run --project src/Etc.Desktop
```

Seeder membuat akun demo dengan password `Etc-Demo!2026`. Password ini bisa diganti lewat environment variable `ETC_DEMO_PASSWORD` saat menjalankan seed.

Untuk menjalankan lagi setelah setup selesai:

```bash
docker start etc-sql
dotnet run --project src/Etc.Desktop
```

Environment variable hanya berlaku di terminal yang sedang dipakai, jadi atur ulang `ETC_CONNECTION_STRING` setiap membuka terminal baru.

## 6. Pengujian
Unit test:

```bash
dotnet test tests/Etc.UnitTests
```

Integration test butuh `ETC_TEST_CONNECTION_STRING` yang mengarah ke database pengujian terpisah, bukan `ETC_Demo`:

```bash
dotnet test Etc.slnx
```

## 7. Batasan
ETC adalah aplikasi demonstrasi desktop. Aplikasi ini tidak punya chat, notifikasi real-time, pembayaran, panel admin, deployment publik, atau sinkronisasi offline. Data disimpan di SQL Server lokal, dan sesi login berakhir saat aplikasi ditutup.
