USE StudentRegistrationDB;
GO

-- Tiga mahasiswa contoh: TI (1), SI (2), dan Teknik Elektro (4).
INSERT INTO Students (NIM, Name, ProgramId, BirthDate, Address, PhoneNumber)
VALUES
('20231006', 'Gilang Ramadhan', 1, '2004-04-09', 'Jl. Kertajaya No. 31, Surabaya', '081377001122'),
('20231007', 'Intan Permata',   2, '2003-10-23', 'Jl. Mulyosari No. 7, Surabaya',  '085733224455'),
('20231008', 'Yoga Pratama',    4, '2004-08-16', 'Jl. Klampis No. 12, Surabaya',   '087855667788');
GO

SELECT s.NIM, s.Name, p.ProgramName, s.BirthDate, s.PhoneNumber
FROM Students s
INNER JOIN Programs p ON s.ProgramId = p.ProgramId
ORDER BY s.Name;
GO
