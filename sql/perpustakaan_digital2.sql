-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Jun 2025 pada 16.04
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_digital2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `nomor_anggota` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `pekerjaan` varchar(100) NOT NULL,
  `tanggal_daftar` date NOT NULL,
  `tanggal_expired` date DEFAULT NULL,
  `status` enum('aktif','nonaktif','suspend') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `anggota`
--

INSERT INTO `anggota` (`id`, `nomor_anggota`, `nama`, `password`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `telepon`, `email`, `pekerjaan`, `tanggal_daftar`, `tanggal_expired`, `status`) VALUES
(4, 'AGT0001', 'AQHLIA NURFAHMA', '827ccb0eea8a706c4c34a16891f84e7b', 'P', 'Bontang', '2004-06-16', 'jl parikesit', '081520362390', 'aqhlianrmdnhi@gmail.com', 'pelajar', '2025-06-30', '2026-06-30', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `kode_buku` varchar(20) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `penulis_id` int(11) DEFAULT NULL,
  `penerbit_id` int(11) DEFAULT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `rak_id` int(11) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `jumlah_halaman` int(11) DEFAULT NULL,
  `jumlah_total` int(11) DEFAULT 1,
  `jumlah_tersedia` int(11) DEFAULT 1,
  `tanggal_masuk` date DEFAULT NULL,
  `kondisi` enum('baik','rusak','hilang') DEFAULT 'baik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `kode_kategori` varchar(10) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `kode_kategori`, `nama_kategori`, `deskripsi`) VALUES
(1, 'FIK', 'Fiksi', 'Buku-buku fiksi dan novel'),
(2, 'NFK', 'Non-Fiksi', 'Buku pengetahuan umum'),
(3, 'TEK', 'Teknologi', 'Buku tentang teknologi dan komputer'),
(4, 'SEJ', 'Sejarah', 'Buku sejarah dan biografi'),
(5, 'ILM', 'Ilmiah', 'Buku-buku ilmiah dan penelitian'),
(6, 'KTG0001', 'buku anak', 'buku belajar menulis dan mewarnai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kunjungan`
--

CREATE TABLE `kunjungan` (
  `id` int(11) NOT NULL,
  `nomor_anggota` varchar(20) DEFAULT NULL,
  `nama_pengunjung` varchar(100) DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `tanggal_kunjungan` date DEFAULT NULL,
  `waktu_masuk` time DEFAULT NULL,
  `waktu_keluar` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kunjungan`
--

INSERT INTO `kunjungan` (`id`, `nomor_anggota`, `nama_pengunjung`, `keperluan`, `tanggal_kunjungan`, `waktu_masuk`, `waktu_keluar`) VALUES
(1, 'lia', 'Pengunjung', 'membaca buku', '2025-06-27', '20:24:10', NULL),
(2, 'lia', 'Pengunjung', 'membaca buku', '2025-06-27', '20:24:15', NULL),
(3, 'lia', 'Pengunjung', 'membaca buku', '2025-06-27', '20:24:16', NULL),
(4, 'lia', 'Pengunjung', 'membaca buku', '2025-06-27', '20:24:17', NULL),
(5, 'AGT0002', 'AQHLIA NURFAHMA', 'meminjam buku', '2025-06-28', '00:31:59', NULL),
(6, 'AGT0002', 'AQHLIA NURFAHMA', 'meminjam buku', '2025-06-28', '00:32:03', NULL),
(7, 'AGT0002', 'AQHLIA NURFAHMA', 'meminjam buku', '2025-06-28', '00:32:05', NULL),
(8, 'AGT0002', 'AQHLIA NURFAHMA', 'meminjam buku', '2025-06-28', '00:32:09', NULL),
(9, 'AGT0002', 'AQHLIA NURFAHMA', 'meminjam buku', '2025-06-28', '00:32:10', NULL),
(10, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '16:44:24', NULL),
(11, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '16:44:27', NULL),
(12, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '16:44:34', NULL),
(13, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:01', NULL),
(14, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:05', NULL),
(15, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:06', NULL),
(16, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:08', NULL),
(17, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:09', NULL),
(18, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:10', NULL),
(19, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:11', NULL),
(20, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:17', NULL),
(21, 'AGT0002', 'AQHLIA NURFAHMA', 'membaca', '2025-06-29', '20:53:24', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `kode_pinjam` varchar(20) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `lama_pinjam` int(11) DEFAULT 7,
  `status` enum('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
  `denda` decimal(10,2) DEFAULT 0.00,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penerbit`
--

CREATE TABLE `penerbit` (
  `id` int(11) NOT NULL,
  `kode_penerbit` varchar(10) NOT NULL,
  `nama_penerbit` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penerbit`
--

INSERT INTO `penerbit` (`id`, `kode_penerbit`, `nama_penerbit`, `alamat`, `email`) VALUES
(2, 'PNB001', 'Elex Media Komputindo', 'Jl. Palmerah Barat No. 123, Jakarta', 'Elexmedia@gmail.com'),
(3, 'PNB002', 'Andi Publisher', 'Jl. Diponegoro No. 45, Yogyakarta', 'AndiPublisher@gmail.com'),
(4, 'PNB003', 'Informatika Bandung', 'Jl. Cihampelas No. 77, Bandung', 'Informatikabdg@gmail.com'),
(5, 'PNB004', 'Graha Ilmu', 'Jl. Kaliurang Km. 14, Sleman', 'Grahailmu@gmail.com'),
(6, 'PNB005', 'Maxikom', 'Jl. Sultan Agung No. 88, Semarang', 'Maxikom@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penulis`
--

CREATE TABLE `penulis` (
  `id` int(11) NOT NULL,
  `kode_penulis` varchar(10) NOT NULL,
  `nama_penulis` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penulis`
--

INSERT INTO `penulis` (`id`, `kode_penulis`, `nama_penulis`, `tempat_lahir`, `tanggal_lahir`, `email`) VALUES
(11, 'PNS001', 'Budi Santosa', 'Jakarta', '1975-05-10', 'Budi Santosa@gmail.com'),
(12, 'PNS002', 'Rizky Hidayat', 'Bandung', '1980-08-22', 'Rizky Hidayat@gmail.com'),
(13, 'PNS003', 'Dian Pratama', 'Surabaya', '1978-12-05', 'Dian Pratama@gmail.com'),
(14, 'PNS004', 'Siti Nurjanah', 'Yogyakarta', '1985-03-17', 'Siti Nurjanah@gmail.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rak`
--

CREATE TABLE `rak` (
  `id` int(11) NOT NULL,
  `kode_rak` varchar(10) NOT NULL,
  `nama_rak` varchar(50) NOT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `kapasitas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rak`
--

INSERT INTO `rak` (`id`, `kode_rak`, `nama_rak`, `lokasi`, `kapasitas`) VALUES
(1, 'A1', 'Rak A1', 'Lantai 1 - Kiri', 100),
(2, 'A2', 'Rak A2', 'Lantai 1 - Tengah', 100),
(3, 'B1', 'Rak B1', 'Lantai 2 - Kiri', 100),
(4, 'B2', 'Rak B2', 'Lantai 2 - Tengah', 100);

-- --------------------------------------------------------

--
-- Struktur dari tabel `setting_aplikasi`
--

CREATE TABLE `setting_aplikasi` (
  `id` int(11) NOT NULL,
  `nama_aplikasi` varchar(100) DEFAULT 'Perpustakaan Digital',
  `logo` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `kepala_perpustakaan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `setting_aplikasi`
--

INSERT INTO `setting_aplikasi` (`id`, `nama_aplikasi`, `logo`, `alamat`, `telepon`, `email`, `kepala_perpustakaan`) VALUES
(1, 'Lunar Library', NULL, 'Jl. Pendidikan No. 123', '021-12345678', 'info@perpustakaan.com', 'Dr. Muhammad Haikal, M.Pd'),
(2, 'Lunar Library', NULL, 'Jl. Pendidikan No. 123', '021-12345678', 'info@perpustakaan.com', 'Dr. Muhammad Haikal, M.Pd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `email`, `status`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin', 'Administrator Perpustakaan', 'admin@perpustakaan.com', 'aktif', '2025-06-27 12:00:33'),
(2, 'user', '6ad14ba9986e3615423dfca256d04e3f', 'user', 'User Perpustakaan', 'user@perpustakaan.com', 'aktif', '2025-06-27 12:00:33');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_anggota` (`nomor_anggota`);

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_buku` (`kode_buku`),
  ADD KEY `penulis_id` (`penulis_id`),
  ADD KEY `penerbit_id` (`penerbit_id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `rak_id` (`rak_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kategori` (`kode_kategori`);

--
-- Indeks untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pinjam` (`kode_pinjam`),
  ADD KEY `anggota_id` (`anggota_id`),
  ADD KEY `buku_id` (`buku_id`);

--
-- Indeks untuk tabel `penerbit`
--
ALTER TABLE `penerbit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_penerbit` (`kode_penerbit`);

--
-- Indeks untuk tabel `penulis`
--
ALTER TABLE `penulis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_penulis` (`kode_penulis`);

--
-- Indeks untuk tabel `rak`
--
ALTER TABLE `rak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_rak` (`kode_rak`);

--
-- Indeks untuk tabel `setting_aplikasi`
--
ALTER TABLE `setting_aplikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penerbit`
--
ALTER TABLE `penerbit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `penulis`
--
ALTER TABLE `penulis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `rak`
--
ALTER TABLE `rak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `setting_aplikasi`
--
ALTER TABLE `setting_aplikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `buku_ibfk_1` FOREIGN KEY (`penulis_id`) REFERENCES `penulis` (`id`),
  ADD CONSTRAINT `buku_ibfk_2` FOREIGN KEY (`penerbit_id`) REFERENCES `penerbit` (`id`),
  ADD CONSTRAINT `buku_ibfk_3` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  ADD CONSTRAINT `buku_ibfk_4` FOREIGN KEY (`rak_id`) REFERENCES `rak` (`id`);

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
