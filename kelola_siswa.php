<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_user'];

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_siswa SET nis='$nis', nisn='$nisn', nama='$nama', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', alamat='$alamat' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif) VALUES ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', 1)");
    }
    header("Location: kelola_siswa.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_siswa.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id='{$_GET['edit']}'")) : ['id'=>'','nis'=>'','nisn'=>'','nama'=>'','jenis_kelamin'=>'','tanggal_lahir'=>'','alamat'=>''];
$data = mysqli_query($koneksi, "SELECT * FROM t_siswa");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>
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

    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1"><a href="dashboard.php">Dashboard</a></li>
            <?php if ($role === 'admin'): ?>
                <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
                <li class="nav-item mb-1"><a href="kelola_siswa.php" class="active">Kelola Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
                <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
            <?php endif; ?>

            <?php if ($role === 'guru'): ?>
                <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
                <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
                <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <?php endif; ?>
        </ul>
        <hr>
        <a href="logout.php" class="text-danger">Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <h2>Kelola Siswa</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <!-- FORM INPUT / EDIT -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $edit['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">NIS:</label>
                        <input type="text" name="nis" class="form-control" value="<?= $edit['nis']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NISN:</label>
                        <input type="text" name="nisn" class="form-control" value="<?= $edit['nisn']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Siswa:</label>
                        <input type="text" name="nama" class="form-control" value="<?= $edit['nama']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin (L/P):</label>
                        <input type="text" name="jenis_kelamin" class="form-control" value="<?= $edit['jenis_kelamin']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir:</label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="<?= $edit['tanggal_lahir']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat:</label>
                        <textarea name="alamat" class="form-control" rows="2" required><?= $edit['alamat']; ?></textarea>
                    </div>
                    
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <?php if($edit['id']): ?><a href="kelola_siswa.php" class="btn btn-secondary">Batal</a><?php endif; ?>
                </form>
            </div>
        </div>

        <!-- TABEL DATA -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>L/P</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $r['nis']; ?></td>
                            <td><?= $r['nisn']; ?></td>
                            <td><?= $r['nama']; ?></td>
                            <td><?= $r['jenis_kelamin']; ?></td>
                            <td><?= $r['tanggal_lahir']; ?></td>
                            <td><?= $r['alamat']; ?></td>
                            <td class="text-center">
                                <a href="kelola_siswa.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="kelola_siswa.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>