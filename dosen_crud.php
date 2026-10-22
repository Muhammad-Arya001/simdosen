<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$role_id   = $_SESSION['role_id'] ?? 2;
$message   = '';
$message_type = '';

// ----------------------------------------------------
// 1. TAMBAH DOSEN BARU (USERNAME = NAMA, PASSWORD = NIP)
// ----------------------------------------------------
if (isset($_POST['tambah'])) {
    if ($role_id != 1) {
        die("Akses ditolak! Hanya Admin yang boleh menambah dosen baru.");
    }
    
    $nama                = trim($_POST['nama']);
    $nip                 = trim($_POST['nip']);
    $nuptk               = trim($_POST['nuptk']);
    $nomor_registrasi    = trim($_POST['nomor_registrasi']);
    $id_prodi            = intval($_POST['id_prodi']);
    $jabatan_fungsional  = trim($_POST['jabatan_fungsional']);
    $kepangkatan         = trim($_POST['kepangkatan']);
    $pendidikan_terakhir = trim($_POST['pendidikan_terakhir']);
    $rumpun_ilmu         = trim($_POST['rumpun_ilmu']);
    $status_serdos       = trim($_POST['status_serdos']);
    $status_pegawai      = trim($_POST['status_pegawai']);
    $ikatan_kerja        = trim($_POST['ikatan_kerja']);
    $status_aktivitas    = trim($_POST['status_aktivitas']);
    $penempatan          = trim($_POST['penempatan']);

    // Username = Nama Dosen (dibuat huruf kecil tanpa spasi)
    $username_dosen = strtolower(str_replace(' ', '', $nama));
    
    // Password Default = NIP Dosen (jika NIP kosong, gunakan '123456')
    $pass_plain = !empty($nip) ? $nip : '123456';
    $password_default = password_hash($pass_plain, PASSWORD_BCRYPT);
    $role_dosen = 2; // Role Dosen

    // Buat User Baru
    $stmt_user = $conn->prepare("INSERT INTO users (username, password, id_role) VALUES (?, ?, ?)");
    $stmt_user->bind_param("ssi", $username_dosen, $password_default, $role_dosen);

    if ($stmt_user->execute()) {
        $new_user_id = $stmt_user->insert_id;
        $stmt_user->close();

        // Buat Data Dosen
        $stmt_dosen = $conn->prepare("INSERT INTO dosen (id_user, nama, nip, nuptk, nomor_registrasi, id_prodi, jabatan_fungsional, kepangkatan, pendidikan_terakhir, rumpun_ilmu, status_serdos, status_pegawai, ikatan_kerja, status_aktivitas, penempatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_dosen->bind_param("issssisssssssss", $new_user_id, $nama, $nip, $nuptk, $nomor_registrasi, $id_prodi, $jabatan_fungsional, $kepangkatan, $pendidikan_terakhir, $rumpun_ilmu, $status_serdos, $status_pegawai, $ikatan_kerja, $status_aktivitas, $penempatan);

        if ($stmt_dosen->execute()) {
            $message = "Dosen berhasil dibuat! <br>Username: <b>$username_dosen</b> | Password: <b>$pass_plain</b>";
            $message_type = "success";
        } else {
            $message = "Gagal menyimpan data dosen: " . $conn->error;
            $message_type = "danger";
        }
        $stmt_dosen->close();
    } else {
        $message = "Gagal membuat akun login dosen (Username '$username_dosen' mungkin sudah digunakan).";
        $message_type = "danger";
    }
}

// ----------------------------------------------------
// 2. FITUR UBAH PASSWORD SAYA (UNTUK DOSEN / ADMIN)
// ----------------------------------------------------
if (isset($_POST['ubah_password'])) {
    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi    = trim($_POST['konfirmasi_password']);

    if ($password_baru !== $konfirmasi) {
        $message = "Konfirmasi password baru tidak cocok!";
        $message_type = "danger";
    } else {
        // Ambil password saat ini dari DB
        $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $res_pass = $stmt_pass->get_result()->fetch_assoc();
        $stmt_pass->close();

        $db_pass = $res_pass['password'];

        // Cek apakah password lama sesuai
        if (($password_lama === $db_pass) || password_verify($password_lama, $db_pass)) {
            $hash_baru = password_hash($password_baru, PASSWORD_BCRYPT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->bind_param("si", $hash_baru, $user_id);
            if ($upd->execute()) {
                $message = "Password Anda berhasil diperbarui!";
                $message_type = "success";
            } else {
                $message = "Gagal memperbarui password!";
                $message_type = "danger";
            }
            $upd->close();
        } else {
            $message = "Password lama yang Anda masukkan salah!";
            $message_type = "danger";
        }
    }
}

// ----------------------------------------------------
// 3. EDIT DATA DOSEN
// ----------------------------------------------------
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);

    if ($role_id != 1) {
        $check = $conn->prepare("SELECT id FROM dosen WHERE id = ? AND id_user = ?");
        $check->bind_param("ii", $id, $user_id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            die("Akses ditolak! Anda tidak berhak mengubah data dosen lain.");
        }
    }

    $nama                = trim($_POST['nama']);
    $nip                 = trim($_POST['nip']);
    $nuptk               = trim($_POST['nuptk']);
    $nomor_registrasi    = trim($_POST['nomor_registrasi']);
    $id_prodi            = intval($_POST['id_prodi']);
    $jabatan_fungsional  = trim($_POST['jabatan_fungsional']);
    $kepangkatan         = trim($_POST['kepangkatan']);
    $pendidikan_terakhir = trim($_POST['pendidikan_terakhir']);
    $rumpun_ilmu         = trim($_POST['rumpun_ilmu']);
    $status_serdos       = trim($_POST['status_serdos']);
    $status_pegawai      = trim($_POST['status_pegawai']);
    $ikatan_kerja        = trim($_POST['ikatan_kerja']);
    $status_aktivitas    = trim($_POST['status_aktivitas']);
    $penempatan          = trim($_POST['penempatan']);

    $stmt = $conn->prepare("UPDATE dosen SET nama=?, nip=?, nuptk=?, nomor_registrasi=?, id_prodi=?, jabatan_fungsional=?, kepangkatan=?, pendidikan_terakhir=?, rumpun_ilmu=?, status_serdos=?, status_pegawai=?, ikatan_kerja=?, status_aktivitas=?, penempatan=? WHERE id=?");
    $stmt->bind_param("ssssisssssssssi", $nama, $nip, $nuptk, $nomor_registrasi, $id_prodi, $jabatan_fungsional, $kepangkatan, $pendidikan_terakhir, $rumpun_ilmu, $status_serdos, $status_pegawai, $ikatan_kerja, $status_aktivitas, $penempatan, $id);

    if ($stmt->execute()) {
        $message = "Data dosen berhasil diperbarui!";
        $message_type = "success";
    } else {
        $message = "Gagal memperbarui data: " . $conn->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// ----------------------------------------------------
// 4. HAPUS DOSEN (KHUSUS ADMIN)
// ----------------------------------------------------
if (isset($_GET['hapus'])) {
    if ($role_id != 1) {
        die("Akses ditolak! Dosen tidak diizinkan menghapus data.");
    }
    
    $id = intval($_GET['hapus']);
    $stmt = $conn->prepare("DELETE FROM dosen WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Data dosen berhasil dihapus!";
        $message_type = "success";
    } else {
        $message = "Gagal menghapus data: " . $conn->error;
        $message_type = "danger";
    }
    $stmt->close();
}

$prodi_list = $conn->query("SELECT * FROM prodi");

// QUERY DATA BERDASARKAN ROLE
if ($role_id == 1) {
    $stmt_dosen = $conn->prepare("SELECT d.*, p.nama_prodi FROM dosen d LEFT JOIN prodi p ON d.id_prodi = p.id ORDER BY d.id DESC");
} else {
    $stmt_dosen = $conn->prepare("SELECT d.*, p.nama_prodi FROM dosen d LEFT JOIN prodi p ON d.id_prodi = p.id WHERE d.id_user = ? ORDER BY d.id DESC");
    $stmt_dosen->bind_param("i", $user_id);
}
$stmt_dosen->execute();
$dosen_list = $stmt_dosen->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dosen - SIMDOSEN UM Bengkulu</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --maroon-primary: #6b0018;
            --maroon-dark: #4a0010;
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
        }

        .navbar-custom {
            background-color: var(--maroon-dark);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .brand-logo {
            height: 40px;
            margin-right: 10px;
        }

        .card-custom {
            background-color: #ffffff;
            border: 1px solid #e2d5bf;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(107, 0, 24, 0.05);
        }

        .btn-maroon {
            background-color: var(--maroon-primary);
            color: #ffffff;
            font-weight: 600;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-dark);
            color: var(--cream-accent);
        }

        .table-maroon th {
            background-color: var(--maroon-dark);
            color: var(--cream-text);
            font-weight: 600;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-2">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <img src="images/logo.png" alt="Logo UMB" class="brand-logo" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/e/e0/Logo_Universitas_Muhammadiyah_Bengkulu.png';">
                <span>SIMDOSEN UM Bengkulu</span>
            </a>
            <div class="ms-auto d-flex gap-2">
                <!-- Tombol Ubah Password -->
                <button type="button" class="btn btn-outline-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalUbahPassword">
                    <i class="fa-solid fa-key me-1"></i> Ubah Password
                </button>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
                <a href="laporan_dosen.php" class="btn btn-warning btn-sm fw-bold"><i class="fa-solid fa-print me-1"></i> Lihat Laporan</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--maroon-dark);">
                    <?= $role_id == 1 ? 'Kelola Data Dosen' : 'Profil Data Dosen Saya' ?>
                </h3>
                <p class="text-muted small mb-0">
                    <?= $role_id == 1 ? 'Manajemen Lengkap Data Tenaga Pengajar UM Bengkulu' : 'Halaman Pengelolaan Informasi Profil Dosen Anda' ?>
                </p>
            </div>
            
            <?php if ($role_id == 1): ?>
                <button type="button" class="btn btn-maroon" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Dosen Baru
                </button>
            <?php endif; ?>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card card-custom p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-maroon">
                        <tr>
                            <th>No</th>
                            <th>Nama Dosen</th>
                            <th>NIP / NUPTK</th>
                            <th>Program Studi</th>
                            <th>Jab. Fungsional</th>
                            <th>Pendidikan</th>
                            <th>Status Aktivitas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dosen_list && $dosen_list->num_rows > 0): ?>
                            <?php $no = 1; while ($d = $dosen_list->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong class="d-block text-dark"><?= htmlspecialchars($d['nama']) ?></strong>
                                        <small class="text-muted">Reg: <?= htmlspecialchars($d['nomor_registrasi'] ?: '-') ?></small>
                                    </td>
                                    <td>
                                        <div>NIP: <?= htmlspecialchars($d['nip'] ?: '-') ?></div>
                                        <small class="text-muted">NUPTK: <?= htmlspecialchars($d['nuptk'] ?: '-') ?></small>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($d['nama_prodi'] ?: '-') ?></span></td>
                                    <td><?= htmlspecialchars($d['jabatan_fungsional'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($d['pendidikan_terakhir'] ?: '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($d['status_aktivitas'] == 'Aktif' || empty($d['status_aktivitas'])) ? 'success' : 'warning' ?>">
                                            <?= htmlspecialchars($d['status_aktivitas'] ?: 'Aktif') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $d['id'] ?>" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <?php if ($role_id == 1): ?>
                                            <a href="dosen_crud.php?hapus=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data dosen ini?')" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- MODAL EDIT -->
                                <div class="modal fade" id="modalEdit<?= $d['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title fw-bold">Edit Data Dosen</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Nama Lengkap</label>
                                                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($d['nama']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Program Studi</label>
                                                        <select name="id_prodi" class="form-select" required>
                                                            <?php 
                                                            $prodi_list->data_seek(0);
                                                            while ($p = $prodi_list->fetch_assoc()): 
                                                            ?>
                                                                <option value="<?= $p['id'] ?>" <?= $p['id'] == $d['id_prodi'] ? 'selected' : '' ?>><?= $p['nama_prodi'] ?></option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">NIP</label>
                                                        <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($d['nip']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">NUPTK</label>
                                                        <input type="text" name="nuptk" class="form-control" value="<?= htmlspecialchars($d['nuptk']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">No. Registrasi</label>
                                                        <input type="text" name="nomor_registrasi" class="form-control" value="<?= htmlspecialchars($d['nomor_registrasi']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Jabatan Fungsional</label>
                                                        <input type="text" name="jabatan_fungsional" class="form-control" value="<?= htmlspecialchars($d['jabatan_fungsional']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Kepangkatan</label>
                                                        <input type="text" name="kepangkatan" class="form-control" value="<?= htmlspecialchars($d['kepangkatan']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Pendidikan Terakhir</label>
                                                        <input type="text" name="pendidikan_terakhir" class="form-control" value="<?= htmlspecialchars($d['pendidikan_terakhir']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Rumpun Ilmu</label>
                                                        <input type="text" name="rumpun_ilmu" class="form-control" value="<?= htmlspecialchars($d['rumpun_ilmu']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Status Serdos</label>
                                                        <input type="text" name="status_serdos" class="form-control" value="<?= htmlspecialchars($d['status_serdos']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Status Pegawai</label>
                                                        <input type="text" name="status_pegawai" class="form-control" value="<?= htmlspecialchars($d['status_pegawai']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Ikatan Kerja</label>
                                                        <input type="text" name="ikatan_kerja" class="form-control" value="<?= htmlspecialchars($d['ikatan_kerja']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Status Aktivitas</label>
                                                        <input type="text" name="status_aktivitas" class="form-control" value="<?= htmlspecialchars($d['status_aktivitas']) ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Penempatan</label>
                                                        <input type="text" name="penempatan" class="form-control" value="<?= htmlspecialchars($d['penempatan']) ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit" class="btn btn-maroon">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <?= $role_id == 1 ? 'Belum ada data dosen terdaftar.' : 'Data profil Anda belum terhubung dengan akun ini.' ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL UBAH PASSWORD SAYA -->
    <div class="modal fade" id="modalUbahPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-key me-1"></i> Ubah Password Saya</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Saat Ini</label>
                            <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ketik ulang password baru" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="ubah_password" class="btn btn-warning fw-bold">Perbarui Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH (KHUSUS ADMIN) -->
    <?php if ($role_id == 1): ?>
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">Tambah Data Dosen Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap (Akan Jadi Username) *</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Ujang Juhardi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Program Studi *</label>
                            <select name="id_prodi" class="form-select" required>
                                <?php 
                                $prodi_list->data_seek(0);
                                while ($p = $prodi_list->fetch_assoc()): 
                                ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['nama_prodi'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIP (Akan Jadi Password Default)</label>
                            <input type="text" name="nip" class="form-control" placeholder="Contoh: 11111111">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NUPTK</label>
                            <input type="text" name="nuptk" class="form-control" placeholder="NUPTK">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">No. Registrasi</label>
                            <input type="text" name="nomor_registrasi" class="form-control" placeholder="Nomor Registrasi">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jabatan Fungsional</label>
                            <input type="text" name="jabatan_fungsional" class="form-control" placeholder="Lektor / Asisten Ahli">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kepangkatan</label>
                            <input type="text" name="kepangkatan" class="form-control" placeholder="Penata III/c">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control" placeholder="S2 / S3">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Rumpun Ilmu</label>
                            <input type="text" name="rumpun_ilmu" class="form-control" placeholder="Komputer / Ilmu Komputer">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Serdos</label>
                            <input type="text" name="status_serdos" class="form-control" placeholder="Sudah / Belum">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Pegawai</label>
                            <input type="text" name="status_pegawai" class="form-control" placeholder="Tetap / Kontrak">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ikatan Kerja</label>
                            <input type="text" name="ikatan_kerja" class="form-control" placeholder="Dosen Tetap">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Aktivitas</label>
                            <input type="text" name="status_aktivitas" class="form-control" value="Aktif">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Penempatan</label>
                            <input type="text" name="penempatan" class="form-control" value="UM Bengkulu">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-maroon">Simpan Data & Akun Dosen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>