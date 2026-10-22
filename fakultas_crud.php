<?php
session_start();
require_once 'koneksi.php';

// Proteksi Halaman: Khusus Admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? 2) != 1) {
    die("Akses ditolak! Halaman ini khusus Admin.");
}

$message = '';
$message_type = '';

// TAMBAH FAKULTAS
if (isset($_POST['tambah_fakultas'])) {
    $nama_fakultas = trim($_POST['nama_fakultas']);

    if (!empty($nama_fakultas)) {
        $stmt = $conn->prepare("INSERT INTO fakultas (nama_fakultas) VALUES (?)");
        $stmt->bind_param("s", $nama_fakultas);
        if ($stmt->execute()) {
            $message = "Fakultas berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "Gagal menambah fakultas: " . $conn->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// HAPUS FAKULTAS
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM fakultas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Fakultas berhasil dihapus!";
        $message_type = "success";
    } else {
        $message = "Gagal menghapus fakultas: " . $conn->error;
        $message_type = "danger";
    }
    $stmt->close();
}

$fakultas_list = $conn->query("SELECT * FROM fakultas ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Fakultas - SIMDOSEN</title>
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
            <h4 class="fw-bold m-0 text-dark"><i class="fa-solid fa-building-columns me-2"></i>Kelola Fakultas</h4>
            <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Form Tambah Fakultas -->
        <form method="POST" class="row g-3 mb-4 p-3 rounded border" style="background-color: #eee5d3;">
            <div class="col-md-9">
                <label class="form-label fw-bold">Nama Fakultas</label>
                <input type="text" name="nama_fakultas" class="form-control" placeholder="Contoh: Fakultas Keguruan dan Ilmu Pendidikan" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" name="tambah_fakultas" class="btn btn-maroon w-100 fw-bold"><i class="fa-solid fa-plus me-1"></i> Tambah</button>
            </div>
        </form>

        <!-- Tabel Daftar Fakultas -->
        <table class="table table-bordered align-middle">
            <thead class="table-dark" style="background-color: #4a0010;">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Fakultas</th>
                    <th style="width: 100px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($fakultas_list && $fakultas_list->num_rows > 0): ?>
                    <?php $no=1; while ($f = $fakultas_list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($f['nama_fakultas']) ?></td>
                            <td class="text-center">
                                <a href="fakultas_crud.php?hapus=<?= $f['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Menghapus fakultas akan menghapus prodi terkait. Lanjutkan?')" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">Belum ada fakultas terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>