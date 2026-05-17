<?php
session_start();

if (empty($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
include 'koneksi.php';

$id_pinjam = $_GET['id'];
$id_user   = $_SESSION['id_user'];

if ($_SESSION['role'] == 'user') {
    $cek = mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_pinjam='$id_pinjam' AND id_user='$id_user'");
} else {
    $cek = mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_pinjam='$id_pinjam'");
}

if (mysqli_num_rows($cek) > 0) {
    $data_pinjam = mysqli_fetch_array($cek);
    $id_buku     = $data_pinjam['id_buku'];

    mysqli_query($konek, "UPDATE peminjaman SET status='dikembalikan' WHERE id_pinjam='$id_pinjam'");

    mysqli_query($konek, "UPDATE buku SET stok = stok + 1 WHERE id_buku='$id_buku'");

    header("location: riwayat.php?pesan=berhasil_kembali");
} else {
    header("location: dashboard.php");
}
exit();
?>