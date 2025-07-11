-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Apr 2025 pada 11.56
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_koperasi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `latitude` decimal(10,6) DEFAULT NULL,
  `longitude` decimal(10,6) DEFAULT NULL,
  `lokasi` text DEFAULT NULL,
  `status` enum('masuk','izin','sakit','alpha') DEFAULT 'masuk',
  `jarak_meter` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `id_karyawan`, `tanggal`, `jam_masuk`, `jam_pulang`, `latitude`, `longitude`, `lokasi`, `status`, `jarak_meter`, `keterangan`, `tanggal_buat`, `tanggal_update`) VALUES
(1, 1, '2025-04-20', '14:40:00', '17:40:00', NULL, NULL, NULL, 'masuk', NULL, '1', '2025-04-20 14:41:08', '2025-04-20 14:45:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bpjs`
--

CREATE TABLE `bpjs` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bpjs`
--

INSERT INTO `bpjs` (`id`, `id_karyawan`, `tanggal`, `jumlah`, `keterangan`, `tanggal_buat`, `tanggal_update`) VALUES
(1, 2, '2025-04-20', 560000.00, '', '2025-04-20 14:19:00', '2025-04-20 14:19:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `saldo_kasbon` decimal(15,2) DEFAULT 0.00,
  `saldo_tabungan` decimal(15,2) DEFAULT 0.00,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `role` enum('owner','admin','karyawan') DEFAULT 'karyawan',
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama_karyawan`, `username`, `password`, `no_hp`, `alamat`, `saldo_kasbon`, `saldo_tabungan`, `status`, `role`, `tanggal_buat`, `tanggal_update`) VALUES
(1, '1', 'admin', 'admin', '$2y$10$kCHxsxO1GnikKsnBSHYFhuIZJibcNEyxInvHNfChsOEIECYlkbjxW', '087780226969', 'Karawang', 0.00, 0.00, 'aktif', 'admin', '2025-04-20 11:15:38', '2025-04-20 15:11:41'),
(2, '321', 'Agus hari murti', 'agus', '$2y$10$IbDh7n5aunJSYJCNgvQIoePOeQt9De7uyX.XZgYPXIy7XxwWCXavq', '0', 'karawang', 600000.00, 200000.00, 'aktif', 'karyawan', '2025-04-20 11:17:30', '2025-04-20 17:01:15'),
(4, '222', 'hohmat', 'rohmat', '$2y$10$y/wCScXwN.xlFjq/i1yBAOgg5gV3rDXIYMRZr/WhSQ5pPB0YbWxvy', '0', 'karawang', 10000.00, 100000.00, 'aktif', 'karyawan', '2025-04-20 17:51:39', '2025-04-20 19:42:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasbon`
--

CREATE TABLE `kasbon` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('pinjam','bayar') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasbon`
--

INSERT INTO `kasbon` (`id`, `id_karyawan`, `tanggal`, `jenis`, `jumlah`, `keterangan`, `tanggal_buat`, `tanggal_update`) VALUES
(1, 2, '2025-04-20', 'pinjam', 200000.00, '', '2025-04-20 14:17:31', '2025-04-20 14:17:31'),
(2, 4, '2025-04-20', 'pinjam', 10000.00, '', '2025-04-20 19:41:54', '2025-04-20 19:41:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `id` int(11) NOT NULL,
  `nama_pekerjaan` varchar(100) NOT NULL,
  `harga_koperasi` decimal(15,2) NOT NULL,
  `harga_karyawan` decimal(15,2) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pekerjaan`
--

INSERT INTO `pekerjaan` (`id`, `nama_pekerjaan`, `harga_koperasi`, `harga_karyawan`, `status`, `tanggal_buat`, `tanggal_update`) VALUES
(1, 'Perbaikan atap', 200000.00, 175000.00, 'aktif', '2025-04-20 11:17:49', '2025-04-20 12:50:19'),
(2, 'Perbaiki WC', 250000.00, 200000.00, 'aktif', '2025-04-20 12:31:26', '2025-04-20 12:31:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendapatan`
--

CREATE TABLE `pendapatan` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_pendapatan` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','selesai') DEFAULT 'pending',
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendapatan`
--

INSERT INTO `pendapatan` (`id`, `id_karyawan`, `tanggal`, `total_pendapatan`, `status`, `tanggal_buat`, `tanggal_update`) VALUES
(7, 2, '2025-04-20', 2100000.00, 'pending', '2025-04-20 17:51:09', '2025-04-20 17:51:09'),
(8, 2, '2025-04-20', 2525000.00, 'pending', '2025-04-20 18:35:05', '2025-04-20 18:35:05'),
(9, 4, '2025-04-20', 5250000.00, 'pending', '2025-04-20 19:40:42', '2025-04-20 19:40:42'),
(10, 2, '2025-04-22', 1925000.00, 'pending', '2025-04-22 16:29:57', '2025-04-22 16:29:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendapatan_detail`
--

CREATE TABLE `pendapatan_detail` (
  `id` int(11) NOT NULL,
  `id_pendapatan` int(11) NOT NULL,
  `id_pekerjaan` int(11) NOT NULL,
  `banyak` int(11) NOT NULL,
  `harga_koperasi` decimal(15,2) NOT NULL,
  `harga_karyawan` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendapatan_detail`
--

INSERT INTO `pendapatan_detail` (`id`, `id_pendapatan`, `id_pekerjaan`, `banyak`, `harga_koperasi`, `harga_karyawan`, `total`) VALUES
(9, 7, 1, 12, 200000.00, 175000.00, 2100000.00),
(10, 8, 2, 10, 250000.00, 200000.00, 2000000.00),
(11, 8, 1, 3, 200000.00, 175000.00, 525000.00),
(12, 9, 1, 30, 200000.00, 175000.00, 5250000.00),
(13, 10, 1, 11, 200000.00, 175000.00, 1925000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `slip_gaji`
--

CREATE TABLE `slip_gaji` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `bulan` varchar(7) NOT NULL,
  `total_pendapatan` decimal(15,2) DEFAULT 0.00,
  `total_kasbon` decimal(15,2) DEFAULT 0.00,
  `total_tabungan` decimal(15,2) DEFAULT 0.00,
  `total_bpjs` decimal(15,2) DEFAULT 0.00,
  `gaji_bersih` decimal(15,2) DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `tanggal_cetak` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tabungan`
--

CREATE TABLE `tabungan` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` enum('setor','tarik') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT current_timestamp(),
  `tanggal_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tabungan`
--

INSERT INTO `tabungan` (`id`, `id_karyawan`, `tanggal`, `jenis`, `jumlah`, `keterangan`, `tanggal_buat`, `tanggal_update`) VALUES
(1, 2, '2025-04-20', 'setor', 100000.00, '', '2025-04-20 14:19:29', '2025-04-20 14:19:29'),
(2, 4, '2025-04-20', 'setor', 100000.00, '', '2025-04-20 19:42:35', '2025-04-20 19:42:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_absen` (`id_karyawan`,`tanggal`);

--
-- Indeks untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `kasbon`
--
ALTER TABLE `kasbon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pendapatan`
--
ALTER TABLE `pendapatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `pendapatan_detail`
--
ALTER TABLE `pendapatan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pendapatan` (`id_pendapatan`),
  ADD KEY `id_pekerjaan` (`id_pekerjaan`);

--
-- Indeks untuk tabel `slip_gaji`
--
ALTER TABLE `slip_gaji`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slip` (`id_karyawan`,`bulan`);

--
-- Indeks untuk tabel `tabungan`
--
ALTER TABLE `tabungan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kasbon`
--
ALTER TABLE `kasbon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pendapatan`
--
ALTER TABLE `pendapatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `pendapatan_detail`
--
ALTER TABLE `pendapatan_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `slip_gaji`
--
ALTER TABLE `slip_gaji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tabungan`
--
ALTER TABLE `tabungan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bpjs`
--
ALTER TABLE `bpjs`
  ADD CONSTRAINT `bpjs_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kasbon`
--
ALTER TABLE `kasbon`
  ADD CONSTRAINT `kasbon_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendapatan`
--
ALTER TABLE `pendapatan`
  ADD CONSTRAINT `pendapatan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendapatan_detail`
--
ALTER TABLE `pendapatan_detail`
  ADD CONSTRAINT `pendapatan_detail_ibfk_1` FOREIGN KEY (`id_pendapatan`) REFERENCES `pendapatan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pendapatan_detail_ibfk_2` FOREIGN KEY (`id_pekerjaan`) REFERENCES `pekerjaan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `slip_gaji`
--
ALTER TABLE `slip_gaji`
  ADD CONSTRAINT `slip_gaji_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tabungan`
--
ALTER TABLE `tabungan`
  ADD CONSTRAINT `tabungan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
