USE StudentRegistrationDB;
GO

-- Harus ditolak: ProgramId 99 tidak ada di tabel Programs (FOREIGN KEY).
INSERT INTO Students (NIM, Name, ProgramId)
VALUES ('20231099', 'Test Student', 99);
GO

-- Harus ditolak: NIM 20231001 sudah dipakai (UNIQUE).
INSERT INTO Students (NIM, Name, ProgramId)
VALUES ('20231001', 'Mahasiswa Duplikat', 1);
GO
