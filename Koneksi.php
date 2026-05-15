<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "perpustakaan";
 
$konek = new mysqli($hostname, $username, $password, $database);
 
if ($konek->connect_error) {
    die("Maaf koneksi gagal: " . $konek->connect_error);
}
 
$konek->set_charset("utf8");
?>