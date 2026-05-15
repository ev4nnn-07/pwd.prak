<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['username'])) {
    header('Location: index.php?pesan=belum_login');
    exit();
}

$total_buku     = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM buku"));
$total_user     = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM users WHERE role='user'"));
$sedang_pinjam  = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
$total_kembali  = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE status='dikembalikan'"));

$query_buku_baru = mysqli_query($konek, "SELECT * FROM buku ORDER BY id_buku DESC LIMIT 5");

$query_pinjam_baru = mysqli_query($konek, "
    SELECT p.*, u.nama as nama_user, b.judul as judul_buku
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id
    JOIN buku b ON p.id_buku = b.id_buku
    ORDER BY p.id_pinjam DESC LIMIT 5
");

if ($_SESSION['role'] == 'user') {
    $id_user = $_SESSION['id_user'];
    $query_pinjam_saya = mysqli_query($konek, "
        SELECT p.*, b.judul, b.pengarang
        FROM peminjaman p
        JOIN buku b ON p.id_buku = b.id_buku
        WHERE p.id_user='$id_user' AND p.status='dipinjam'
        ORDER BY p.tgl_pinjam DESC
    ");
}

$judul_halaman = "Dashboard - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>Selamat Datang, <?php echo $_SESSION['nama']; ?>! 👋</h1>
    <p>
        <?php
        if ($_SESSION['role'] == 'admin') {
            echo "Panel Admin — Kelola seluruh sistem perpustakaan digital.";
        } else {
            echo "Temukan dan pinjam buku favoritmu dari koleksi kami.";
        }
        ?>
    </p>
</div>

<div class="container fade-in">

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <h3><?php echo $total_buku; ?></h3>
                <p>Total Koleksi Buku</p>
            </div>
        </div>
        <div class="stat-card kuning">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo $total_user; ?></h3>
                <p>Member Terdaftar</p>
            </div>
        </div>
        <div class="stat-card merah">
            <div class="stat-icon">📤</div>
            <div class="stat-info">
                <h3><?php echo $sedang_pinjam; ?></h3>
                <p>Sedang Dipinjam</p>
            </div>
        </div>
        <div class="stat-card coklat">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3><?php echo $total_kembali; ?></h3>
                <p>Sudah Dikembalikan</p>
            </div>
        </div>
    </div>

    <?php if ($_SESSION['role'] == 'admin') { ?>

        <div class="card mb-20">
            <div class="card-header">📋 Peminjaman Terbaru</div>
            <div class="card-body">
                <?php $ada_data = false; ?>
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_array($query_pinjam_baru)) {
                        $ada_data = true;
                        if ($data['status'] == 'dipinjam') {
                            $badge = '<span class="badge badge-merah">Dipinjam</span>';
                        } else {
                            $badge = '<span class="badge badge-hijau">Dikembalikan</span>';
                        }
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $data['nama_user']; ?></td>
                            <td><?php echo $data['judul_buku']; ?></td>
                            <td><?php echo $data['tgl_pinjam']; ?></td>
                            <td><?php echo $data['tgl_kembali']; ?></td>
                            <td><?php echo $badge; ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (!$ada_data) { ?>
                        <tr><td colspan="6" class="text-center" style="color:var(--teks-abu); padding:30px;">Belum ada data peminjaman.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
                <div class="mt-20">
                    <a href="laporan.php" class="btn btn-outline">📊 Lihat Semua Laporan</a>
                </div>
            </div>
        </div>

    <?php } else { ?>

        <div class="card mb-20">
            <div class="card-header">📤 Buku yang Sedang Saya Pinjam</div>
            <div class="card-body">
                <?php $ada = false; ?>
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_array($query_pinjam_saya)) {
                        $ada = true;
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $data['judul']; ?></td>
                            <td><?php echo $data['pengarang']; ?></td>
                            <td><?php echo $data['tgl_pinjam']; ?></td>
                            <td><?php echo $data['tgl_kembali']; ?></td>
                            <td>
                                <a href="kembalikan.php?id=<?php echo $data['id_pinjam']; ?>"
                                   onclick="return confirm('Kembalikan buku ini?')"
                                   class="btn btn-sm btn-primary">↩️ Kembalikan</a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!$ada) { ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <p>Kamu belum meminjam buku apapun.</p>
                                    <a href="pinjam.php" class="btn btn-primary mt-20">📖 Cari & Pinjam Buku</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    <?php } ?>

    <div class="card">
        <div class="card-header">🆕 Koleksi Buku Terbaru</div>
        <div class="card-body">
            <div class="buku-grid">
            <?php
            $warna_cover = array('', 'buku-cover-coklat', 'buku-cover-biru', 'buku-cover-ungu', '');
            $emoji_buku  = array('📘', '📗', '📕', '📙', '📓');
            $i = 0;

            while ($buku = mysqli_fetch_array($query_buku_baru)) {
                $cover_class = $warna_cover[$i % 5];
                $emoji = $emoji_buku[$i % 5];
                $i++;
            ?>
                <div class="buku-card">
                    <div class="buku-cover <?php echo $cover_class; ?>">
                        <?php echo $emoji; ?>
                        <span class="stok-badge">Stok: <?php echo $buku['stok']; ?></span>
                    </div>
                    <div class="buku-info">
                        <h4><?php echo $buku['judul']; ?></h4>
                        <p>✍️ <?php echo $buku['pengarang']; ?></p>
                        <p>🏢 <?php echo $buku['penerbit']; ?></p>
                        <span class="buku-kategori"><?php echo $buku['kategori']; ?></span>
                    </div>
                    <div class="buku-actions">
                        <a href="buku.php" class="btn btn-sm btn-outline">Lihat Buku</a>
                        <?php if ($_SESSION['role'] == 'user' && $buku['stok'] > 0) { ?>
                            <a href="pinjam.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-sm btn-primary">Pinjam</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>