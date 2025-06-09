<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 only for debugging

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Connect to DB
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mydatabase;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal koneksi ke database']);
    exit;
}

// Validate required fields
$required = [
    'nama_lengkap', 'email', 'no_hp', 'jenis_kelamin', 'tempat_lahir',
    'tanggal_lahir', 'alamat_lengkap', 'provinsi', 'kabupaten_kota',
    'jenis_sekolah', 'nama_sekolah', 'jurusan_sekolah', 'tahun_lulus',
    'pilihan_1', 'jalur_pendaftaran'
];

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => "Field '$field' wajib diisi"]);
        exit;
    }
}

// Create per-student upload folder
function sanitize($str) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($str));
}

$upload_root = __DIR__ . '/uploads/';
if (!is_dir($upload_root)) mkdir($upload_root, 0755, true);

$folder_name = date('YmdHis') . '_' . sanitize($_POST['nama_lengkap']);
$student_folder = $upload_root . $folder_name;
if (!is_dir($student_folder)) mkdir($student_folder, 0755, true);

// Save file into student folder
function saveFile($field, $dir, $required = true) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        if ($required) throw new Exception("Upload gagal untuk $field");
        return null;
    }

    $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
    $filename = $field . '.' . $ext;
    $dest = $dir . '/' . $filename;

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        throw new Exception("Tidak dapat memindahkan $field");
    }

    return "uploads/$folder_name/$filename";
}

try {
    $foto = saveFile('foto', $student_folder);
    $ijazah = saveFile('ijazah', $student_folder);
    $raport_10 = saveFile('raport_10', $student_folder);
    $raport_11 = saveFile('raport_11', $student_folder);
    $raport_12 = saveFile('raport_12', $student_folder);

    $kip_card = null;
    $sertifikat_prestasi = null;
    $jalur = $_POST['jalur_pendaftaran'];

    if ($jalur === 'KIP') {
        $kip_card = saveFile('kip_card', $student_folder, true);
    }

    if ($jalur === 'Prestasi') {
        $sertifikat_prestasi = saveFile('sertifikat_prestasi', $student_folder, true);
    }

    $stmt = $pdo->prepare("
        INSERT INTO pendaftaran (
            nama_lengkap, email, no_hp, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat_lengkap,
            provinsi, kabupaten_kota, jenis_sekolah, nama_sekolah, jurusan_sekolah, tahun_lulus,
            pilihan_1, pilihan_2, jalur_pendaftaran,
            foto, ijazah, raport_10, raport_11, raport_12, kip_card, sertifikat_prestasi
        ) VALUES (
            :nama_lengkap, :email, :no_hp, :jenis_kelamin, :tempat_lahir, :tanggal_lahir, :alamat_lengkap,
            :provinsi, :kabupaten_kota, :jenis_sekolah, :nama_sekolah, :jurusan_sekolah, :tahun_lulus,
            :pilihan_1, :pilihan_2, :jalur_pendaftaran,
            :foto, :ijazah, :raport_10, :raport_11, :raport_12, :kip_card, :sertifikat_prestasi
        )
    ");

    $stmt->execute([
        ':nama_lengkap' => $_POST['nama_lengkap'],
        ':email' => $_POST['email'],
        ':no_hp' => $_POST['no_hp'],
        ':jenis_kelamin' => $_POST['jenis_kelamin'],
        ':tempat_lahir' => $_POST['tempat_lahir'],
        ':tanggal_lahir' => $_POST['tanggal_lahir'],
        ':alamat_lengkap' => $_POST['alamat_lengkap'],
        ':provinsi' => $_POST['provinsi'],
        ':kabupaten_kota' => $_POST['kabupaten_kota'],
        ':jenis_sekolah' => $_POST['jenis_sekolah'],
        ':nama_sekolah' => $_POST['nama_sekolah'],
        ':jurusan_sekolah' => $_POST['jurusan_sekolah'],
        ':tahun_lulus' => $_POST['tahun_lulus'],
        ':pilihan_1' => $_POST['pilihan_1'],
        ':pilihan_2' => $_POST['pilihan_2'] ?? '',
        ':jalur_pendaftaran' => $_POST['jalur_pendaftaran'],
        ':foto' => $foto,
        ':ijazah' => $ijazah,
        ':raport_10' => $raport_10,
        ':raport_11' => $raport_11,
        ':raport_12' => $raport_12,
        ':kip_card' => $kip_card,
        ':sertifikat_prestasi' => $sertifikat_prestasi
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Pendaftaran berhasil disimpan']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Kesalahan: ' . $e->getMessage()]);
}
exit;
