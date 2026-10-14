<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama']; 
    $deskripsi = $_POST['deskripsi']; 
    $status = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_pelanggaran_kategori SET nama='$nama', deskripsi='$deskripsi', status_aktif='$status' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_pelanggaran_kategori (nama, deskripsi, status_aktif) VALUES ('$nama', '$deskripsi', '$status')");
    }
    header("Location: kelola_kategori_pelanggaran.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_pelanggaran_kategori WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_kategori_pelanggaran.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE id='{$_GET['edit']}'")) : ['id'=>'','nama'=>'','deskripsi'=>'','status_aktif'=>'1'];
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Kategori Pelanggaran</title>
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
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php" class="active">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="cetak/export.php">Cetak / Export</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Kelola Kategori Pelanggaran</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm mb-4"><div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit['id']; ?>">
                <div class="mb-3"><label class="form-label">Nama Kategori:</label><input type="text" name="nama" class="form-control" value="<?= $edit['nama']; ?>" required></div>
                <div class="mb-3"><label class="form-label">Deskripsi:</label><textarea name="deskripsi" class="form-control" rows="2"><?= $edit['deskripsi']; ?></textarea></div>
                <div class="mb-3"><label class="form-label">Status Aktif:</label><input type="number" name="status_aktif" class="form-control" min="0" max="1" value="<?= $edit['status_aktif']; ?>" required></div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <?php if($edit['id']): ?><a href="kelola_kategori_pelanggaran.php" class="btn btn-secondary">Batal</a><?php endif; ?>
            </form>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark"><tr><th>No</th><th>Nama Kategori</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++; ?></td><td><?= $r['nama']; ?></td><td><?= $r['deskripsi']; ?></td><td><?= $r['status_aktif']; ?></td>
                        <td class="text-center">
                            <a href="kelola_kategori_pelanggaran.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="kelola_kategori_pelanggaran.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
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