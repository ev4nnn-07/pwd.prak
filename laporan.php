<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_user   = isset($_GET['user']) ? $_GET['user'] : '';

$where = "WHERE 1=1";
if ($filter_status != '') {
    $where .= " AND p.status='$filter_status'";
}
if ($filter_user != '') {
    $where .= " AND p.id_user='$filter_user'";
}

$query_laporan = mysqli_query($konek, "
    SELECT p.*, u.nama as nama_user, u.username,
           b.judul as judul_buku, b.pengarang, b.kategori
    FROM peminjaman p
    JOIN users u ON p.id_user = u.id
    JOIN buku b ON p.id_buku = b.id_buku
    $where
    ORDER BY p.id_pinjam DESC
");

$query_user_list = mysqli_query($konek, "SELECT * FROM users WHERE role='user' ORDER BY nama");

$total_semua    = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman"));
$total_pinjam   = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
$total_kembali  = mysqli_num_rows(mysqli_query($konek, "SELECT * FROM peminjaman WHERE status='dikembalikan'"));

$judul_halaman = "Laporan Peminjaman - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>📊 Laporan Peminjaman</h1>
    <p>Rekap dan monitoring seluruh aktivitas peminjaman buku.</p>
</div>

<div class="container fade-in">

    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
                <h3><?php echo $total_semua; ?></h3>
                <p>Total Transaksi</p>
            </div>
        </div>
        <div class="stat-card merah">
            <div class="stat-icon">📤</div>
            <div class="stat-info">
                <h3><?php echo $total_pinjam; ?></h3>
                <p>Sedang Dipinjam</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <h3><?php echo $total_kembali; ?></h3>
                <p>Dikembalikan</p>
            </div>
        </div>
    </div>

    <div class="card mb-20">
        <div class="card-body">
            <form method="GET" action="laporan.php">
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Filter Status</label>
                        <select name="status">
                            <option value="">— Semua Status —</option>
                            <option value="dipinjam"   <?php if($filter_status=='dipinjam')   echo 'selected'; ?>>📤 Sedang Dipinjam</option>
                            <option value="dikembalikan" <?php if($filter_status=='dikembalikan') echo 'selected'; ?>>✅ Dikembalikan</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Filter Peminjam</label>
                        <select name="user">
                            <option value="">— Semua Peminjam —</option>
                            <?php while ($u = mysqli_fetch_array($query_user_list)) { ?>
                                <option value="<?php echo $u['id']; ?>" <?php if ($filter_user == $u['id']) echo 'selected'; ?> >
                                    <?php echo $u['nama']; ?>
                                </option>
                            <?php } ?>
                            
                        </select>
                    </div>
                </div>
                <div class="mt-20" style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                    <a href="laporan.php" class="btn btn-outline">✖ Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header flex-between" style="display:flex; align-items:center; justify-content:space-between;">
            <span>📋 Data Peminjaman</span>
            <span style="font-size:0.85rem; font-weight:400;">
                Menampilkan: <?php echo mysqli_num_rows($query_laporan); ?> data
            </span>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
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

                while ($data = mysqli_fetch_array($query_laporan)) {
                    $ada = true;

                    if ($data['status'] == 'dipinjam') {
                        $badge = '<span class="badge badge-merah">📤 Dipinjam</span>';
                    } else {
                        $badge = '<span class="badge badge-hijau">✅ Kembali</span>';
                    }
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <div style="font-weight:500;"><?php echo $data['nama_user']; ?></div>
                            <div style="font-size:0.75rem; color:var(--teks-abu);">@<?php echo $data['username']; ?></div>
                        </td>
                        <td style="font-weight:500;"><?php echo $data['judul_buku']; ?></td>
                        <td><?php echo $data['pengarang']; ?></td>
                        <td><span class="buku-kategori"><?php echo $data['kategori']; ?></span></td>
                        <td><?php echo $data['tgl_pinjam']; ?></td>
                        <td><?php echo $data['tgl_kembali']; ?></td>
                        <td><?php echo $badge; ?></td>
                        <td>
                            <?php if ($data['status'] == 'dipinjam') { ?>
                                <a href="kembalikan.php?id=<?php echo $data['id_pinjam']; ?>"
                                   onclick="return confirm('Konfirmasi pengembalian buku ini?')"
                                   class="btn btn-sm btn-primary">↩️ Proses Kembali</a>
                            <?php } else { ?>
                                <span style="color:var(--teks-abu); font-size:0.8rem;">—</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (!$ada) { ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <p>Tidak ada data peminjaman ditemukan.</p>
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