<?php
session_start();
require_once 'koneksi.php';

// Proteksi Halaman: Khusus Admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 2) != 1) {
    die("Akses ditolak! Halaman ini khusus Admin.");
}

$message = '';
$message_type = '';

// TAMBAH PRODI
if (isset($_POST['tambah_prodi'])) {
    $nama_prodi  = trim($_POST['nama_prodi']);
    $id_fakultas = intval($_POST['id_fakultas']);

    if (!empty($nama_prodi)) {
        $stmt = $conn->prepare("INSERT INTO prodi (id_fakultas, nama_prodi) VALUES (?, ?)");
        $stmt->bind_param("is", $id_fakultas, $nama_prodi);
        if ($stmt->execute()) {
            $message = "Program Studi berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "Gagal menambah prodi: " . $conn->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// HAPUS PRODI
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM prodi WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Program Studi berhasil dihapus!";
        $message_type = "success";
    }
    $stmt->close();
}

$fakultas_list = $conn->query("SELECT * FROM fakultas");
$prodi_list = $conn->query("SELECT p.*, f.nama_fakultas FROM prodi p LEFT JOIN fakultas f ON p.id_fakultas = f.id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Program Studi - SIMDOSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f7f3e9; }
        .btn-maroon { background-color: #6b0018; color: #fff; }
        .btn-maroon:hover { background-color: #4a0010; color: #fff; }
    </style>
</head>
<body class="p-4">

    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold m-0 text-dark">Kelola Program Studi</h4>
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Form Tambah Prodi -->
        <form method="POST" class="row g-3 mb-4 p-3 rounded border" style="background-color: #eee5d3;">
            <div class="col-md-6">
                <label class="form-label fw-bold">Nama Program Studi</label>
                <input type="text" name="nama_prodi" class="form-control" placeholder="Contoh: Sistem Informasi" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Fakultas</label>
                <select name="id_fakultas" class="form-select" required>
                    <?php while ($f = $fakultas_list->fetch_assoc()): ?>
                        <option value="<?= $f['id'] ?>"><?= $f['nama_fakultas'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="tambah_prodi" class="btn btn-maroon w-100 fw-bold"><i class="fa-solid fa-plus me-1"></i> Tambah</button>
            </div>
        </form>

        <!-- Tabel Daftar Prodi -->
        <table class="table table-bordered align-middle">
            <thead class="table-dark" style="background-color: #4a0010;">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Program Studi</th>
                    <th>Fakultas</th>
                    <th style="width: 80px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; while ($p = $prodi_list->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($p['nama_prodi']) ?></td>
                        <td><?= htmlspecialchars($p['nama_fakultas']) ?></td>
                        <td class="text-center">
                            <a href="prodi_crud.php?hapus=<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus prodi ini?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>