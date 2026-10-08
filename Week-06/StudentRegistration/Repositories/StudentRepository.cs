using System;
using System.Collections.Generic;
using System.Threading.Tasks;
using Microsoft.Data.SqlClient;
using StudentRegistration.Models;

namespace StudentRegistration.Repositories;

public class StudentRepository
{
    // Hanya untuk container lokal. Server lain: isi environment variable SQLSERVER_CONNECTION_STRING.
    private const string DefaultConnectionString =
        "Server=localhost,1433;Database=StudentRegistrationDB;User ID=sa;Password=Pbkk_Week06!;" +
        "TrustServerCertificate=True;Connect Timeout=5;";

    private readonly string connectionString =
        Environment.GetEnvironmentVariable("SQLSERVER_CONNECTION_STRING") ?? DefaultConnectionString;

    public async Task<List<Student>> GetAllAsync()
    {
        const string sql = """
            SELECT
                s.StudentId,
                s.NIM,
                s.Name,
                s.ProgramId,
                p.ProgramName,
                s.BirthDate,
                s.Address,
                s.PhoneNumber,
                s.CreatedAt,
                s.UpdatedAt
            FROM Students s
            INNER JOIN Programs p
                ON s.ProgramId = p.ProgramId
            ORDER BY s.StudentId;
            """;

        var students = new List<Student>();

        await using var connection = new SqlConnection(connectionString);
        await using var command = new SqlCommand(sql, connection);

        await connection.OpenAsync();
        await using var reader = await command.ExecuteReaderAsync();

        // Posisi kolom dicari sekali saja, bukan di setiap baris.
        int colStudentId = reader.GetOrdinal("StudentId");
        int colNim = reader.GetOrdinal("NIM");
        int colName = reader.GetOrdinal("Name");
        int colProgramId = reader.GetOrdinal("ProgramId");
        int colProgramName = reader.GetOrdinal("ProgramName");
        int colBirthDate = reader.GetOrdinal("BirthDate");
        int colAddress = reader.GetOrdinal("Address");
        int colPhone = reader.GetOrdinal("PhoneNumber");
        int colCreatedAt = reader.GetOrdinal("CreatedAt");
        int colUpdatedAt = reader.GetOrdinal("UpdatedAt");

        while (await reader.ReadAsync())
        {
            students.Add(new Student
            {
                StudentId = reader.GetInt32(colStudentId),
                NIM = reader.GetString(colNim),
                Name = reader.GetString(colName),
                ProgramId = reader.GetInt32(colProgramId),
                ProgramName = reader.GetString(colProgramName),
                BirthDate = reader.IsDBNull(colBirthDate) ? null : reader.GetDateTime(colBirthDate),
                Address = reader.IsDBNull(colAddress) ? string.Empty : reader.GetString(colAddress),
                PhoneNumber = reader.IsDBNull(colPhone) ? string.Empty : reader.GetString(colPhone),
                CreatedAt = reader.GetDateTime(colCreatedAt),
                UpdatedAt = reader.IsDBNull(colUpdatedAt) ? null : reader.GetDateTime(colUpdatedAt)
            });
        }

        return students;
    }

    public async Task<int> GetTotalAsync()
    {
        const string sql = "SELECT COUNT(*) FROM Students;";

        await using var connection = new SqlConnection(connectionString);
        await using var command = new SqlCommand(sql, connection);

        await connection.OpenAsync();
        return Convert.ToInt32(await command.ExecuteScalarAsync());
    }
}
