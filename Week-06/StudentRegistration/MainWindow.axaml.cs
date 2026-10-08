using Avalonia.Controls;
using Avalonia.Interactivity;
using StudentRegistration.Repositories;
using System;
using System.Threading.Tasks;

namespace StudentRegistration;

public partial class MainWindow : Window
{
    private readonly StudentRepository studentRepository = new();

    public MainWindow()
    {
        InitializeComponent();
        Opened += async (_, _) => await LoadStudentsAsync();
    }

    private async void RefreshButton_Click(object? sender, RoutedEventArgs e) => await LoadStudentsAsync();

    private async Task LoadStudentsAsync()
    {
        RefreshButton.IsEnabled = false;
        TampilkanInfo(StudentDataGrid.ItemsSource == null ? "Memuat data dari SQL Server..." : null);
        StatusTextBlock.Classes.Set("error", false);
        StatusTextBlock.Text = "Memuat data...";

        try
        {
            var students = await studentRepository.GetAllAsync();
            int total = await studentRepository.GetTotalAsync();

            StudentDataGrid.ItemsSource = students;
            TotalTextBlock.Text = total.ToString();
            TampilkanInfo(students.Count == 0
                ? "Tabel Students masih kosong. Jalankan sql/03-students.sql lalu klik Refresh."
                : null);
            StatusTextBlock.Text = $"{students.Count} baris dibaca dari SQL Server pukul {DateTime.Now:HH:mm:ss}.";
        }
        catch (Exception ex)
        {
            StudentDataGrid.ItemsSource = null;
            TotalTextBlock.Text = "-";
            TampilkanInfo("Data tidak bisa dimuat. Pastikan container SQL Server berjalan dan semua file di folder sql sudah dijalankan, lalu klik Refresh.");
            StatusTextBlock.Classes.Set("error", true);
            StatusTextBlock.Text = $"Gagal mengakses SQL Server: {ex.Message}";
        }
        finally
        {
            RefreshButton.IsEnabled = true;
        }
    }

    // Teks di tengah tabel untuk kondisi memuat, kosong, atau gagal. null berarti disembunyikan.
    private void TampilkanInfo(string? pesan)
    {
        InfoTextBlock.Text = pesan;
        InfoTextBlock.IsVisible = pesan != null;
    }
}
