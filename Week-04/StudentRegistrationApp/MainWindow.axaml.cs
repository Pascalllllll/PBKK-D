using Avalonia.Controls;
using Avalonia.Input;
using Avalonia.Interactivity;
using Avalonia.Layout;
using Avalonia.Media;
using System;
using System.Collections.Generic;
using System.Globalization;
using System.Linq;
using System.Threading.Tasks;

namespace StudentRegistrationApp;

public partial class MainWindow : Window
{
    private readonly List<Mahasiswa> daftarMahasiswa = new();
    private Mahasiswa? sedangDiedit;

    public MainWindow()
    {
        InitializeComponent();
        TampilkanData();
    }

    private void BtnSimpan_Click(object? sender, RoutedEventArgs e)
    {
        string nrp = InputNrp.Text?.Trim() ?? "";
        string nama = InputNama.Text?.Trim() ?? "";
        string prodi = (InputProdi.SelectedItem as ComboBoxItem)?.Content?.ToString() ?? "";

        string? error = Validasi(nrp, nama, prodi, InputIpk.Text, out double ipk);
        if (error != null)
        {
            TampilkanError(error);
            return;
        }

        if (sedangDiedit == null)
        {
            daftarMahasiswa.Add(new Mahasiswa(nrp, nama, prodi, ipk));
            TxtStatus.Text = $"{nama} ({nrp}) ditambahkan.";
        }
        else
        {
            sedangDiedit.NRP = nrp;
            sedangDiedit.Nama = nama;
            sedangDiedit.Prodi = prodi;
            sedangDiedit.IPK = ipk;
            TxtStatus.Text = $"Data {nrp} diperbarui.";
        }

        KosongkanForm();
        TampilkanData();
    }

    private string? Validasi(string nrp, string nama, string prodi, string? ipkInput, out double ipk)
    {
        ipk = 0;

        if (nrp.Length != 10 || !nrp.All(char.IsDigit))
            return "NRP harus 10 digit angka.";

        // Saat edit, NRP milik mahasiswa itu sendiri tidak dihitung sebagai duplikat.
        if (daftarMahasiswa.Any(m => m != sedangDiedit && m.NRP == nrp))
            return $"NRP {nrp} sudah terdaftar.";

        if (nama.Length == 0)
            return "Nama belum diisi.";

        if (prodi.Length == 0)
            return "Program studi belum dipilih.";

        // Terima "3.5" maupun "3,5" apa pun locale sistemnya.
        string ipkNormal = (ipkInput ?? "").Trim().Replace(',', '.');
        if (!double.TryParse(ipkNormal, NumberStyles.Float, CultureInfo.InvariantCulture, out ipk))
            return "IPK harus berupa angka, misalnya 3.50.";

        if (ipk < 0 || ipk > 4)
            return "IPK harus di antara 0 dan 4.";

        return null;
    }

    private void TampilkanError(string pesan)
    {
        TxtError.Text = pesan;
        TxtError.IsVisible = true;
    }

    private void BtnBatal_Click(object? sender, RoutedEventArgs e) => KosongkanForm();

    private void KosongkanForm()
    {
        InputNrp.Text = "";
        InputNama.Text = "";
        InputIpk.Text = "";
        InputProdi.SelectedIndex = -1;
        TxtError.IsVisible = false;

        sedangDiedit = null;
        TxtFormTitle.Text = "Tambah mahasiswa";
        BtnSimpan.Content = "Simpan";
        BtnBatal.Content = "Kosongkan";
        InputNrp.Focus();
    }

    private void BtnEdit_Click(object? sender, RoutedEventArgs e) => MulaiEdit();

    private void GridMahasiswa_DoubleTapped(object? sender, TappedEventArgs e) => MulaiEdit();

