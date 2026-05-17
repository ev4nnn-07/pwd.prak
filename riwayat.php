<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['username'])) {
    header('Location: index.php?pesan=belum_login');
    exit();
}
if ($_SESSION['role'] != 'user') {
    header('Location: dashboard.php');
    exit();
}

$id_user = $_SESSION['id_user'];
$pesan_url = isset($_GET['pesan']) ? $_GET['pesan'] : '';

$query_riwayat = mysqli_query($konek, "
    SELECT p.*, b.judul, b.pengarang, b.kategori
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_user = '$id_user'
    ORDER BY p.id_pinjam DESC
");

$total_pinjam   = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_user='$id_user'"));
$masih_pinjam   = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_user='$id_user' AND status='dipinjam'"));
$sudah_kembali  = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_user='$id_user' AND status='dikembalikan'"));

$judul_halaman = "Riwayat Peminjaman - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>📋 Riwayat Peminjaman Saya</h1>
    <p>Seluruh rekap peminjaman buku yang pernah kamu lakukan.</p>
</div>

<div class="container fade-in">

    <?php if ($pesan_url == 'berhasil_kembali') { ?>
        <div class="alert alert-success">✅ Buku berhasil dikembalikan. Terima kasih!</div>
    <?php } ?>

    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <h3><?php echo $total_pinjam; ?></h3>
                <p>Total Peminjaman</p>
            </div>
        </div>
        <div class="stat-card merah">
            <div class="stat-icon">📤</div>
            <div class="stat-info">
                <h3><?php echo $masih_pinjam; ?></h3>
                <p>Sedang Dipinjam</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3><?php echo $sudah_kembali; ?></h3>
                <p>Sudah Dikembalikan</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">📖 Daftar Riwayat Peminjaman</div>
        <div class="card-body">
            <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Kategori</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $ada = false;

                while ($data = mysqli_fetch_array($query_riwayat)) {
                    $ada = true;

                    if ($data['status'] == 'dipinjam') {
                        $badge = '<span class="badge badge-merah">📤 Dipinjam</span>';
                        $tombol = '<a href="kembalikan.php?id=' . $data['id_pinjam'] . '"
                                     onclick="return confirm(\'Kembalikan buku ini?\')"
                                     class="btn btn-sm btn-primary">↩️ Kembalikan</a>';
                    } else {
                        $badge = '<span class="badge badge-hijau">✅ Kembali</span>';
                        $tombol = '<span style="color:var(--teks-abu); font-size:0.8rem;">—</span>';
                    }
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td style="font-weight:500;"><?php echo $data['judul']; ?></td>
                        <td><?php echo $data['pengarang']; ?></td>
                        <td><span class="buku-kategori"><?php echo $data['kategori']; ?></span></td>
                        <td><?php echo $data['tgl_pinjam']; ?></td>
                        <td><?php echo $data['tgl_kembali']; ?></td>
                        <td><?php echo $badge; ?></td>
                        <td><?php echo $tombol; ?></td>
                    </tr>
                <?php } ?>
                <?php if (!$ada) { ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <p>Belum ada riwayat peminjaman.</p>
                                <a href="pinjam.php" class="btn btn-primary mt-20">📖 Pinjam Buku Sekarang</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
<?php include 'footer.php'; ?>