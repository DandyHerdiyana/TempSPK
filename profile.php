<?php
// profile.php with Approve, Edit, Delete functionality
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = (int) $_GET['id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=mydatabase;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle actions (approve/delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['approve'])) {
            $pdo->prepare("UPDATE pendaftaran SET status = 'approved' WHERE id = ?")->execute([$id]);
            header("Location: profile.php?id=$id");
            exit;
        } elseif (isset($_POST['delete'])) {
            $pdo->prepare("DELETE FROM pendaftaran WHERE id = ?")->execute([$id]);
            header("Location: users.php?deleted=1");
            exit;
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM pendaftaran WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Data tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Gagal koneksi database.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pendaftar</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <h1>Profil Pendaftar</h1>

    <?php if (!empty($data['status']) && $data['status'] === 'approved') : ?>
        <p class="approved-status">Status: Sudah Disetujui</p>
    <?php endif; ?>

    <div class="section">
        <h2>Informasi Pribadi</h2>
        <p><label>Nama Lengkap:</label> <span class="value"><?= htmlspecialchars($data['nama_lengkap']) ?></span></p>
        <p><label>Email:</label> <span class="value"><?= htmlspecialchars($data['email']) ?></span></p>
        <p><label>No HP:</label> <span class="value"><?= htmlspecialchars($data['no_hp']) ?></span></p>
        <p><label>Jenis Kelamin:</label> <span class="value"><?= htmlspecialchars($data['jenis_kelamin']) ?></span></p>
        <p><label>Tempat, Tanggal Lahir:</label> <span class="value"><?= htmlspecialchars($data['tempat_lahir']) ?>, <?= htmlspecialchars($data['tanggal_lahir']) ?></span></p>
        <p><label>Alamat:</label> <span class="value"><?= nl2br(htmlspecialchars($data['alamat_lengkap'])) ?></span></p>
    </div>

    <div class="section">
        <h2>Asal Sekolah</h2>
        <p><label>Provinsi:</label> <span class="value"><?= htmlspecialchars($data['provinsi']) ?></span></p>
        <p><label>Kabupaten/Kota:</label> <span class="value"><?= htmlspecialchars($data['kabupaten_kota']) ?></span></p>
        <p><label>Jenis Sekolah:</label> <span class="value"><?= htmlspecialchars($data['jenis_sekolah']) ?></span></p>
        <p><label>Nama Sekolah:</label> <span class="value"><?= htmlspecialchars($data['nama_sekolah']) ?></span></p>
        <p><label>Jurusan:</label> <span class="value"><?= htmlspecialchars($data['jurusan_sekolah']) ?></span></p>
        <p><label>Tahun Lulus:</label> <span class="value"><?= htmlspecialchars($data['tahun_lulus']) ?></span></p>
    </div>

    <div class="section">
        <h2>Pilihan Program Studi</h2>
        <p><label>Pilihan 1:</label> <span class="value"><?= htmlspecialchars($data['pilihan_1']) ?></span></p>
        <p><label>Pilihan 2:</label> <span class="value"><?= htmlspecialchars($data['pilihan_2']) ?></span></p>
        <p><label>Jalur Pendaftaran:</label> <span class="value"><?= htmlspecialchars($data['jalur_pendaftaran']) ?></span></p>
    </div>

    <div class="section">
        <h2>Berkas Unggahan</h2>
        <p><label>Foto:</label><br>
            <?php if ($data['foto']) : ?>
                <img src="<?= htmlspecialchars($data['foto']) ?>" alt="Foto">
            <?php endif; ?>
        </p>
        <?php
        $docs = ['ijazah', 'raport_10', 'raport_11', 'raport_12', 'kip_card', 'sertifikat_prestasi'];
        foreach ($docs as $doc) {
            if (!empty($data[$doc])) {
                echo '<p><label>' . ucfirst(str_replace('_', ' ', $doc)) . ':</label> <a class="file-link" href="' . htmlspecialchars($data[$doc]) . '" target="_blank">Lihat Berkas</a></p>';
            }
        }
        ?>
    </div>

    <div class="buttons">
        <?php if (empty($data['status'])): ?>
            <form method="post">
                <button type="submit" name="approve">Setujui</button>
            </form>
        <?php endif; ?>

        <form method="post" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
            <button type="submit" name="delete" style="background-color: red; color: white;">Hapus</button>
        </form>

        <a href="register-users.php">&larr; Kembali ke daftar pendaftar</a>
    </div>
</body>
</html>
