<?php
require 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID pengguna diperlukan.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Pengguna tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $nidn = $_POST['nidn'] ?? '';
    $role = $_POST['role'] ?? 'mahasiswa';
    $accessibility = $_POST['accessibility'] ?? 'full';
    $approved = isset($_POST['approved']) ? 1 : 0;
    $registration_date = $_POST['registration_date'] ?? null;

    $update = $conn->prepare("UPDATE users SET name=?, email=?, nidn=?, role=?, accessibility=?, approved=?, registration_date=? WHERE id=?");
    $update->bind_param("ssssissi", $name, $email, $nidn, $role, $accessibility, $approved, $registration_date, $id);
    $update->execute();

    header("Location: users.php?message=Pengguna+berhasil+diupdate");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pengguna</title>
</head>
<body>
    <h2>Edit Data Pengguna</h2>
    <form method="post">
        <label>Nama Lengkap:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br><br>

        <label>NIDN:</label><br>
        <input type="text" name="nidn" value="<?= htmlspecialchars($user['nidn']) ?>"><br><br>

        <label>Peran:</label><br>
        <select name="role">
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
            <option value="mahasiswa" <?= $user['role'] === 'dosen' ? 'selected' : '' ?>>Dosen</option>
        </select><br><br>

        <label>Aksesibilitas:</label><br>
        <select name="accessibility">
            <option value="full" <?= $user['accessibility'] === 'full' ? 'selected' : '' ?>>Penuh</option>
            <option value="limited" <?= $user['accessibility'] === 'limited' ? 'selected' : '' ?>>Terbatas</option>
            <option value="restricted" <?= $user['accessibility'] === 'restricted' ? 'selected' : '' ?>>Dibatasi</option>
        </select><br><br>

        <label>Disetujui:</label>
        <input type="checkbox" name="approved" <?= $user['approved'] ? 'checked' : '' ?>><br><br>

        <label>Tanggal Registrasi:</label><br>
        <input type="datetime-local" name="registration_date" value="<?= str_replace(' ', 'T', $user['registration_date']) ?>"><br><br>

        <button type="submit">Perbarui</button>
    </form>
    <a href="users.php">Kembali ke Daftar Pengguna</a>
</body>
</html>
