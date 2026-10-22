<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username  = $_SESSION['username'] ?? 'User';
$role_id   = $_SESSION['role_id'] ?? 2;
$nama_role = $_SESSION['nama_role'] ?? 'Dosen';

$total_dosen = 0;
$total_prodi = 0;
$total_fakultas = 0;

$res1 = $conn->query("SELECT COUNT(*) AS total FROM dosen");
if ($res1) $total_dosen = $res1->fetch_assoc()['total'];

$res2 = $conn->query("SELECT COUNT(*) AS total FROM prodi");
if ($res2) $total_prodi = $res2->fetch_assoc()['total'];

$res3 = $conn->query("SELECT COUNT(*) AS total FROM fakultas");
if ($res3) $total_fakultas = $res3->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIMDOSEN UM Bengkulu</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --maroon-primary: #6b0018;
            --maroon-dark: #4a0010;
            --maroon-light: #8a0d28;
            --cream-bg: #f7f3e9;
            --cream-card: #eee5d3;
            --cream-accent: #f0e6d2;
            --cream-text: #f5eedc;
            --text-dark: #382c2a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cream-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background-color: var(--maroon-dark);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .brand-logo {
            height: 40px;
            margin-right: 12px;
        }

        .navbar-custom .nav-link {
            color: rgba(245, 238, 220, 0.8) !important;
            font-weight: 500;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--cream-text) !important;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--maroon-dark) 0%, var(--maroon-primary) 100%);
            color: var(--cream-text);
            padding: 35px 0;
            border-bottom: 4px solid var(--maroon-light);
        }

        .role-badge {
            background-color: var(--cream-accent);
            color: var(--maroon-dark);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .stat-card {
            background-color: var(--cream-card);
            border: 1px solid #e2d5bf;
            border-radius: 14px;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--maroon-primary);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            background-color: var(--maroon-primary);
            color: var(--cream-accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .menu-card {
            background-color: #ffffff;
            border: 1px solid #e2d5bf;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text-dark);
            display: block;
            height: 100%;
            transition: all 0.3s;
        }

        .menu-card:hover {
            border-color: var(--maroon-primary);
            transform: translateY(-4px);
            color: var(--maroon-primary);
        }

        footer {
            margin-top: auto;
            background-color: var(--maroon-dark);
            color: var(--cream-accent);
            border-top: 3px solid var(--maroon-light);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="images/logo.png" alt="Logo UMB" class="brand-logo" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/e/e0/Logo_Universitas_Muhammadiyah_Bengkulu.png';">
                <span>SIMDOSEN UM Bengkulu</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-gauge me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="dosen_crud.php"><i class="fa-solid fa-users me-1"></i> Kelola Dosen</a>
                    </li>

                    <?php if ($role_id == 1): ?>
                    <li class="nav-item me-3">
                        <a class="nav-link text-warning fw-bold" href="prodi_crud.php"><i class="fa-solid fa-book-bookmark me-1"></i> Kelola Prodi</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-warning fw-bold" href="fakultas_crud.php"><i class="fa-solid fa-building-columns me-1"></i> Kelola Fakultas</a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item me-3">
                        <a class="nav-link" href="laporan_dosen.php"><i class="fa-solid fa-file-invoice me-1"></i> Laporan</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="api/dosen.php" target="_blank"><i class="fa-solid fa-code me-1"></i> API</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3" href="logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="dashboard-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1">Selamat Datang, <?= htmlspecialchars($username) ?>! 👋</h2>
                    <p class="mb-0 text-light opacity-75">Sistem Informasi Pengelolaan Data Dosen Universitas Muhammadiyah Bengkulu</p>
                </div>
                <div>
                    <span class="role-badge"><i class="fa-solid fa-user-shield me-1"></i> Hak Akses: <?= htmlspecialchars($nama_role) ?></span>
                </div>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <a href="dosen_crud.php" class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark"><?= $total_dosen ?></h3>
                            <p class="text-muted small mb-0 fw-semibold">Total Dosen Terdaftar</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="<?= $role_id == 1 ? 'prodi_crud.php' : '#' ?>" class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon"><i class="fa-solid fa-book-bookmark"></i></div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark"><?= $total_prodi ?></h3>
                            <p class="text-muted small mb-0 fw-semibold">Program Studi</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?= $role_id == 1 ? 'fakultas_crud.php' : '#' ?>" class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon"><i class="fa-solid fa-building-columns"></i></div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark"><?= $total_fakultas ?></h3>
                            <p class="text-muted small mb-0 fw-semibold">Fakultas</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <h4 class="fw-bold mb-4 text-dark" style="border-left: 4px solid var(--maroon-primary); padding-left: 12px;">Navigasi Modul Sistem</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="dosen_crud.php" class="menu-card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-list-check fs-2 me-3" style="color: var(--maroon-primary);"></i>
                        <h5 class="fw-bold mb-0"><?= $role_id == 1 ? 'Kelola Data Dosen' : 'Profil Saya' ?></h5>
                    </div>
                    <p class="text-muted small mb-0"><?= $role_id == 1 ? 'Tambah, ubah, hapus, dan tinjau seluruh data profil dosen.' : 'Lihat dan sesuaikan data profil pengajar Anda.' ?></p>
                </a>
            </div>

            <?php if ($role_id == 1): ?>
            <div class="col-md-4">
                <a href="prodi_crud.php" class="menu-card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-book-bookmark fs-2 me-3" style="color: var(--maroon-primary);"></i>
                        <h5 class="fw-bold mb-0">Kelola Program Studi</h5>
                    </div>
                    <p class="text-muted small mb-0">Tambah, ubah, dan hapus daftar Program Studi (Prodi) universitas.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="fakultas_crud.php" class="menu-card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-building-columns fs-2 me-3" style="color: var(--maroon-primary);"></i>
                        <h5 class="fw-bold mb-0">Kelola Fakultas</h5>
                    </div>
                    <p class="text-muted small mb-0">Tambah, ubah, dan hapus daftar Fakultas di lingkungan universitas.</p>
                </a>
            </div>
            <?php endif; ?>

            <div class="col-md-4">
                <a href="laporan_dosen.php" class="menu-card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-print fs-2 me-3" style="color: var(--maroon-primary);"></i>
                        <h5 class="fw-bold mb-0">Laporan Dosen</h5>
                    </div>
                    <p class="text-muted small mb-0">Cetak dan rekapitulasi daftar dosen aktif sesuai format standar pelaporan universitas.</p>
                </a>
            </div>

            <div class="col-md-4">
                <a href="api/dosen.php" target="_blank" class="menu-card p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fa-solid fa-network-wired fs-2 me-3" style="color: var(--maroon-primary);"></i>
                        <h5 class="fw-bold mb-0">Web Service (API)</h5>
                    </div>
                    <p class="text-muted small mb-0">Akses endpoint REST API JSON data dosen untuk integrasi dengan sistem pihak ketiga.</p>
                </a>
            </div>
        </div>
    </main>

    <footer class="text-center py-3">
        <div class="container">
            <p class="mb-0 small">&copy; <?= date('Y') ?> Universitas Muhammadiyah Bengkulu. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>