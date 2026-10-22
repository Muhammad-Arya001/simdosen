<?php
session_start();
// Menggunakan @-operator agar halaman tetap bisa dirender walau koneksi.php belum dibuat
@include_once 'koneksi.php';

// Ambil data statistik ringkas jika database sudah terhubung
$total_dosen = 0;
$total_prodi = 0;
$total_fakultas = 0;

if (isset($conn) && !$conn->connect_error) {
    $res1 = $conn->query("SELECT COUNT(*) AS total FROM dosen");
    if ($res1) $total_dosen = $res1->fetch_assoc()['total'];

    $res2 = $conn->query("SELECT COUNT(*) AS total FROM prodi");
    if ($res2) $total_prodi = $res2->fetch_assoc()['total'];

    $res3 = $conn->query("SELECT COUNT(*) AS total FROM fakultas");
    if ($res3) $total_fakultas = $res3->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMDOSEN - Universitas Muhammadiyah Bengkulu</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS (Tema Maroon & Cream Sejajar) -->
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
            --text-muted: #6e5c59;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cream-bg);
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar-custom {
            background-color: var(--maroon-dark);
            border-bottom: 1px solid rgba(240, 230, 210, 0.15);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .navbar-custom .navbar-brand {
            font-weight: 800;
            color: var(--cream-text) !important;
            letter-spacing: 0.5px;
        }

        /* Style Logo UMB */
        .brand-logo {
            height: 42px;
            width: auto;
            object-fit: contain;
            margin-right: 12px;
        }

        .navbar-custom .nav-link {
            color: rgba(245, 238, 220, 0.8) !important;
            font-weight: 500;
            transition: color 0.2s;
        }

        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--cream-text) !important;
        }

        /* Hero Section dengan Gambar Latar Belakang & Pembatas Sejajar */
        .hero-section {
            background: linear-gradient(135deg, rgba(74, 0, 16, 0.92) 0%, rgba(107, 0, 24, 0.88) 100%), 
                        url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;
            color: var(--cream-text);
            padding: 90px 0 90px 0;
            border-bottom: 4px solid var(--maroon-light);
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--cream-accent);
            font-weight: 600;
        }

        .hero-desc {
            color: #e8dcd0;
            font-size: 1.05rem;
            line-height: 1.7;
        }

        /* Tombol Cream & Maroon */
        .btn-cream {
            background-color: var(--cream-accent);
            color: var(--maroon-dark);
            font-weight: 700;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn-cream:hover {
            background-color: #ffffff;
            color: var(--maroon-primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.25);
        }

        .btn-outline-cream {
            border: 2px solid var(--cream-accent);
            color: var(--cream-accent);
            font-weight: 600;
            padding: 10px 26px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-cream:hover {
            background-color: var(--cream-accent);
            color: var(--maroon-dark);
            transform: translateY(-2px);
        }

        /* Judul Seksi */
        .section-title {
            font-weight: 800;
            color: var(--maroon-dark);
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 70px;
            height: 4px;
            background-color: var(--maroon-primary);
            margin: 10px auto 0 auto;
            border-radius: 2px;
        }

        /* Kartu Statistik */
        .stat-card {
            background-color: var(--cream-card);
            border: 1px solid #e2d5bf;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(74, 0, 16, 0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 22px rgba(74, 0, 16, 0.12);
            border-color: var(--maroon-primary);
        }

        .stat-number {
            font-size: 3.2rem;
            font-weight: 800;
            color: var(--maroon-primary);
        }

        .stat-label {
            font-weight: 700;
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* Footer */
        footer {
            background-color: var(--maroon-dark);
            color: var(--cream-accent);
            border-top: 3px solid var(--maroon-light);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <!-- Gambar Logo UMB Lokal -->
                <img src="images/logo.png" alt="Logo UMB" class="brand-logo">
                <span>SIMDOSEN UM Bengkulu</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active me-3" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-3" href="api/dosen.php" target="_blank">API Dosen</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="btn btn-cream ms-2" href="dashboard.php">Dashboard</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-cream ms-2" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Garis Lurus & Sejajar) -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="hero-title mb-2">Sistem Informasi Dosen</h1>
            <p class="hero-subtitle mb-4">Universitas Muhammadiyah Bengkulu</p>
            <p class="hero-desc mx-auto mb-5" style="max-width: 680px;">
                Selamat datang di Layanan Informasi Akademik Dosen Terpadu. Platform terintegrasi untuk pengelolaan data tenaga pengajar serta penyedia Layanan Web Service (API) resmi.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#statistik" class="btn btn-cream">Lihat Ringkasan</a>
                <a href="login.php" class="btn btn-outline-cream">Masuk Portal</a>
            </div>
        </div>
    </section>

    <!-- Ringkasan Data / Statistik -->
    <section id="statistik" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title">Ringkasan Data Akademik</h2>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="card stat-card p-4">
                        <div class="card-body">
                            <div class="stat-number mb-1"><?= $total_dosen ?></div>
                            <p class="stat-label mb-0">Total Dosen Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card p-4">
                        <div class="card-body">
                            <div class="stat-number mb-1"><?= $total_prodi ?></div>
                            <p class="stat-label mb-0">Program Studi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card p-4">
                        <div class="card-body">
                            <div class="stat-number mb-1"><?= $total_fakultas ?></div>
                            <p class="stat-label mb-0">Fakultas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center py-4">
        <div class="container">
            <p class="mb-0 small">&copy; <?= date('Y') ?> Universitas Muhammadiyah Bengkulu. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>