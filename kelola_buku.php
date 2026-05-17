<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

$pesan = "";
$tipe  = "";
$mode  = isset($_GET['edit']) ? 'edit' : 'tambah';
$id_edit = isset($_GET['edit']) ? $_GET['edit'] : '';
$upload_dir = 'uploads/covers/';

function upload_cover($file, $upload_dir) {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }

    if (!in_array($file['type'], $allowed)) {
        return false;
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('cover_', true) . '.' . strtolower($extension);
    $path = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return $path;
    }

    return false;
}

if (isset($_POST['submit_tambah'])) {
    $judul        = $_POST['judul'];
    $pengarang    = $_POST['pengarang'];
    $penerbit     = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $kategori     = $_POST['kategori'];
    $stok         = $_POST['stok'];
    $cover        = null;

    if (isset($_FILES['cover'])) {
        $upload = upload_cover($_FILES['cover'], $upload_dir);
        if ($upload === false) {
            $pesan = "Gagal mengunggah cover. Pastikan file JPG, PNG, atau GIF dan ukuran maksimal 2MB.";
            $tipe  = "danger";
        } else {
            $cover = $upload;
        }
    }

    if ($pesan === "") {
        $query = mysqli_query($konek,
            "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, kategori, stok, cover)
             VALUES ('$judul', '$pengarang', '$penerbit', '$tahun_terbit', '$kategori', '$stok', '$cover')"
        );

        if ($query) {
            $pesan = "Buku \"$judul\" berhasil ditambahkan!";
            $tipe  = "success";
        } else {
            $pesan = "Gagal menambahkan buku!";
            $tipe  = "danger";
            if ($cover && file_exists($cover)) {
                unlink($cover);
            }
        }
    }
}

if (isset($_POST['submit_edit'])) {
    $id_buku      = $_POST['id_buku'];
    $judul        = $_POST['judul'];
    $pengarang    = $_POST['pengarang'];
    $penerbit     = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $kategori     = $_POST['kategori'];
    $stok         = $_POST['stok'];
    $cover        = isset($_POST['old_cover']) ? $_POST['old_cover'] : null;

    if (isset($_FILES['cover'])) {
        $upload = upload_cover($_FILES['cover'], $upload_dir);
        if ($upload === false) {
            $pesan = "Gagal mengunggah cover. Pastikan file JPG, PNG, atau GIF dan ukuran maksimal 2MB.";
            $tipe  = "danger";
        } elseif ($upload !== null) {
            if ($cover && file_exists($cover)) {
                unlink($cover);
            }
            $cover = $upload;
        }
    }

    if ($pesan === "") {
        $query = mysqli_query($konek,
            "UPDATE buku SET
                judul='$judul',
                pengarang='$pengarang',
                penerbit='$penerbit',
                tahun_terbit='$tahun_terbit',
                kategori='$kategori',
                stok='$stok',
                cover='$cover'
             WHERE id_buku='$id_buku'"
        );

        if ($query) {
            $pesan = "Buku berhasil diperbarui!";
            $tipe  = "success";
            $mode  = 'tambah';
            $id_edit = '';
        } else {
            $pesan = "Gagal memperbarui buku!";
            $tipe  = "danger";
        }
    }
}

$data_edit = null;
if ($id_edit != '') {
    $q_edit   = mysqli_query($konek, "SELECT * FROM buku WHERE id_buku='$id_edit'");
    $data_edit = mysqli_fetch_array($q_edit);
}

$query_buku = mysqli_query($konek, "SELECT * FROM buku ORDER BY judul ASC");

$kategori_tersedia = array('Teknologi', 'Ilmu Komputer', 'Matematika', 'Sastra', 'Novel', 'Sejarah', 'Sains', 'Lainnya');

$judul_halaman = "Kelola Buku - Perpustakaan Digital";
include 'header.php';
?>

<div class="page-hero">
    <h1>⚙️ Kelola Buku</h1>
    <p>Tambah, edit, dan hapus koleksi buku perpustakaan.</p>
</div>

