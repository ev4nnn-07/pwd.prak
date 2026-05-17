<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

$pesan = "";
$tipe  = "";

if (isset($_POST['submit_tambah'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];
    $tgl      = date('Y-m-d');

    $cek = mysqli_query($konek, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Username \"$username\" sudah digunakan!";
        $tipe  = "danger";
    } else {
        $query = mysqli_query($konek,
            "INSERT INTO users (username, password, nama, email, role, tgl_daftar)
             VALUES ('$username', '$password', '$nama', '$email', '$role', '$tgl')"
        );

        if ($query) {
            $pesan = "User \"$nama\" berhasil ditambahkan!";
            $tipe  = "success";
        } else {
            $pesan = "Gagal menambahkan user!";
            $tipe  = "danger";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    if ($id_hapus != $_SESSION['id_user']) {
        mysqli_query($konek, "DELETE FROM users WHERE id='$id_hapus'");
        $pesan = "User berhasil dihapus.";
        $tipe  = "success";
    } else {
        $pesan = "Tidak bisa menghapus akun sendiri!";
        $tipe  = "danger";
    }
}

$query_users = mysqli_query($konek, "SELECT * FROM users ORDER BY role ASC, nama ASC");

$judul_halaman = "Kelola User - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>👥 Kelola Pengguna</h1>
    <p>Tambah dan kelola akun pengguna perpustakaan.</p>
</div>

<div class="container fade-in">

    <?php if ($pesan != "") { ?>
        <div class="alert alert-<?php echo $tipe; ?>">
            <?php echo ($tipe == 'success') ? '✅' : '⚠️'; ?> <?php echo $pesan; ?>
        </div>
    <?php } ?>

    <div style="display:grid; grid-template-columns: 340px 1fr; gap: 24px; align-items: start;">

        <div class="card" style="position:sticky; top:90px;">
            <div class="card-header">➕ Tambah Pengguna Baru</div>
            <div class="card-body">
                <form method="POST" action="kelola_user.php">
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama" placeholder="Nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" name="username" placeholder="Username untuk login" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="email@domain.com">
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role" required>
                            <option value="user">👤 User (Member)</option>
                            <option value="admin">🔑 Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="submit_tambah" class="btn btn-primary" style="width:100%; justify-content:center;">
                        ➕ Tambah Pengguna
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">📋 Daftar Pengguna Terdaftar</div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tgl Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    $ada = false;

                    while ($user = mysqli_fetch_array($query_users)) {
                        $ada = true;

                        if ($user['role'] == 'admin') {
                            $role_badge = '<span class="badge badge-hijau">🔑 Admin</span>';
                        } else {
                            $role_badge = '<span class="badge badge-biru">👤 User</span>';
                        }

                        if ($user['id'] != $_SESSION['id_user']) {
                            $tombol_hapus = '<a href="kelola_user.php?hapus=' . $user['id'] . '"
                                             onclick="return confirm(\'Hapus user ' . $user['nama'] . '?\')"
                                             class="btn btn-sm btn-danger">🗑️</a>';
                        } else {
                            $tombol_hapus = '<span style="font-size:0.75rem; color:var(--teks-abu);">Akun saya</span>';
                        }
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td style="font-weight:500;"><?php echo $user['nama']; ?></td>
                            <td><code style="background:var(--krem); padding:2px 6px; border-radius:4px; font-size:0.85rem;"><?php echo $user['username']; ?></code></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $role_badge; ?></td>
                            <td><?php echo $user['tgl_daftar']; ?></td>
                            <td><?php echo $tombol_hapus; ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!$ada) { ?>
                        <tr><td colspan="7" class="text-center" style="padding:30px; color:var(--teks-abu);">Belum ada pengguna.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="footer">
    &copy; <?php echo date('Y'); ?> Perpustakaan Digital — Prodi Teknik Informatika UPN "Veteran" Yogyakarta
</div>
</body>
</html>