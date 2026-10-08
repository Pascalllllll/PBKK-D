namespace StudentRegistration.Models;

// Satu baris tabel Programs. Tidak diberi nama "Program" supaya tidak tertukar
// dengan class Program di Program.cs yang menjadi entry point aplikasi.
public class StudyProgram
{
    public int ProgramId { get; set; }
    public string ProgramCode { get; set; } = string.Empty;
    public string ProgramName { get; set; } = string.Empty;
    public bool IsActive { get; set; }
}
