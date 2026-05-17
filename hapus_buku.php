<?php
session_start();

if (empty($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}
include 'koneksi.php';
$id_buku = $_GET['id'];

$cek_pinjam = mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_buku='$id_buku' AND status='dipinjam'");

if (mysqli_num_rows($cek_pinjam) > 0) {
    header("location: kelola_buku.php?pesan=gagal_hapus");
} else {
    mysqli_query($konek, "DELETE FROM buku WHERE id_buku='$id_buku'");
    header("location: kelola_buku.php?pesan=berhasil_hapus");
}
exit();
?>