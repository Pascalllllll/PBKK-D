using System;

namespace SistemDataMahasiswa
{
    class Mahasiswa
    {
        public string NRP { get; set; }
        public string Nama { get; set; }
        public string Prodi { get; set; }
        public double IPK { get; set; }

        public Mahasiswa(string nim, string nama, string prodi, double ipk)
        {
            NRP = nim;
            Nama = nama;
            Prodi = prodi;
            IPK = ipk;
        }
    }
}