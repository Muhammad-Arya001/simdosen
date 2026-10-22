-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Okt 2026 pada 11.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simdosen`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_prodi` int(11) NOT NULL,
  `nomor_registrasi` varchar(50) DEFAULT NULL,
  `nuptk` varchar(50) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `nama` varchar(150) NOT NULL,
  `jabatan_fungsional` varchar(100) DEFAULT NULL,
  `kepangkatan` varchar(100) DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) DEFAULT NULL,
  `rumpun_ilmu` varchar(100) DEFAULT NULL,
  `status_serdos` varchar(50) DEFAULT NULL,
  `status_pegawai` varchar(50) DEFAULT NULL,
  `ikatan_kerja` varchar(50) DEFAULT NULL,
  `status_aktivitas` varchar(50) DEFAULT NULL,
  `penempatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dosen`
--

INSERT INTO `dosen` (`id`, `id_user`, `id_prodi`, `nomor_registrasi`, `nuptk`, `nip`, `nama`, `jabatan_fungsional`, `kepangkatan`, `pendidikan_terakhir`, `rumpun_ilmu`, `status_serdos`, `status_pegawai`, `ikatan_kerja`, `status_aktivitas`, `penempatan`) VALUES
(1, 2, 1, '21122', '123', '11111111', 'Ujang Juhardi', 'Dosen', 'Dosen', 'S2', 'Perancangan Sistem', 'Sudah', 'Tetap', 'Dosen Tetap', 'Aktif', 'UM Bengkulu'),
(2, 3, 1, '121213', '12234', '12223112', 'Ardi Wijaya', 'Ketua Prodi', 'Dosen', 'S2', 'Data Analisis, Perancangan Sistem, Image Prosesing', 'Sudah', 'Tetap', 'Dosen Tetap', 'Aktif', 'UM Bengkulu'),
(4, 5, 1, '1234221', '12313', '1213', 'Fitriah', 'Dosen', 'Dosen', 'S2', 'Analisis Data', 'Sudah', 'Tetap', 'Dosen Tetap', 'Aktif', 'UM Bengkulu'),
(5, 6, 1, '121231', '13141', '12312', 'Agung Kharima Hidayah', 'Wakil Dekan', 'Wakil Dekan', 'S2', 'Analisis Data', 'Sudah', 'Tetap', 'Dosen Tetap', 'Aktif', 'UM Bengkulu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `fakultas`
--

CREATE TABLE `fakultas` (
  `id` int(11) NOT NULL,
  `nama_fakultas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `fakultas`
--

INSERT INTO `fakultas` (`id`, `nama_fakultas`) VALUES
(1, 'Teknik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `prodi`
--

CREATE TABLE `prodi` (
  `id` int(11) NOT NULL,
  `id_fakultas` int(11) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `prodi`
--

INSERT INTO `prodi` (`id`, `id_fakultas`, `nama_prodi`) VALUES
(1, 1, 'Teknik Informatika'),
(2, 1, 'Sistem Informasi'),
(3, 1, 'Arsitektur');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `nama_role`) VALUES
(1, 'Admin'),
(2, 'Dosen');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `id_role`) VALUES
(1, 'admin', 'admin123', 1),
(2, 'dosen', 'dosen123', 2),
(3, '12223112', '$2y$10$j14/OANbFfvNl9DrmlX9yeIoePLPgCAfS/hrEW9ui1mgMyydb0E7C', 2),
(4, 'ardiwijaya', '$2y$10$Cuq0jIv/OHlrSFkky3KEr.rLQ3RPw8MxGajVLlNvuDlQJHWu8TIV2', 2),
(5, 'fitriah', '$2y$10$mIoGn8tOIXSIEqpg8b/tFeDyRj4rPvSC6QoAUFcHUs3SfJn6/pUz2', 2),
(6, 'agungkharimahidayah', '$2y$10$whw690eGgPumH35Bv7KLV.zTYx2MVMGWyP4I3IjOW6oduK3z5UGeq', 2);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_user` (`id_user`),
  ADD KEY `id_prodi` (`id_prodi`);

--
-- Indeks untuk tabel `fakultas`
--
ALTER TABLE `fakultas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_fakultas` (`id_fakultas`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `fakultas`
--
ALTER TABLE `fakultas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dosen_ibfk_2` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id`);

--
-- Ketidakleluasaan untuk tabel `prodi`
--
ALTER TABLE `prodi`
  ADD CONSTRAINT `prodi_ibfk_1` FOREIGN KEY (`id_fakultas`) REFERENCES `fakultas` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
