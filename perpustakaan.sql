-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Bulan Mei 2026 pada 12.22
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
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(5) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pengarang` varchar(100) NOT NULL,
  `penerbit` varchar(100) NOT NULL,
  `tahun_terbit` year(4) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `stok` int(3) NOT NULL DEFAULT 1,
  `cover` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori`, `stok`, `cover`) VALUES
(1, 'Pemrograman Web dengan PHP', 'Betha Sidik', 'Informatika', '2014', 'Teknologi', 3, NULL),
(2, 'Belajar CSS Modern', 'Ahmad Hakim', 'Elex Media', '2020', 'Teknologi', 2, NULL),
(3, 'Database MySQL Lengkap', 'Bunafit Nugroho', 'Lokomedia', '2018', 'Teknologi', 4, NULL),
(4, 'Algoritma dan Pemrograman', 'Rinaldi Munir', 'Informatika', '2016', 'Ilmu Komputer', 3, NULL),
(5, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', 'Novel', 5, NULL),
(6, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', '1980', 'Sastra', 2, NULL),
(7, 'Matematika Diskrit', 'Kenneth Rosen', 'McGraw-Hill', '2012', 'Matematika', 3, NULL),
(8, 'Jaringan Komputer', 'Andrew Tanenbaum', 'Pearson', '2011', 'Teknologi', 2, NULL),
(9, 'Kecerdasan Buatan', 'Stuart Russell', 'Pearson', '2010', 'Ilmu Komputer', 1, NULL),
(10, 'Harry Potter', 'J.K. Rowling', 'Gramedia', '2000', 'Novel', 6, NULL),
(11, '5cm', 'Donny Dhirgantoro', 'Grasindo', '2005', 'Novel', 7, NULL),
(12, 'Dilan 1990', 'Pidi Baiq', 'Pastel Books', '2014', 'Novel', 6, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_pinjam` int(5) NOT NULL,
  `id_user` int(5) NOT NULL,
  `id_buku` int(5) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `status` enum('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_pinjam`, `id_user`, `id_buku`, `tgl_pinjam`, `tgl_kembali`, `status`) VALUES
(1, 2, 6, '2026-05-07', '2026-05-21', 'dikembalikan'),
(2, 2, 9, '2026-05-07', '2026-05-08', 'dikembalikan'),
(3, 2, 9, '2026-05-07', '2026-05-08', 'dikembalikan'),
(4, 2, 11, '2026-05-08', '2026-05-09', 'dikembalikan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `tgl_daftar` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `email`, `role`, `tgl_daftar`) VALUES
(2, 'budi', 'budi123', 'Budi Santoso', 'budi@email.com', 'user', '2026-05-07'),
(4, 'Evan', 'Sman5garut', 'Revand Khemaluddin', 'abangevan2005@gmail.com', 'admin', '2026-05-07'),
(6, 'Ikram', 'ikram123', 'Muhammad Ikram Mughni', 'Ikram@gmail.com', 'user', '2026-05-07'),
(7, 'Barita', 'barita123', 'Barita Davitya Setiawati', 'barita@gmail.com', 'user', '2026-05-07'),
(8, 'Geragas', 'bagas123', 'Bagas Raffi Ananta', 'bagas@gmail.com', 'user', '2026-05-07'),
(9, 'Lano', 'lano123', 'Maylano', 'maylano@gmail.com', 'admin', '2026-05-08');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_pinjam`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_pinjam` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
