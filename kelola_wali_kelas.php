<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $ta = $_POST['tahun_ajaran_id']; 
    $kls = $_POST['kelas_id']; 
    $gur = $_POST['guru_id'];
    $mulai = $_POST['tanggal_mulai']; 
    $selesai = $_POST['tanggal_selesai']; 
    $status = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_wali_kelas SET tahun_ajaran_id='$ta', kelas_id='$kls', guru_id='$gur', tanggal_mulai='$mulai', tanggal_selesai='$selesai', status_aktif='$status' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_wali_kelas (tahun_ajaran_id, kelas_id, guru_id, tanggal_mulai, tanggal_selesai, status_aktif) VALUES ('$ta', '$kls', '$gur', '$mulai', '$selesai', '$status')");
    }
    header("Location: kelola_wali_kelas.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_wali_kelas WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_wali_kelas.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_wali_kelas WHERE id='{$_GET['edit']}'")) : ['id'=>'','tahun_ajaran_id'=>'','kelas_id'=>'','guru_id'=>'','tanggal_mulai'=>'','tanggal_selesai'=>'','status_aktif'=>'1'];
$data = mysqli_query($koneksi, "SELECT tw.*, ta.nama AS n_ta, tk.nama AS n_kls, tg.nama AS n_gur FROM t_wali_kelas tw LEFT JOIN t_tahun_ajaran ta ON tw.tahun_ajaran_id=ta.id LEFT JOIN t_kelas tk ON tw.kelas_id=tk.id LEFT JOIN t_guru tg ON tw.guru_id=tg.id");

$opt_ta = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");
$opt_kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas");
$opt_guru = mysqli_query($koneksi, "SELECT * FROM t_guru");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Wali Kelas</title>
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
            <li class="nav-item mb-1"><a href="kelola_wali_kelas.php" class="active">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Kelola Wali Kelas</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm mb-4"><div class="card-body">
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit['id']; ?>">
                <div class="mb-3">
                    <label class="form-label">Tahun Ajaran:</label>
                    <select name="tahun_ajaran_id" class="form-control" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php while($ta = mysqli_fetch_assoc($opt_ta)): ?>
                            <option value="<?= $ta['id']; ?>" <?= ($edit['tahun_ajaran_id'] == $ta['id']) ? 'selected' : ''; ?>><?= $ta['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas:</label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($tk = mysqli_fetch_assoc($opt_kelas)): ?>
                            <option value="<?= $tk['id']; ?>" <?= ($edit['kelas_id'] == $tk['id']) ? 'selected' : ''; ?>><?= $tk['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Guru:</label>
                    <select name="guru_id" class="form-control" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php while($tg = mysqli_fetch_assoc($opt_guru)): ?>
                            <option value="<?= $tg['id']; ?>" <?= ($edit['guru_id'] == $tg['id']) ? 'selected' : ''; ?>><?= $tg['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3"><label class="form-label">Tanggal Mulai:</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $edit['tanggal_mulai']; ?>" required></div>
                <div class="mb-3"><label class="form-label">Tanggal Selesai:</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $edit['tanggal_selesai']; ?>" required></div>
                <div class="mb-3"><label class="form-label">Status Aktif:</label><input type="number" name="status_aktif" class="form-control" min="0" max="1" value="<?= $edit['status_aktif']; ?>" required></div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                <?php if($edit['id']): ?><a href="kelola_wali_kelas.php" class="btn btn-secondary">Batal</a><?php endif; ?>
            </form>
        </div></div>

        <div class="card shadow-sm"><div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark"><tr><th>No</th><th>Tahun</th><th>Kelas</th><th>Guru</th><th>Mulai</th><th>Selesai</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++; ?></td><td><?= $r['n_ta']; ?></td><td><?= $r['n_kls']; ?></td><td><?= $r['n_gur']; ?></td><td><?= $r['tanggal_mulai']; ?></td><td><?= $r['tanggal_selesai']; ?></td><td><?= $r['status_aktif']; ?></td>
                        <td class="text-center">
                            <a href="kelola_wali_kelas.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="kelola_wali_kelas.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
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