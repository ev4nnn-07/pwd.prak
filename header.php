<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($judul_halaman) ? $judul_halaman : 'Perpustakaan Digital'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">
        📚 <span>Pustaka</span>Digital
    </a>

    <ul class="navbar-menu">
        <li><a href="dashboard.php" <?php if (basename($_SERVER['PHP_SELF']) == 'dashboard.php') echo 'class="aktif"'; ?>>🏠 Dashboard</a></li>
        <li><a href="buku.php" <?php if (basename($_SERVER['PHP_SELF']) == 'buku.php') echo 'class="aktif"'; ?>>📖 Koleksi Buku</a></li>
        <?php if ($_SESSION['role'] == 'user') { ?>
            <li><a href="pinjam.php" <?php if (basename($_SERVER['PHP_SELF']) == 'pinjam.php') echo 'class="aktif"'; ?>>📤 Pinjam Buku</a></li>
            <li><a href="riwayat.php" <?php if (basename($_SERVER['PHP_SELF']) == 'riwayat.php') echo 'class="aktif"'; ?>>📋 Riwayat</a></li>
        <?php } ?>

        <?php if ($_SESSION['role'] == 'admin') { ?>
            <li><a href="kelola_buku.php" <?php if (basename($_SERVER['PHP_SELF']) == 'kelola_buku.php') echo 'class="aktif"'; ?>>⚙️ Kelola Buku</a></li>
            <li><a href="kelola_user.php" <?php if (basename($_SERVER['PHP_SELF']) == 'kelola_user.php') echo 'class="aktif"'; ?>>👥 Kelola User</a></li>
            <li><a href="laporan.php" <?php if (basename($_SERVER['PHP_SELF']) == 'laporan.php') echo 'class="aktif"'; ?>>📊 Laporan</a></li>
        <?php } ?>
    </ul>

    <div class="navbar-user">
        <span>👤 <?php echo $_SESSION['nama']; ?></span>
        <span class="badge-role <?php echo $_SESSION['role']; ?>">
            <?php echo strtoupper($_SESSION['role']); ?>
        </span>
        <a href="logout.php" class="btn btn-sm" style="background:rgba(255,255,255,0.15); color:white; padding:6px 12px;">Logout</a>
    </div>
</nav>