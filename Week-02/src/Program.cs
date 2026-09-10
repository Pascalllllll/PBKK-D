using System;
using System.Collections.Generic;

namespace SistemDataMahasiswa
{
    class Program
    {
        static List<Mahasiswa> daftarMahasiswa = new List<Mahasiswa>();

        static void Main(string[] args)
        {
            int opsi;
            do
            {
                TampilkanMenuUtama();
                Console.Write("Masukkan nomor opsi: ");
                string input = Console.ReadLine();

                if (!int.TryParse(input, out opsi))
                {
                    opsi = 0;
                }

                Console.WriteLine();

                switch (opsi)
                {
                    case 1:
                        TambahData();
                        break;
                    case 2:
                        TampilkanData();
                        break;
                    case 3:
                        CariData();
                        break;
                    case 4:
                        HapusData();
                        break;
                    case 5:
                        Console.WriteLine("Program dihentikan. Terima kasih.");
                        break;
                    default:
                        Console.WriteLine("Pilihan tidak valid. Silakan coba lagi.");
                        break;
                }

                if (opsi != 5)
                {
                    Console.WriteLine("\nTekan ENTER untuk kembali ke menu...");
                    Console.ReadLine();
                }

            } while (opsi != 5);
        }

        static void TampilkanMenuUtama()
        {
            Console.Clear();
            Console.WriteLine("=================================================");
            Console.WriteLine("    SISTEM MANAJEMEN DATA MAHASISWA (.NET 8)     ");
            Console.WriteLine("    Oleh: Hosea Felix Sanjaya (5025241177)       ");
            Console.WriteLine("=================================================");
            Console.WriteLine("1. Registrasi Mahasiswa Baru");
            Console.WriteLine("2. Lihat Daftar Mahasiswa");
            Console.WriteLine("3. Cari Mahasiswa (Berdasarkan NRP)");
            Console.WriteLine("4. Hapus Data Mahasiswa");
            Console.WriteLine("5. Keluar Aplikasi");
            Console.WriteLine("=================================================");
        }

        static void TambahData()
        {
            Console.Clear();
            Console.WriteLine("--- REGISTRASI MAHASISWA ---");

            Console.Write("NRP           : ");
            string nrp = Console.ReadLine();

            Console.Write("Nama Lengkap  : ");
            string nama = Console.ReadLine();

            Console.Write("Program Studi : ");
            string prodi = Console.ReadLine();

            double ipk;
            while (true)
            {
                Console.Write("IPK (0.00 - 4.00) : ");
                if (double.TryParse(Console.ReadLine(), out ipk) && ipk >= 0 && ipk <= 4)
                {
                    break;
                }
                Console.WriteLine("Input tidak valid. Masukkan angka desimal antara 0 hingga 4.");
            }

            daftarMahasiswa.Add(new Mahasiswa(nrp, nama, prodi, ipk));
            Console.WriteLine("\nData berhasil disimpan ke dalam memori.");
        }

        static void TampilkanData()
        {
            Console.Clear();
            Console.WriteLine("=================================================================");
            Console.WriteLine("                     DAFTAR DATA MAHASISWA                       ");
            Console.WriteLine("=================================================================");

            if (daftarMahasiswa.Count == 0)
            {
                Console.WriteLine("Database kosong. Belum ada data yang diregistrasikan.");
                return;
            }

            Console.WriteLine(string.Format("{0,-15} | {1,-20} | {2,-15} | {3,-5}", "NRP", "NAMA LENGKAP", "PROGRAM STUDI", "IPK"));
            Console.WriteLine("-----------------------------------------------------------------");

            foreach (var mhs in daftarMahasiswa)
            {
                Console.WriteLine(string.Format("{0,-15} | {1,-20} | {2,-15} | {3,-5:F2}", mhs.NRP, mhs.Nama, mhs.Prodi, mhs.IPK));
            }
            Console.WriteLine("=================================================================");
        }

        static void CariData()
        {
            Console.Clear();
            Console.WriteLine("--- PENCARIAN DATA ---");
            Console.Write("Masukkan NRP yang dicari: ");
            string nrpCari = Console.ReadLine();

            Mahasiswa hasilCari = null;
            foreach (var mhs in daftarMahasiswa)
            {
                if (mhs.NRP.Equals(nrpCari, StringComparison.OrdinalIgnoreCase))
                {
                    hasilCari = mhs;
                    break;
                }
            }

            if (hasilCari != null)
            {
                Console.WriteLine("\n[Data Ditemukan]");
                Console.WriteLine($"NRP           : {hasilCari.NRP}");
                Console.WriteLine($"Nama Lengkap  : {hasilCari.Nama}");
                Console.WriteLine($"Program Studi : {hasilCari.Prodi}");
                Console.WriteLine($"IPK           : {hasilCari.IPK:F2}");
            }
            else
            {
                Console.WriteLine("\nData tidak ditemukan.");
            }
        }

        static void HapusData()
        {
            Console.Clear();
            Console.WriteLine("--- HAPUS DATA ---");
            Console.Write("Masukkan NRP yang akan dihapus: ");
            string nrpHapus = Console.ReadLine();

            int index = daftarMahasiswa.FindIndex(m => m.NRP.Equals(nrpHapus, StringComparison.OrdinalIgnoreCase));

            if (index != -1)
            {
                daftarMahasiswa.RemoveAt(index);
                Console.WriteLine("\nData berhasil dihapus dari sistem.");
            }
            else
            {
                Console.WriteLine("\nPenghapusan gagal. Data dengan NRP tersebut tidak ditemukan.");
            }
        }
    }
}