<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama = $_SESSION['nama_user'];

// Hitung total data otomatis dari database untuk card
$jml_siswa = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM t_siswa"))[0];
$jml_guru  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM t_guru"))[0];
$jml_kelas = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM t_kelas"))[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistem Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-hover { transition: transform 0.2s; }
        .card-hover:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1"><a href="dashboard.php" class="active">Dashboard</a></li>
            <?php if ($role === 'admin'): ?>
                <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
                <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
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
                <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
            <?php endif; ?>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="p-4 mb-4 bg-white rounded shadow-sm border-start border-primary border-4">
            <h2>Selamat Datang, <?= htmlspecialchars($nama); ?>! 👋</h2>
            <p class="text-muted mb-0">Anda masuk sebagai akses: <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($role); ?></span></p>
        </div>

        <?php if ($role === 'admin'): ?>
        <div class="row">
            <!-- CARD DATA SISWA -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 card-hover bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Siswa</h5>
                        <h2 class="display-6 fw-bold"><?= $jml_siswa; ?></h2>
                        <a href="kelola_siswa.php" class="text-white text-decoration-none small stretched-link">Kelola Data Siswa &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- CARD DATA GURU -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 card-hover bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Guru</h5>
                        <h2 class="display-6 fw-bold"><?= $jml_guru; ?></h2>
                        <a href="kelola_guru.php" class="text-white text-decoration-none small stretched-link">Kelola Data Guru &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- CARD KELAS -->
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm border-0 card-hover bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Total Kelas</h5>
                        <h2 class="display-6 fw-bold"><?= $jml_kelas; ?></h2>
                        <a href="kelola_kelas.php" class="text-dark text-decoration-none small stretched-link">Kelola Data Kelas &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info shadow-sm">
            Silakan pilih menu di sebelah kiri untuk mulai mencatat pelanggaran atau melihat riwayat poin siswa.
        </div>
        <?php endif; ?>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>