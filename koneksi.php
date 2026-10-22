<?php
// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "simdosen";

// Membuat Koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Periksa Koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set karakter encoding ke UTF-8
$conn->set_charset("utf8");
?>