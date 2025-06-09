@echo off
:: Database Creation Script
:: This script creates a MySQL database with tables based on columns.txt

:: Set MySQL connection parameters
set DB_HOST=localhost
set DB_USER=root
set DB_PASS=
set DB_NAME=mydatabase

:: Prompt for MySQL password if not set
if "%DB_PASS%"=="yourpassword" (
    set /p DB_PASS=Enter MySQL root password: 
)

:: MySQL commands to create database and tables
mysql -h %DB_HOST% -u %DB_USER% -p%DB_PASS% --execute="
CREATE DATABASE IF NOT EXISTS %DB_NAME%;
USE %DB_NAME%;

-- Create approved_users table
CREATE TABLE IF NOT EXISTS approved_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap VARCHAR(255),
    email VARCHAR(255),
    no_hp VARCHAR(20),
    jenis_kelamin VARCHAR(10),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    alamat_lengkap TEXT,
    provinsi VARCHAR(100),
    kabupaten_kota VARCHAR(100),
    jenis_sekolah VARCHAR(50),
    nama_sekolah VARCHAR(100),
    jurusan_sekolah VARCHAR(100),
    tahun_lulus INT,
    pilihan_1 VARCHAR(100),
    pilihan_2 VARCHAR(100),
    jalur_pendaftaran VARCHAR(50),
    foto VARCHAR(255),
    ijazah VARCHAR(255),
    raport_10 VARCHAR(255),
    raport_11 VARCHAR(255),
    raport_12 VARCHAR(255),
    kip_card VARCHAR(255),
    sertifikat_prestasi VARCHAR(255),
    folder_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create pendaftaran table
CREATE TABLE IF NOT EXISTS pendaftaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap VARCHAR(255),
    email VARCHAR(255),
    no_hp VARCHAR(20),
    jenis_kelamin VARCHAR(10),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    alamat_lengkap TEXT,
    provinsi VARCHAR(100),
    kabupaten_kota VARCHAR(100),
    jenis_sekolah VARCHAR(50),
    nama_sekolah VARCHAR(100),
    jurusan_sekolah VARCHAR(100),
    tahun_lulus INT,
    pilihan_1 VARCHAR(100),
    pilihan_2 VARCHAR(100),
    jalur_pendaftaran VARCHAR(50),
    foto VARCHAR(255),
    ijazah VARCHAR(255),
    raport_10 VARCHAR(255),
    raport_11 VARCHAR(255),
    raport_12 VARCHAR(255),
    kip_card VARCHAR(255),
    sertifikat_prestasi VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create student_profiles table
CREATE TABLE IF NOT EXISTS student_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    no_hp VARCHAR(20),
    jenis_kelamin ENUM('Laki-laki', 'Perempuan'),
    tempat_lahir VARCHAR(100),
    tanggal_lahir DATE,
    alamat_lengkap TEXT,
    provinsi VARCHAR(100),
    kabupaten_kota VARCHAR(100),
    jenis_sekolah ENUM('SMA', 'SMK', 'MA'),
    nama_sekolah VARCHAR(100),
    jurusan_sekolah VARCHAR(100),
    tahun_lulus YEAR,
    pilihan_prodi_1 ENUM('Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Mesin', 'Akuntansi', 'Manajemen'),
    pilihan_prodi_2 ENUM('Teknik Informatika', 'Sistem Informasi', 'Teknik Elektro', 'Teknik Mesin', 'Akuntansi', 'Manajemen'),
    jalur_pendaftaran ENUM('SNBP', 'SNBT', 'Mandiri'),
    foto_path VARCHAR(255),
    ijazah_path VARCHAR(255),
    raport_10_path VARCHAR(255),
    raport_11_path VARCHAR(255),
    raport_12_path VARCHAR(255),
    kip_card_path VARCHAR(255),
    sertifikat_prestasi_path VARCHAR(255)
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    email VARCHAR(255),
    nidn VARCHAR(20),
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role ENUM('admin', 'student', 'reviewer'),
    accessibility ENUM('full', 'limited'),
    approved TINYINT(1) DEFAULT 0,
    registration_date DATETIME
);

SHOW TABLES;
"

:: Check if the command was successful
if %errorlevel% equ 0 (
    echo Database %DB_NAME% and tables created successfully!
) else (
    echo Error creating database or tables.
)

pause