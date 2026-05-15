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
$pesan   = "";
$tipe    = "";

$id_buku_pilih = isset($_GET['id']) ? $_GET['id'] : '';

if (isset($_POST['submit_pinjam'])) {
    $id_buku    = $_POST['id_buku'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];

    $cek_stok = mysqli_query($konek, "SELECT * FROM buku WHERE id_buku='$id_buku' AND stok > 0");

    $cek_duplikat = mysqli_query($konek, "SELECT * FROM peminjaman WHERE id_user='$id_user' AND id_buku='$id_buku' AND status='dipinjam'");

    if (mysqli_num_rows($cek_duplikat) > 0) {
        $pesan = "Kamu sudah meminjam buku ini dan belum dikembalikan!";
        $tipe  = "danger";
    } elseif (mysqli_num_rows($cek_stok) == 0) {
        $pesan = "Maaf, stok buku ini sudah habis!";
        $tipe  = "danger";
    } else {
        $query_insert = mysqli_query($konek,
            "INSERT INTO peminjaman (id_user, id_buku, tgl_pinjam, tgl_kembali, status)
             VALUES ('$id_user', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'dipinjam')"
        );

        if ($query_insert) {
            mysqli_query($konek, "UPDATE buku SET stok = stok - 1 WHERE id_buku='$id_buku'");
            $pesan = "Berhasil meminjam buku! Jangan lupa kembalikan sebelum " . $tgl_kembali;
            $tipe  = "success";
        } else {
            $pesan = "Gagal meminjam buku. Coba lagi.";
            $tipe  = "danger";
        }
    }
}

$query_buku = mysqli_query($konek, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul ASC");

$buku_dipilih = null;
if ($id_buku_pilih != '') {
    $q = mysqli_query($konek, "SELECT * FROM buku WHERE id_buku='$id_buku_pilih'");
    $buku_dipilih = mysqli_fetch_array($q);
}

$judul_halaman = "Pinjam Buku - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>📤 Pinjam Buku</h1>
    <p>Pilih buku yang ingin kamu pinjam. Maksimal 14 hari peminjaman.</p>
</div>

<div class="container fade-in">

    <?php if ($pesan != "") { ?>
        <div class="alert alert-<?php echo $tipe; ?>">
            <?php echo ($tipe == 'success') ? '✅' : '⚠️'; ?> <?php echo $pesan; ?>
        </div>
    <?php } ?>

    <div style="display:grid; grid-template-columns: 1fr 340px; gap: 24px;">

        <div class="card">
            <div class="card-header">📋 Form Peminjaman Buku</div>
            <div class="card-body">
                <form method="POST" action="pinjam.php">
                    <div class="form-group">
                        <label>Pilih Buku</label>
                        <select name="id_buku" required>
                            <option value="">— Pilih Buku —</option>
                            <?php
                            while ($buku = mysqli_fetch_array($query_buku)) {
                                $selected = ($buku_dipilih && $buku['id_buku'] == $buku_dipilih['id_buku']) ? 'selected' : '';
                                echo "<option value='{$buku['id_buku']}' $selected>
                                        {$buku['judul']} — {$buku['pengarang']} (Stok: {$buku['stok']})
                                      </option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Pinjam</label>
                            <input type="date" name="tgl_pinjam"
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Kembali</label>
                            <input type="date" name="tgl_kembali"
                                   value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Peminjam</label>
                        <input type="text" value="<?php echo $_SESSION['nama']; ?>" disabled
                               style="background:var(--krem); color:var(--teks-abu);">
                    </div>

                    <button type="submit" name="submit_pinjam" class="btn btn-primary" style="width:100%; justify-content:center; padding:12px;">
                        📤 Pinjam Sekarang
                    </button>
                </form>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">📋 Sedang Saya Pinjam</div>
                <div class="card-body" style="padding:0;">
                    <?php
                    $q_aktif = mysqli_query($konek, "
                        SELECT p.*, b.judul, b.pengarang
                        FROM peminjaman p
                        JOIN buku b ON p.id_buku = b.id_buku
                        WHERE p.id_user='$id_user' AND p.status='dipinjam'
                    ");
                    $ada = false;

                    while ($dp = mysqli_fetch_array($q_aktif)) {
                        $ada = true;
                    ?>
                        <div style="padding:16px; border-bottom:1px solid var(--krem-gelap);">
                            <div style="font-weight:600; color:var(--hijau-tua); font-size:0.9rem;">
                                📕 <?php echo $dp['judul']; ?>
                            </div>
                            <div style="font-size:0.8rem; color:var(--teks-abu); margin-top:4px;">
                                ✍️ <?php echo $dp['pengarang']; ?>
                            </div>
                            <div style="font-size:0.8rem; color:var(--merah); margin-top:4px;">
                                ⏰ Kembali: <?php echo $dp['tgl_kembali']; ?>
                            </div>
                            <a href="kembalikan.php?id=<?php echo $dp['id_pinjam']; ?>"
                               onclick="return confirm('Kembalikan buku ini?')"
                               class="btn btn-sm btn-primary" style="margin-top:8px;">↩️ Kembalikan</a>
                        </div>
                    <?php } ?>

                    <?php if (!$ada) { ?>
                        <div class="empty-state" style="padding:30px;">
                            <div class="empty-icon">📭</div>
                            <p style="font-size:0.85rem;">Tidak ada buku yang dipinjam.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="card mt-20">
                <div class="card-body">
                    <h4 style="font-family:'Playfair Display',serif; color:var(--hijau-tua); margin-bottom:12px;">📌 Aturan Peminjaman</h4>
                    <ul style="font-size:0.85rem; color:var(--teks-abu); line-height:2; padding-left:16px;">
                        <li>Maksimal peminjaman <strong>14 hari</strong></li>
                        <li>Kembalikan tepat waktu</li>
                        <li>Jaga kondisi buku</li>
                        <li>Maksimal 3 buku sekaligus</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>