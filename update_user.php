<?php
$mysqli = new mysqli("localhost", "root", "", "mydatabase");
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$id = $_POST['id'] ?? null;
if (!$id) die("ID tidak valid");

$stmt = $mysqli->prepare("UPDATE pendaftaran SET nama_lengkap=?, email=?, no_hp=?, jenis_kelamin=?, tempat_lahir=?, alamat_lengkap=?, kabupaten_kota=?, provinsi=?, jenis_sekolah=?, jurusan_sekolah=?, jalur_pendaftaran=? WHERE id=?");
$stmt->bind_param(
    "sssssssssssi",
    $_POST['nama_lengkap'],
    $_POST['email'],
    $_POST['no_hp'],
    $_POST['jenis_kelamin'],
    $_POST['tempat_lahir'],
    $_POST['alamat_lengkap'],
    $_POST['kabupaten_kota'],
    $_POST['provinsi'],
    $_POST['jenis_sekolah'],
    $_POST['jurusan_sekolah'],
    $_POST['jalur_pendaftaran'],
    $id
);
$stmt->execute();

header("Location: register-users.php?updated=1");
exit;
