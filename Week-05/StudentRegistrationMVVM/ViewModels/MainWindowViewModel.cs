using System;
using System.Collections.ObjectModel;
using System.Linq;
using System.Text.RegularExpressions;
using System.Threading.Tasks;
using MySqlConnector;
using StudentRegistrationMVVM.Data;
using StudentRegistrationMVVM.Models;

namespace StudentRegistrationMVVM.ViewModels;

public class MainWindowViewModel : ViewModelBase
{
    private readonly MahasiswaRepository repository;

    private string nrp = "";
    private string nama = "";
    private string? prodi;
    private string? jenisKelamin;
    private DateTime? tanggalLahir;
    private string alamat = "";
    private string noTelepon = "";
    private string? pesanForm;

    private Mahasiswa? diedit;
    private Mahasiswa? selectedMahasiswa;
    private bool menungguKonfirmasiHapus;

    private string kataKunci = "";
    private string kataKunciTerakhir = "";
    private bool isLoading;
    private bool isError;
    private string pesanStatus = "";

    public MainWindowViewModel(MahasiswaRepository repository)
    {
        this.repository = repository;

        MuatCommand = new AsyncRelayCommand(() => MuatDataAsync());
        SimpanCommand = new AsyncRelayCommand(SimpanAsync);
        KosongkanCommand = new RelayCommand(KosongkanForm);
        HapusCommand = new RelayCommand(() => MenungguKonfirmasiHapus = true, () => SelectedMahasiswa != null);
        KonfirmasiHapusCommand = new AsyncRelayCommand(HapusAsync);
        BatalHapusCommand = new RelayCommand(() => MenungguKonfirmasiHapus = false);
    }

    public ObservableCollection<Mahasiswa> DaftarMahasiswa { get; } = new();

    public string[] DaftarProdi { get; } =
    {
        "Teknik Informatika",
        "Sistem Informasi",
        "Teknik Komputer",
        "Teknologi Informasi",
        "Sains Data"
    };

    public string[] DaftarJenisKelamin { get; } = { "Laki-laki", "Perempuan" };

    public AsyncRelayCommand MuatCommand { get; }
    public AsyncRelayCommand SimpanCommand { get; }
    public RelayCommand KosongkanCommand { get; }
    public RelayCommand HapusCommand { get; }
    public AsyncRelayCommand KonfirmasiHapusCommand { get; }
    public RelayCommand BatalHapusCommand { get; }

    public string Nrp { get => nrp; set => SetProperty(ref nrp, value); }
    public string Nama { get => nama; set => SetProperty(ref nama, value); }
    public string? Prodi { get => prodi; set => SetProperty(ref prodi, value); }
    public string? JenisKelamin { get => jenisKelamin; set => SetProperty(ref jenisKelamin, value); }
    public DateTime? TanggalLahir { get => tanggalLahir; set => SetProperty(ref tanggalLahir, value); }
    public string Alamat { get => alamat; set => SetProperty(ref alamat, value); }
    public string NoTelepon { get => noTelepon; set => SetProperty(ref noTelepon, value); }

    public string? PesanForm
    {
        get => pesanForm;
        private set
        {
            if (SetProperty(ref pesanForm, value))
                OnPropertyChanged(nameof(AdaPesanForm));
        }
    }

    public bool AdaPesanForm => !string.IsNullOrEmpty(PesanForm);

    public string JudulForm => diedit == null ? "Tambah mahasiswa" : $"Edit {diedit.Nrp}";
    public string LabelSimpan => diedit == null ? "Simpan" : "Simpan perubahan";
    public string LabelKosongkan => diedit == null ? "Kosongkan" : "Batal edit";

    public Mahasiswa? SelectedMahasiswa
    {
        get => selectedMahasiswa;
        set
        {
            if (!SetProperty(ref selectedMahasiswa, value))
                return;

            MenungguKonfirmasiHapus = false;
            HapusCommand.RaiseCanExecuteChanged();
            OnPropertyChanged(nameof(PertanyaanHapus));

            if (value != null)
                IsiForm(value);
        }
    }

