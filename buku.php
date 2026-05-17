<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['username'])) {
    header('Location: index.php?pesan=belum_login');
    exit();
}

$cari  = isset($_GET['cari'])  ? $_GET['cari']  : '';
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$where = "WHERE 1=1";
if ($cari != '') {
    $where .= " AND (judul LIKE '%$cari%' OR pengarang LIKE '%$cari%' OR penerbit LIKE '%$cari%')";
}
if ($kategori_filter != '') {
    $where .= " AND kategori='$kategori_filter'";
}

$query_buku = mysqli_query($konek, "SELECT * FROM buku $where ORDER BY judul ASC");
$total_hasil = mysqli_num_rows($query_buku);

$query_kat = mysqli_query($konek, "SELECT DISTINCT kategori FROM buku ORDER BY kategori");
$kategori_list = array();
while ($k = mysqli_fetch_array($query_kat)) {
    $kategori_list[] = $k['kategori'];
}

$judul_halaman = "Koleksi Buku - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>📚 Koleksi Buku</h1>
    <p>Temukan buku yang kamu cari dari koleksi lengkap perpustakaan kami.</p>
</div>

<div class="container fade-in">

    <div class="card mb-20">
        <div class="card-body">
            <form method="GET" action="buku.php">
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Cari Buku</label>
                        <input type="text" name="cari" value="<?php echo $cari; ?>"
                               placeholder="Judul, pengarang, atau penerbit...">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Filter Kategori</label>
                        <select name="kategori">
                            <option value="">— Semua Kategori —</option>
                            <?php
                            for ($i = 0; $i < count($kategori_list); $i++) {
                                $kat = $kategori_list[$i];
                                $sel = ($kategori_filter == $kat) ? 'selected' : '';
                                echo "<option value='$kat' $sel>$kat</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mt-20" style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary">🔍 Cari</button>
                    <a href="buku.php" class="btn btn-outline">✖ Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if ($cari != '' || $kategori_filter != '') { ?>
        <div class="alert alert-info">
            🔍 Menampilkan <strong><?php echo $total_hasil; ?></strong> hasil
            <?php if ($cari) echo "untuk pencarian \"<strong>$cari</strong>\""; ?>
            <?php if ($kategori_filter) echo " pada kategori \"<strong>$kategori_filter</strong>\""; ?>
        </div>
    <?php } ?>

    <?php if ($total_hasil > 0) { ?>
        <div class="buku-grid">
        <?php
        $warna_cover = array('', 'buku-cover-coklat', 'buku-cover-biru', 'buku-cover-ungu', '');
        $emoji_buku  = array('📘', '📗', '📕', '📙', '📓');
        $i = 0;

        while ($buku = mysqli_fetch_array($query_buku)) {
            $cover_class = $warna_cover[$i % 5];
            $emoji = $emoji_buku[$i % 5];
            $i++;

            if ($buku['stok'] > 0) {
                $stok_info = "Stok: " . $buku['stok'];
                $stok_warna = "";
            } else {
                $stok_info = "Habis";
                $stok_warna = "background:rgba(192,57,43,0.8);";
            }
        ?>
            <div class="buku-card">
                <?php
                $cover_style = '';
                if (!empty($buku['cover'])) {
                    $url = $buku['cover'];
                    $cover_style = "background-image: url('$url'); background-size: cover; background-position: center; background-repeat: no-repeat;";
                }
                ?>
                <div class="buku-cover <?php echo $cover_class; ?>" style="<?php echo $cover_style; ?>">
                    <?php if (empty($buku['cover'])) { echo $emoji; } ?>
                    <span class="stok-badge" style="<?php echo $stok_warna; ?>"><?php echo $stok_info; ?></span>
                </div>
                <div class="buku-info">
                    <h4><?php echo $buku['judul']; ?></h4>
                    <p>✍️ <?php echo $buku['pengarang']; ?></p>
                    <p>🏢 <?php echo $buku['penerbit']; ?> (<?php echo $buku['tahun_terbit']; ?>)</p>
                    <span class="buku-kategori"><?php echo $buku['kategori']; ?></span>
                </div>
                <div class="buku-actions">
                    <?php if ($_SESSION['role'] == 'user') { ?>
                        <?php if ($buku['stok'] > 0) { ?>
                            <a href="pinjam.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-sm btn-primary" style="flex:1; justify-content:center;">📤 Pinjam</a>
                        <?php } else { ?>
                            <span class="btn btn-sm" style="flex:1; justify-content:center; background:var(--krem); color:var(--teks-abu);">Tidak Tersedia</span>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <a href="kelola_buku.php?edit=<?php echo $buku['id_buku']; ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <a href="hapus_buku.php?id=<?php echo $buku['id_buku']; ?>"
                           onclick="return confirm('Hapus buku ini?')"
                           class="btn btn-sm btn-danger">🗑️</a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        </div>
    <?php } else { ?>
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">🔍</div>
                    <p>Tidak ada buku yang ditemukan.</p>
                    <?php if ($cari || $kategori_filter) { ?>
                        <a href="buku.php" class="btn btn-outline mt-20">Lihat Semua Buku</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php include 'footer.php'; ?>