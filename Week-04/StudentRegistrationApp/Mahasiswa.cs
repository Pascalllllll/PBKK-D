namespace StudentRegistrationApp;

public class Mahasiswa
{
    public string NRP { get; set; }
    public string Nama { get; set; }
    public string Prodi { get; set; }
    public double IPK { get; set; }

    public string IPKText => IPK.ToString("0.00");

    public Mahasiswa(string nrp, string nama, string prodi, double ipk)
    {
        NRP = nrp;
        Nama = nama;
        Prodi = prodi;
        IPK = ipk;
    }
}