    public bool MenungguKonfirmasiHapus
    {
        get => menungguKonfirmasiHapus;
        set => SetProperty(ref menungguKonfirmasiHapus, value);
    }

    public string PertanyaanHapus => SelectedMahasiswa == null
        ? ""
        : $"Hapus {SelectedMahasiswa.Nama} ({SelectedMahasiswa.Nrp})? Data yang dihapus tidak bisa dikembalikan.";

    public string KataKunci { get => kataKunci; set => SetProperty(ref kataKunci, value); }

    public string InfoJumlah => kataKunciTerakhir.Length == 0
        ? $"{DaftarMahasiswa.Count} data"
        : $"{DaftarMahasiswa.Count} hasil untuk \"{kataKunciTerakhir}\"";

    // Satu teks di tengah tabel kosong untuk tiga kondisi: memuat, gagal, dan memang belum ada data.
    public bool TampilkanInfoTabel => DaftarMahasiswa.Count == 0;
    public bool TampilkanCobaLagi => IsError && !IsLoading && DaftarMahasiswa.Count == 0;

    public string InfoTabel
    {
        get
        {
            if (IsLoading)
                return "Memuat data dari MySQL...";
            if (IsError)
                return "Data tidak bisa dimuat. Pastikan MySQL berjalan dan schema.sql sudah dijalankan, lalu coba lagi.";
            if (kataKunciTerakhir.Length > 0)
                return $"Tidak ada mahasiswa yang cocok dengan \"{kataKunciTerakhir}\".";
            return "Belum ada data. Isi form di kiri untuk menambah mahasiswa.";
        }
    }

    public bool IsLoading
    {
        get => isLoading;
        private set
        {
            if (SetProperty(ref isLoading, value))
                PerbaruiInfoTabel();
        }
    }

    public bool IsError
    {
        get => isError;
        private set
        {
            if (SetProperty(ref isError, value))
                PerbaruiInfoTabel();
        }
    }

    public string PesanStatus { get => pesanStatus; private set => SetProperty(ref pesanStatus, value); }

    // Pesan sukses simpan/hapus baru ditampilkan setelah reload berhasil, supaya tidak tertimpa.
    private async Task MuatDataAsync(string? pesanSukses = null)
    {
        string kunci = KataKunci.Trim();
        IsLoading = true;

        try
        {
            var hasil = await repository.GetAllAsync(kunci);

            DaftarMahasiswa.Clear();
            foreach (var mhs in hasil)
                DaftarMahasiswa.Add(mhs);

            kataKunciTerakhir = kunci;
            IsError = false;
            PesanStatus = pesanSukses ?? "Terhubung ke MySQL.";
        }
        catch (Exception ex)
        {
            DaftarMahasiswa.Clear();
            TampilkanErrorDatabase(ex);
        }
        finally
        {
            IsLoading = false;
            OnPropertyChanged(nameof(InfoJumlah));
            PerbaruiInfoTabel();
        }
    }

    private async Task SimpanAsync()
    {
        Mahasiswa? data = Validasi();
        if (data == null)
            return;

        string pesan;
        try
        {
            if (await repository.NrpExistsAsync(data.Nrp, diedit?.Id))
            {
                PesanForm = $"NRP {data.Nrp} sudah dipakai mahasiswa lain.";
                return;
            }

            if (diedit == null)
            {
                await repository.InsertAsync(data);
                pesan = $"{data.Nama} ({data.Nrp}) ditambahkan.";
            }
            else
            {
                data.Id = diedit.Id;
                await repository.UpdateAsync(data);
                pesan = $"Data {data.Nrp} diperbarui.";
            }
        }
        catch (MySqlException ex) when (ex.ErrorCode == MySqlErrorCode.DuplicateKeyEntry)
        {
            // Jaga-jaga kalau NRP yang sama disimpan dari tempat lain di antara cek dan insert.
            PesanForm = $"NRP {data.Nrp} sudah dipakai mahasiswa lain.";
            return;
        }
        catch (Exception ex)
        {
            TampilkanErrorDatabase(ex);
            return;
        }

        KosongkanForm();
        await MuatDataAsync(pesan);
    }

