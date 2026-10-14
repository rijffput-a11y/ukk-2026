<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $kategori_id = $_POST['pelanggaran_kategori_id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $poin = $_POST['poin'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_pelanggaran SET pelanggaran_kategori_id='$kategori_id', kode='$kode', nama='$nama', poin='$poin', deskripsi='$deskripsi', status_aktif='$status' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_pelanggaran (pelanggaran_kategori_id, kode, nama, poin, deskripsi, status_aktif) VALUES ('$kategori_id', '$kode', '$nama', '$poin', '$deskripsi', '$status')");
    }
    header("Location: kelola_jenis_pelanggaran.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_pelanggaran WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_jenis_pelanggaran.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE id='{$_GET['edit']}'")) : ['id'=>'','pelanggaran_kategori_id'=>'','kode'=>'','nama'=>'','poin'=>'','deskripsi'=>'','status_aktif'=>'1'];
$data = mysqli_query($koneksi, "SELECT tp.*, pk.nama AS nama_kategori FROM t_pelanggaran tp LEFT JOIN t_pelanggaran_kategori pk ON tp.pelanggaran_kategori_id = pk.id");
$opt_kategori = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Jenis Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #0d6efd; }
        .main-content { margin-left: 250px; padding: 20px; }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1"><a href="dashboard.php">Dashboard</a></li>
            <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
            <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
            <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php" class="active">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="cetak/export.php">Cetak / Export</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Kelola Jenis Pelanggaran</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>
        <div class="card shadow-sm mb-4"><div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Kategori Pelanggaran:</label>
                    <select name="pelanggaran_kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while($k = mysqli_fetch_assoc($opt_kategori)): ?>
                            <option value="<?= $k['id']; ?>" <?= ($edit['pelanggaran_kategori_id'] == $k['id']) ? 'selected' : ''; ?>><?= $k['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Kode:</label><input type="text" name="kode" class="form-control" value="<?= $edit['kode']; ?>" required></div>
                <div class="mb-3"><label class="form-label">Nama Pelanggaran:</label><input type="text" name="nama" class="form-control" value="<?= $edit['nama']; ?>" required></div>
                <div class="mb-3"><label class="form-label">Poin:</label><input type="number" name="poin" class="form-control" value="<?= $edit['poin']; ?>" required></div>
                <div class="mb-3"><label class="form-label">Deskripsi:</label><textarea name="deskripsi" class="form-control" rows="2"><?= $edit['deskripsi']; ?></textarea></div>
                <div class="mb-3"><label class="form-label">Status Aktif:</label><input type="number" name="status_aktif" class="form-control" min="0" max="1" value="<?= $edit['status_aktif']; ?>" required></div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <?php if($edit['id']): ?><a href="kelola_jenis_pelanggaran.php" class="btn btn-secondary">Batal</a><?php endif; ?>
            </form>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark"><tr><th>No</th><th>Kode</th><th>Nama Pelanggaran</th><th>Kategori</th><th>Poin</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++; ?></td><td><?= $r['kode']; ?></td><td><?= $r['nama']; ?></td><td><?= $r['nama_kategori']; ?></td><td><?= $r['poin']; ?></td><td><?= $r['status_aktif']; ?></td>
                        <td class="text-center">
                            <a href="kelola_jenis_pelanggaran.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="kelola_jenis_pelanggaran.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>