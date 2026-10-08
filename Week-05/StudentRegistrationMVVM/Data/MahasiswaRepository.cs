using System;
using System.Collections.Generic;
using System.Threading.Tasks;
using MySqlConnector;
using StudentRegistrationMVVM.Models;

namespace StudentRegistrationMVVM.Data;

public class MahasiswaRepository
{
    // Connection Timeout dibuat pendek supaya aplikasi tidak menunggu lama kalau MySQL mati.
    private const string DefaultConnectionString =
        "Server=localhost;Port=3306;Database=student_db;User ID=root;Password=;Connection Timeout=5;";

    private readonly string connectionString;

    public MahasiswaRepository()
    {
        connectionString = Environment.GetEnvironmentVariable("STUDENT_DB_CONNECTION_STRING")
            ?? DefaultConnectionString;
    }

    public async Task<List<Mahasiswa>> GetAllAsync(string kataKunci)
    {
        const string sql = """
            SELECT id, nrp, nama, prodi, jenis_kelamin, tanggal_lahir, alamat, no_telepon
            FROM mahasiswa
            WHERE @kunci = '' OR nrp LIKE @pola OR nama LIKE @pola OR prodi LIKE @pola
            ORDER BY nama
            """;

        await using var conn = await BukaKoneksiAsync();
        await using var cmd = new MySqlCommand(sql, conn);
        cmd.Parameters.AddWithValue("@kunci", kataKunci);
        cmd.Parameters.AddWithValue("@pola", $"%{EscapeLike(kataKunci)}%");

        var hasil = new List<Mahasiswa>();
        await using var reader = await cmd.ExecuteReaderAsync();
        while (await reader.ReadAsync())
        {
            hasil.Add(new Mahasiswa
            {
                Id = reader.GetInt32("id"),
                Nrp = reader.GetString("nrp"),
                Nama = reader.GetString("nama"),
                Prodi = reader.GetString("prodi"),
                JenisKelamin = reader.GetString("jenis_kelamin"),
                TanggalLahir = reader.GetDateTime("tanggal_lahir"),
                Alamat = reader.GetString("alamat"),
                NoTelepon = reader.GetString("no_telepon")
            });
        }

        return hasil;
    }

    public async Task InsertAsync(Mahasiswa mhs)
    {
        const string sql = """
            INSERT INTO mahasiswa (nrp, nama, prodi, jenis_kelamin, tanggal_lahir, alamat, no_telepon)
            VALUES (@nrp, @nama, @prodi, @jenisKelamin, @tanggalLahir, @alamat, @noTelepon)
            """;

        await using var conn = await BukaKoneksiAsync();
        await using var cmd = new MySqlCommand(sql, conn);
        TambahParameter(cmd, mhs);
        await cmd.ExecuteNonQueryAsync();
    }

    public async Task UpdateAsync(Mahasiswa mhs)
    {
        const string sql = """
            UPDATE mahasiswa
            SET nrp = @nrp, nama = @nama, prodi = @prodi, jenis_kelamin = @jenisKelamin,
                tanggal_lahir = @tanggalLahir, alamat = @alamat, no_telepon = @noTelepon
            WHERE id = @id
            """;

        await using var conn = await BukaKoneksiAsync();
        await using var cmd = new MySqlCommand(sql, conn);
        TambahParameter(cmd, mhs);
        cmd.Parameters.AddWithValue("@id", mhs.Id);
        await cmd.ExecuteNonQueryAsync();
    }

    public async Task DeleteAsync(int id)
    {
        await using var conn = await BukaKoneksiAsync();
        await using var cmd = new MySqlCommand("DELETE FROM mahasiswa WHERE id = @id", conn);
        cmd.Parameters.AddWithValue("@id", id);
        await cmd.ExecuteNonQueryAsync();
    }

    public async Task<bool> NrpExistsAsync(string nrp, int? kecualiId)
    {
        // Saat edit, baris milik mahasiswa itu sendiri tidak dihitung sebagai duplikat.
        const string sql = "SELECT COUNT(*) FROM mahasiswa WHERE nrp = @nrp AND (@id IS NULL OR id <> @id)";

        await using var conn = await BukaKoneksiAsync();
        await using var cmd = new MySqlCommand(sql, conn);
        cmd.Parameters.AddWithValue("@nrp", nrp);
        cmd.Parameters.AddWithValue("@id", kecualiId);
        return Convert.ToInt64(await cmd.ExecuteScalarAsync()) > 0;
    }

    private async Task<MySqlConnection> BukaKoneksiAsync()
    {
        var conn = new MySqlConnection(connectionString);
        await conn.OpenAsync();
        return conn;
    }

    private static void TambahParameter(MySqlCommand cmd, Mahasiswa mhs)
    {
        cmd.Parameters.AddWithValue("@nrp", mhs.Nrp);
        cmd.Parameters.AddWithValue("@nama", mhs.Nama);
        cmd.Parameters.AddWithValue("@prodi", mhs.Prodi);
        cmd.Parameters.AddWithValue("@jenisKelamin", mhs.JenisKelamin);
        cmd.Parameters.AddWithValue("@tanggalLahir", mhs.TanggalLahir.Date);
        cmd.Parameters.AddWithValue("@alamat", mhs.Alamat);
        cmd.Parameters.AddWithValue("@noTelepon", mhs.NoTelepon);
    }

    // Tanpa ini, mengetik "%" atau "_" di kolom cari akan dianggap wildcard oleh LIKE.
    private static string EscapeLike(string teks) =>
        teks.Replace(@"\", @"\\").Replace("%", @"\%").Replace("_", @"\_");
}
