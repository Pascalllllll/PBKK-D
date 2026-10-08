using System;
using System.Globalization;

namespace StudentRegistrationMVVM.Models;

public class Mahasiswa
{
    private static readonly CultureInfo Indonesia = new("id-ID");

    public int Id { get; set; }
    public string Nrp { get; set; } = "";
    public string Nama { get; set; } = "";
    public string Prodi { get; set; } = "";
    public string JenisKelamin { get; set; } = "";
    public DateTime TanggalLahir { get; set; }
    public string Alamat { get; set; } = "";
    public string NoTelepon { get; set; } = "";

    public string TanggalLahirText => TanggalLahir.ToString("dd MMM yyyy", Indonesia);
}
