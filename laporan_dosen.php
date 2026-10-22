<?php
session_start();
require_once 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil Query Data Dosen beserta Nama Prodi & Fakultas
$query = "SELECT d.*, p.nama_prodi, f.nama_fakultas 
          FROM dosen d 
          LEFT JOIN prodi p ON d.id_prodi = p.id 
          LEFT JOIN fakultas f ON p.id_fakultas = f.id 
          ORDER BY d.id ASC";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Daftar Dosen Aktif - UM Bengkulu</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --maroon-primary: #6b0018;
            --maroon-dark: #4a0010;
            --cream-bg: #f7f3e9;
            --cream-card: #eee5d3;
            --text-dark: #382c2a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cream-bg);
            color: var(--text-dark);
            font-size: 0.85rem;
        }

        .navbar-custom {
            background-color: var(--maroon-dark);
        }

        .brand-logo {
            height: 38px;
            margin-right: 10px;
        }

        /* Header Laporan Resmi */
        .report-header {
            background-color: #ffffff;
            border-bottom: 2px solid var(--maroon-primary);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .report-title {
            font-weight: 800;
            color: var(--maroon-dark);
            font-size: 1.25rem;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .meta-table td {
            padding: 2px 10px 2px 0;
            font-weight: 600;
        }

        /* Styling Tabel Laporan Grid */
        .table-report {
            background-color: #ffffff;
            border-collapse: collapse;
            width: 100%;
        }

        .table-report th {
            background-color: #f0e8d8 !important;
            color: var(--maroon-dark);
            border: 1px solid #b3a594 !important;
            text-align: center;
            vertical-align: middle;
            font-weight: 700;
            padding: 8px 4px;
            font-size: 0.8rem;
        }

        .table-report td {
            border: 1px solid #c9beae !important;
            padding: 6px 5px;
            vertical-align: middle;
            font-size: 0.8rem;
        }

        .btn-maroon {
            background-color: var(--maroon-primary);
            color: #ffffff;
            font-weight: 600;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-dark);
            color: #ffffff;
        }

        /* CSS KHUSUS CETAK / PRINT */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                padding: 0;
            }
            .container-fluid {
                padding: 0 !important;
            }
            .report-header {
                border: none;
                padding: 0 0 10px 0;
            }
            .table-report th {
                background-color: #e0e0e0 !important;
                color: #000000 !important;
            }
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar Navigasi (Sembunyi saat dicetak) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2 no-print">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="images/logo.png" alt="Logo UMB" class="brand-logo" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/e/e0/Logo_Universitas_Muhammadiyah_Bengkulu.png';">
                <span>SIMDOSEN UM Bengkulu</span>
            </a>
            <div class="ms-auto d-flex gap-2">
                <a href="dosen_crud.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Kelola Dosen</a>
                <button onclick="window.print()" class="btn btn-warning btn-sm fw-bold"><i class="fa-solid fa-print me-1"></i> Cetak Laporan (PDF)</button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">

        <!-- Banner Kop Header Laporan Resmi -->
        <div class="report-header shadow-sm">
            <h5 class="report-title">Daftar Dosen Aktif Universitas</h5>
            <table class="meta-table">
                <tr>
                    <td>Universitas</td>
                    <td>: Universitas Muhammadiyah Bengkulu</td>
                </tr>
                <tr>
                    <td>Fakultas</td>
                    <td>: Fakultas Teknik</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>: Teknik Informatika</td>
                </tr>
            </table>
        </div>

        <!-- Tabel Laporan Format Kotak Grid -->
        <div class="card border-0 shadow-sm p-2 bg-white">
            <div class="table-responsive">
                <table class="table table-report table-sm mb-0">
                    <thead>
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th>Nomor Registrasi</th>
                            <th>NUPTK</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan Fungsional</th>
                            <th>Kepangkatan</th>
                            <th>Pendidikan Terakhir</th>
                            <th>Rumpun Ilmu</th>
                            <th>Status Serdos</th>
                            <th>Status Pegawai</th>
                            <th>Ikatan Kerja</th>
                            <th>Status Aktivitas</th>
                            <th>Penempatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nomor_registrasi'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($row['nuptk'] ?: '-') ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['nip'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($row['jabatan_fungsional'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($row['kepangkatan'] ?: '-') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['pendidikan_terakhir'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($row['rumpun_ilmu'] ?: '-') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['status_serdos'] ?: '-') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['status_pegawai'] ?: '-') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['ikatan_kerja'] ?: '-') ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['status_aktivitas'] ?: 'Aktif') ?></td>
                                    <td><?= htmlspecialchars($row['penempatan'] ?: 'UM Bengkulu') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="14" class="text-center py-4 text-muted">Data dosen belum tersedia dalam database.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>