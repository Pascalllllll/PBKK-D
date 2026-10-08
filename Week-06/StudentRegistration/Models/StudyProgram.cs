namespace StudentRegistration.Models;

// Bukan "Program" supaya tidak tertukar dengan class entry point di Program.cs.
public class StudyProgram
{
    public int ProgramId { get; set; }
    public string ProgramCode { get; set; } = string.Empty;
    public string ProgramName { get; set; } = string.Empty;
    public bool IsActive { get; set; }
}
