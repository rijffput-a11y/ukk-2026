<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$nama_user = $_SESSION['nama_user'] ?? $_SESSION['name'] ?? '';
$is_wali_kelas = $_SESSION['is_wali_kelas'] ?? false;

if ($role === 'guru' && !$is_wali_kelas) {
    echo "<script>alert('Akses ditolak! Menu laporan ini hanya untuk Admin dan Wali Kelas.'); window.location='dashboard.php';</script>";
    exit();
}

if ($role === 'admin') {
    $q_lap = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa ORDER BY tanggal DESC");
    $kelas_binaan = '';
} else {
    $q_wk = mysqli_query($koneksi, "SELECT k.nama FROM t_wali_kelas tw JOIN t_kelas k ON tw.kelas_id = k.id JOIN t_guru g ON tw.guru_id = g.id WHERE (g.user_id = '$user_id' OR g.nama LIKE '%$nama_user%') AND tw.status_aktif = 1 LIMIT 1");
    $kelas_binaan = ($r_wk = mysqli_fetch_assoc($q_wk)) ? $r_wk['nama'] : '';
    $q_lap = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa WHERE nama_kelas = '$kelas_binaan' ORDER BY tanggal DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Laporan Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 30px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1"><a href="dashboard.php">Dashboard</a></li>
        <?php if ($role === 'admin'): ?>
            <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
            <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
            <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php" class="active">Laporan</a></li>
        <?php endif; ?>
        <?php if ($role === 'guru'): ?>
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <?php if ($is_wali_kelas): ?>
                <li class="nav-item mb-1"><a href="laporan.php" class="active">Laporan</a></li>
            <?php endif; ?>
        <?php endif; ?>
        <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
    </ul>
    <hr><a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Laporan Pelanggaran Siswa</h2>
        <span class="badge bg-secondary">Login: <?= htmlspecialchars($nama_user); ?> (<?= ucfirst($role); ?>)</span>
    </div>

    <?php if ($role !== 'admin' && !empty($kelas_binaan)): ?>
        <div class="alert alert-info">Menampilkan laporan khusus kelas binaan Anda: <strong><?= htmlspecialchars($kelas_binaan); ?></strong></div>
    <?php endif; ?>

    <div class="card shadow-sm"><div class="card-body">
        <h5 class="card-title mb-3">Rekapitulasi Data Pelanggaran</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr><th>No</th><th>Tanggal</th><th>Nama Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pencatat</th></tr>
                </thead>
                <tbody>
                    <?php if ($q_lap && mysqli_num_rows($q_lap) > 0): $no = 1; while ($r = mysqli_fetch_assoc($q_lap)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $r['tanggal']; ?></td>
                        <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                        <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                        <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                        <td><span class="badge bg-danger"><?= $r['poin']; ?></span></td>
                        <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="7" class="text-center text-muted">Belum ada data laporan pelanggaran.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>