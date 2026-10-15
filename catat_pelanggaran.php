<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $s = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT s.nama AS n_sis, k.nama AS n_kls FROM t_siswa s LEFT JOIN t_kelas_siswa ks ON s.id=ks.siswa_id LEFT JOIN t_kelas k ON ks.kelas_id=k.id WHERE s.id='{$_POST['siswa_id']}'"));
    $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama, poin FROM t_pelanggaran WHERE id='{$_POST['pelanggaran_id']}'"));
    $g = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama FROM t_guru WHERE id='{$_SESSION['user_id']}'"));
    
    mysqli_query($koneksi, "INSERT INTO t_pelanggaran_siswa (tanggal, siswa_id, nama_siswa, nama_kelas, pelanggaran_id, nama_pelanggaran, poin, guru_id, nama_guru, status) VALUES ('{$_POST['tanggal']}', '{$_POST['siswa_id']}', '{$s['n_sis']}', '{$s['n_kls']}', '{$_POST['pelanggaran_id']}', '{$p['nama']}', '{$p['poin']}', '{$_SESSION['user_id']}', '{$g['nama']}', 'Pending')");
    header("Location: catat_pelanggaran.php"); exit();
}
$opt_s = mysqli_query($koneksi, "SELECT * FROM t_siswa");
$opt_p = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif='1'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Catat Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f8f9fa; } .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; } .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; } .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; } .main-content { margin-left: 250px; padding: 20px; }</style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1"><a href="dashboard.php">Dashboard</a></li>
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php" class="active">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>
    <div class="main-content">
        <h2>Catat Pelanggaran</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm"><div class="card-body">
            <form method="POST">
                <div class="mb-3"><label class="form-label">Tanggal:</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required></div>
                <div class="mb-3"><label class="form-label">Siswa:</label><select name="siswa_id" class="form-control" required><option value="">-- Pilih Siswa --</option><?php while($s = mysqli_fetch_assoc($opt_s)): ?><option value="<?= $s['id']; ?>"><?= $s['nis'].' - '.$s['nama']; ?></option><?php endwhile; ?></select></div>
                <div class="mb-3"><label class="form-label">Pelanggaran:</label><select name="pelanggaran_id" class="form-control" required><option value="">-- Pilih Pelanggaran --</option><?php while($p = mysqli_fetch_assoc($opt_p)): ?><option value="<?= $p['id']; ?>"><?= $p['nama'].' (Poin: '.$p['poin'].')'; ?></option><?php endwhile; ?></select></div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            </form>
        </div></div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>