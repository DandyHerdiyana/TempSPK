<?php
// daftar-pendaftar.php dengan fitur lengkap
$mysqli = new mysqli("localhost", "root", "", "mydatabase");
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

$search = $_GET['search'] ?? '';
$filter_jalur = $_GET['jalur'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';

$query = "SELECT * FROM pendaftaran WHERE 1";
$params = [];

if (!empty($search)) {
    $query .= " AND (nama_lengkap LIKE ? OR email LIKE ? OR no_hp LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($filter_jalur)) {
    $query .= " AND jalur_pendaftaran = ?";
    $params[] = $filter_jalur;
}

if (!empty($filter_tahun)) {
    $query .= " AND tahun_lulus = ?";
    $params[] = $filter_tahun;
}

$query .= " ORDER BY created_at DESC";
$stmt = $mysqli->prepare($query);
if ($params) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pendaftar</title>
    <link rel="stylesheet" href="register-users.css">
</head>
<body>
    <h1>Daftar Pendaftar</h1>

    <div class="filters">
        <form method="get">
            <input type="text" name="search" placeholder="Cari nama/email/no hp" value="<?= htmlspecialchars($search) ?>">
            <select name="jalur">
                <option value="">-- Semua Jalur --</option>
                <option value="KIP" <?= $filter_jalur == 'KIP' ? 'selected' : '' ?>>KIP</option>
                <option value="Prestasi" <?= $filter_jalur == 'Prestasi' ? 'selected' : '' ?>>Prestasi</option>
                <option value="Gelombang 1" <?= $filter_jalur == 'Gelombang 1' ? 'selected' : '' ?>>Gelombang 1</option>
                <option value="Gelombang 2" <?= $filter_jalur == 'Gelombang 2' ? 'selected' : '' ?>>Gelombang 2</option>
                <option value="Gelombang 3" <?= $filter_jalur == 'Gelombang 3' ? 'selected' : '' ?>>Gelombang 3</option>
            </select>
            <select name="tahun">
                <option value="">-- Semua Tahun --</option>
                <?php for ($y = date('Y'); $y >= date('Y') - 10; $y--): ?>
                    <option value="<?= $y ?>" <?= $filter_tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <a class="export-btn" href="export-csv.php">Export ke CSV</a>
    <a class="back-btn" href="dashboard.php">Kembali ke Dashboard</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>No HP</th>
                <th>Jenis Kelamin</th>
                <th>Asal</th>
                <th>Pilihan 1</th>
                <th>Pilihan 2</th>
                <th>Jalur</th>
                <th>Tahun Lulus</th>
                <th>Daftar Pada</th>
                <th>Profil</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                    <td><?= htmlspecialchars($row['kabupaten_kota']) . ', ' . htmlspecialchars($row['provinsi']) ?></td>
                    <td><?= htmlspecialchars($row['pilihan_1']) ?></td>
                    <td><?= htmlspecialchars($row['pilihan_2']) ?></td>
                    <td><?= htmlspecialchars($row['jalur_pendaftaran']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_lulus']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td><a class="profile-link" href="profile.php?id=<?= $row['id'] ?>">Lihat Profil</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>