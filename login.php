<?php
session_start();
@include_once 'koneksi.php';

// Jika pengguna sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } elseif (!isset($conn) || $conn->connect_error) {
        $error = "Koneksi database belum terhubung (Periksa koneksi.php).";
    } else {
        // Query LEFT JOIN agar tetap berhasil walau tabel roles belum diisi
        $stmt = $conn->prepare("SELECT u.id, u.username, u.password, u.id_role, COALESCE(r.nama_role, 'Admin') AS nama_role 
                                FROM users u 
                                LEFT JOIN roles r ON u.id_role = r.id 
                                WHERE u.username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $db_password = $user['password'];

            // Cek apakah password cocok (Mendukung Teks Biasa 'admin123' DAN Hash bcrypt)
            $is_valid = ($password === $db_password) || password_verify($password, $db_password);

            if ($is_valid) {
                // Simpan data session user (RBAC)
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role_id']   = $user['id_role'];
                $_SESSION['nama_role'] = $user['nama_role'];

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMDOSEN UM Bengkulu</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS (Tema Maroon & Cream) -->
    <style>
        :root {
            --maroon-primary: #6b0018;
            --maroon-dark: #4a0010;
            --maroon-light: #8a0d28;
            --cream-bg: #f7f3e9;
            --cream-card: #eee5d3;
            --cream-accent: #f0e6d2;
            --text-dark: #382c2a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cream-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(74, 0, 16, 0.85) 0%, rgba(107, 0, 24, 0.85) 100%), 
                        url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;
            padding: 20px;
        }

        .login-card {
            background-color: var(--cream-bg);
            border: 2px solid var(--cream-accent);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .login-header {
            background-color: var(--maroon-dark);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 3px solid var(--maroon-light);
        }

        .login-header img {
            height: 60px;
            width: auto;
            margin-bottom: 12px;
        }

        .login-header h4 {
            font-weight: 800;
            margin: 0;
            font-size: 1.3rem;
        }

        .login-header p {
            color: var(--cream-accent);
            font-size: 0.85rem;
            margin: 4px 0 0 0;
        }

        .login-body {
            padding: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--maroon-dark);
            font-size: 0.9rem;
        }

        .form-control {
            border: 1px solid #dcd1be;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--maroon-primary);
            box-shadow: 0 0 0 0.25rem rgba(107, 0, 24, 0.15);
        }

        .btn-maroon {
            background-color: var(--maroon-primary);
            color: #ffffff;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            border: none;
            width: 100%;
        }

        .btn-maroon:hover {
            background-color: var(--maroon-dark);
            color: var(--cream-accent);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: var(--maroon-primary);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <img src="images/logo.png" alt="Logo UMB" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/e/e0/Logo_Universitas_Muhammadiyah_Bengkulu.png';">
            <h4>SIMDOSEN UM BENGKULU</h4>
            <p>Portal Otentikasi Akses Sistem</p>
        </div>

        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                </div>

                <button type="submit" name="login" class="btn btn-maroon">Masuk Portal</button>
            </form>

            <a href="index.php" class="back-link">← Kembali ke Beranda</a>
        </div>
    </div>

</body>
</html>