<div class="container fade-in">

    <?php if ($pesan != "") { ?>
        <div class="alert alert-<?php echo $tipe; ?>">
            <?php echo ($tipe == 'success') ? '✅' : '⚠️'; ?> <?php echo $pesan; ?>
        </div>
    <?php } ?>

    <div style="display:grid; grid-template-columns: 380px 1fr; gap: 24px; align-items: start;">

        <div class="card" style="position:sticky; top:90px;">
            <div class="card-header">
                <?php echo ($mode == 'edit') ? '✏️ Edit Buku' : '➕ Tambah Buku Baru'; ?>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" action="kelola_buku.php<?php echo ($id_edit) ? '?edit='.$id_edit : ''; ?>">
                    <?php if ($mode == 'edit') { ?>
                        <input type="hidden" name="id_buku" value="<?php echo $data_edit['id_buku']; ?>">
                        <input type="hidden" name="old_cover" value="<?php echo $data_edit['cover']; ?>">
                    <?php } ?>

                    <div class="form-group">
                        <label>Judul Buku *</label>
                        <input type="text" name="judul"
                               value="<?php echo ($data_edit) ? $data_edit['judul'] : ''; ?>"
                               placeholder="Judul buku" required>
                    </div>

                    <div class="form-group">
                        <label>Pengarang *</label>
                        <input type="text" name="pengarang"
                               value="<?php echo ($data_edit) ? $data_edit['pengarang'] : ''; ?>"
                               placeholder="Nama pengarang" required>
                    </div>

                    <div class="form-group">
                        <label>Penerbit *</label>
                        <input type="text" name="penerbit"
                               value="<?php echo ($data_edit) ? $data_edit['penerbit'] : ''; ?>"
                               placeholder="Nama penerbit" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tahun Terbit *</label>
                            <input type="number" name="tahun_terbit" min="1900" max="2099"
                                   value="<?php echo ($data_edit) ? $data_edit['tahun_terbit'] : date('Y'); ?>"
                                   required>
                        </div>
                        <div class="form-group">
                            <label>Stok *</label>
                            <input type="number" name="stok" min="0"
                                   value="<?php echo ($data_edit) ? $data_edit['stok'] : '1'; ?>"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Upload Foto Buku</label>
                        <input type="file" name="cover" accept="image/jpeg,image/png,image/gif">
                        <small>Max 2MB. Format: JPG, JPEG, PNG, GIF.</small>
                    </div>

                    <div class="form-group">
                        <label>Kategori *</label>
                        <select name="kategori" required>
                            <option value="">— Pilih Kategori —</option>
                            <?php
                            for ($i = 0; $i < count($kategori_tersedia); $i++) {
                                $kat = $kategori_tersedia[$i];
                                $current = $data_edit ? $data_edit['kategori'] : '';
                                $sel = ($current == $kat) ? 'selected' : '';
                                echo "<option value='$kat' $sel>$kat</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <?php if ($mode == 'edit') { ?>
                            <button type="submit" name="submit_edit" class="btn btn-warning" style="flex:1; justify-content:center;">
                                💾 Simpan Perubahan
                            </button>
                            <a href="kelola_buku.php" class="btn btn-outline">✖ Batal</a>
                        <?php } else { ?>
                            <button type="submit" name="submit_tambah" class="btn btn-primary" style="flex:1; justify-content:center;">
                                ➕ Tambah Buku
                            </button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header flex-between" style="display:flex; align-items:center; justify-content:space-between;">
                <span>📚 Daftar Koleksi Buku</span>
                <span style="font-size:0.85rem; font-weight:400;">
                    Total: <?php echo mysqli_num_rows($query_buku); ?> buku
                </span>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no = 1;
                    $ada = false;

                    while ($buku = mysqli_fetch_array($query_buku)) {
                        $ada = true;

                        if ($buku['stok'] > 3) {
                            $stok_badge = 'badge-hijau';
                        } elseif ($buku['stok'] > 0) {
                            $stok_badge = 'badge-kuning';
                        } else {
                            $stok_badge = 'badge-merah';
                        }
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            
                            <td style="font-weight:500; max-width:260px;"><?php echo $buku['judul']; ?></td>
                            <td><?php echo $buku['pengarang']; ?></td>
                            <td><span class="buku-kategori"><?php echo $buku['kategori']; ?></span></td>
                            <td><?php echo $buku['tahun_terbit']; ?></td>
                            <td>
                                <span class="badge <?php echo $stok_badge; ?>"><?php echo $buku['stok']; ?></span>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="kelola_buku.php?edit=<?php echo $buku['id_buku']; ?>"
                                   class="btn btn-sm btn-warning">✏️ Edit</a>
                                <a href="hapus_buku.php?id=<?php echo $buku['id_buku']; ?>"
                                   onclick="return confirm('Hapus buku \"<?php echo $buku['judul']; ?>\"?\nData yang sudah dihapus tidak bisa dikembalikan.')"
                                   class="btn btn-sm btn-danger">🗑️</a>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if (!$ada) { ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">📚</div>
                                    <p>Belum ada buku di perpustakaan.</p>
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
</div>

<?php include 'footer.php'; ?>