    private void MulaiEdit()
    {
        if (GridMahasiswa.SelectedItem is not Mahasiswa mhs)
            return;

        sedangDiedit = mhs;
        InputNrp.Text = mhs.NRP;
        InputNama.Text = mhs.Nama;
        InputIpk.Text = mhs.IPKText;
        InputProdi.SelectedItem = InputProdi.Items
            .OfType<ComboBoxItem>()
            .FirstOrDefault(item => item.Content?.ToString() == mhs.Prodi);

        TxtError.IsVisible = false;
        TxtFormTitle.Text = $"Edit {mhs.NRP}";
        BtnSimpan.Content = "Simpan perubahan";
        BtnBatal.Content = "Batal";
        InputNama.Focus();
    }

    private async void BtnHapus_Click(object? sender, RoutedEventArgs e)
    {
        if (GridMahasiswa.SelectedItem is not Mahasiswa mhs)
            return;

        bool yakin = await Konfirmasi($"Hapus {mhs.Nama} ({mhs.NRP})? Data yang dihapus tidak bisa dikembalikan.");
        if (!yakin)
            return;

        daftarMahasiswa.Remove(mhs);
        if (sedangDiedit == mhs)
            KosongkanForm();

        TxtStatus.Text = $"{mhs.Nama} ({mhs.NRP}) dihapus.";
        TampilkanData();
    }

    private void GridMahasiswa_SelectionChanged(object? sender, SelectionChangedEventArgs e)
    {
        bool adaPilihan = GridMahasiswa.SelectedItem != null;
        BtnEdit.IsEnabled = adaPilihan;
        BtnHapus.IsEnabled = adaPilihan;
    }

    private void InputCari_TextChanged(object? sender, TextChangedEventArgs e) => TampilkanData();

    private void TampilkanData()
    {
        string kataKunci = InputCari.Text?.Trim() ?? "";

        List<Mahasiswa> hasil = daftarMahasiswa
            .Where(m => kataKunci.Length == 0
                || m.NRP.Contains(kataKunci, StringComparison.OrdinalIgnoreCase)
                || m.Nama.Contains(kataKunci, StringComparison.OrdinalIgnoreCase)
                || m.Prodi.Contains(kataKunci, StringComparison.OrdinalIgnoreCase))
            .ToList();

        GridMahasiswa.ItemsSource = hasil;
        TxtJumlahTampil.Text = kataKunci.Length == 0
            ? $"{hasil.Count} data"
            : $"{hasil.Count} dari {daftarMahasiswa.Count} data";

        TxtTotal.Text = daftarMahasiswa.Count.ToString();
        if (daftarMahasiswa.Count == 0)
        {
            TxtRataRata.Text = "-";
            TxtTertinggi.Text = "-";
            return;
        }

        TxtRataRata.Text = daftarMahasiswa.Average(m => m.IPK).ToString("0.00");
        TxtTertinggi.Text = daftarMahasiswa.Max(m => m.IPK).ToString("0.00");
    }

    private async Task<bool> Konfirmasi(string pesan)
    {
        bool hasil = false;

        var dialog = new Window
        {
            Title = "Konfirmasi",
            Width = 380,
            SizeToContent = SizeToContent.Height,
            CanResize = false,
            WindowStartupLocation = WindowStartupLocation.CenterOwner
        };

        var btnBatal = new Button { Content = "Batal", IsCancel = true };
        var btnHapus = new Button
        {
            Content = "Hapus",
            Background = new SolidColorBrush(Color.Parse("#B42318")),
            Foreground = Brushes.White
        };

        btnBatal.Click += (_, _) => dialog.Close();
        btnHapus.Click += (_, _) =>
        {
            hasil = true;
            dialog.Close();
        };

        var tombol = new StackPanel
        {
            Orientation = Orientation.Horizontal,
            HorizontalAlignment = HorizontalAlignment.Right,
            Spacing = 10
        };
        tombol.Children.Add(btnBatal);
        tombol.Children.Add(btnHapus);

        var isi = new StackPanel { Margin = new Avalonia.Thickness(20), Spacing = 20 };
        isi.Children.Add(new TextBlock { Text = pesan, TextWrapping = TextWrapping.Wrap });
        isi.Children.Add(tombol);

        dialog.Content = isi;
        await dialog.ShowDialog(this);
        return hasil;
    }
}
