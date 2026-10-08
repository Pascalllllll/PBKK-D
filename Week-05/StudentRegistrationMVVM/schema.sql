CREATE DATABASE IF NOT EXISTS student_db;
USE student_db;

CREATE TABLE IF NOT EXISTS mahasiswa (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nrp           CHAR(10)     NOT NULL,
    nama          VARCHAR(100) NOT NULL,
    prodi         VARCHAR(50)  NOT NULL,
    jenis_kelamin VARCHAR(20)  NOT NULL,
    tanggal_lahir DATE         NOT NULL,
    alamat        VARCHAR(255) NOT NULL,
    no_telepon    VARCHAR(20)  NOT NULL,
    CONSTRAINT uq_mahasiswa_nrp UNIQUE (nrp)
);