    private async Task HapusAsync()
    {
        if (SelectedMahasiswa is not Mahasiswa mhs)
            return;

        try
        {
            await repository.DeleteAsync(mhs.Id);
        }
        catch (Exception ex)
        {
            MenungguKonfirmasiHapus = false;
            TampilkanErrorDatabase(ex);
            return;
        }

        KosongkanForm();
        await MuatDataAsync($"{mhs.Nama} ({mhs.Nrp}) dihapus.");
    }

    private Mahasiswa? Validasi()
    {
        PesanForm = null;

        string nrpBersih = Nrp.Trim();
        string namaBersih = Regex.Replace(Nama.Trim(), @"\s+", " ");
        string alamatBersih = Alamat.Trim();
        // Spasi, strip, dan tanda + dibuang dulu, jadi "+62 812-3456-7890" tetap diterima.
        string teleponBersih = Regex.Replace(NoTelepon, @"[\s\-+]", "");

        if (nrpBersih.Length != 10 || !nrpBersih.All(char.IsDigit))
            return Gagal("NRP harus 10 digit angka.");

        if (namaBersih.Length < 3)
            return Gagal("Nama minimal 3 karakter.");

        if (string.IsNullOrEmpty(Prodi))
            return Gagal("Program studi belum dipilih.");

        if (string.IsNullOrEmpty(JenisKelamin))
            return Gagal("Jenis kelamin belum dipilih.");

        if (TanggalLahir is not DateTime lahir)
            return Gagal("Tanggal lahir belum diisi.");

        if (lahir.Date > DateTime.Today.AddYears(-15))
            return Gagal("Umur mahasiswa minimal 15 tahun.");

        if (alamatBersih.Length < 5)
            return Gagal("Alamat minimal 5 karakter.");

        if (!Regex.IsMatch(teleponBersih, @"^(08|628)\d{8,11}$"))
            return Gagal("Nomor telepon harus diawali 08 atau 62, 10 sampai 14 digit.");

        return new Mahasiswa
        {
            Nrp = nrpBersih,
            Nama = namaBersih,
            Prodi = Prodi,
            JenisKelamin = JenisKelamin,
            TanggalLahir = lahir.Date,
            Alamat = alamatBersih,
            NoTelepon = teleponBersih
        };
    }

    private Mahasiswa? Gagal(string pesan)
    {
        PesanForm = pesan;
        return null;
    }

    // Data disalin ke form, jadi mengetik di form tidak langsung mengubah isi tabel.
    private void IsiForm(Mahasiswa mhs)
    {
        diedit = mhs;
        Nrp = mhs.Nrp;
        Nama = mhs.Nama;
        Prodi = mhs.Prodi;
        JenisKelamin = mhs.JenisKelamin;
        TanggalLahir = mhs.TanggalLahir;
        Alamat = mhs.Alamat;
        NoTelepon = mhs.NoTelepon;
        PesanForm = null;
        PerbaruiLabelForm();
    }

    private void KosongkanForm()
    {
        diedit = null;
        SelectedMahasiswa = null;
        Nrp = "";
        Nama = "";
        Prodi = null;
        JenisKelamin = null;
        TanggalLahir = null;
        Alamat = "";
        NoTelepon = "";
        PesanForm = null;
        MenungguKonfirmasiHapus = false;
        PerbaruiLabelForm();
    }

    private void TampilkanErrorDatabase(Exception ex)
    {
        IsError = true;
        PesanStatus = $"Gagal mengakses MySQL: {ex.Message}";
    }

    private void PerbaruiLabelForm()
    {
        OnPropertyChanged(nameof(JudulForm));
        OnPropertyChanged(nameof(LabelSimpan));
        OnPropertyChanged(nameof(LabelKosongkan));
    }

    private void PerbaruiInfoTabel()
    {
        OnPropertyChanged(nameof(TampilkanInfoTabel));
        OnPropertyChanged(nameof(TampilkanCobaLagi));
        OnPropertyChanged(nameof(InfoTabel));
    }
}
