<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<div class="container-fluid">
    <div class="row">

    <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
    <h4 class="text-white mb-4">Pelanggaran Siswa</h4>

    <ul class="nav nav-pills flex-column">
        <!-- Menu yang selalu tampil untuk semua role -->
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link active">Dashboard</a>
        </li>

        <!-- Menu Khusus Admin -->
        <?php if ($role === 'admin'): ?>
            <li class="nav-item mb-2"><a href="kelola_siswa.php" class="nav-link text-white">Kelola Siswa</a></li>
            <li class="nav-item mb-2"><a href="kelola_guru.php" class="nav-link text-white">Kelola Guru</a></li>
            <li class="nav-item mb-2"><a href="kelola_kelas.php" class="nav-link text-white">Kelola Kelas</a></li>
            <li class="nav-item mb-2"><a href="kelola_tahun_ajaran.php" class="nav-link text-white">Kelola Tahun Ajaran</a></li>
            <li class="nav-item mb-2"><a href="penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a></li>
            <li class="nav-item mb-2"><a href="kelola_wali_kelas.php" class="nav-link text-white">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-2"><a href="kelola_kategori.php" class="nav-link text-white">Kelola Kategori</a></li>
            <li class="nav-item mb-2"><a href="kelola_jenis_pelanggaran.php" class="nav-link text-white">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-2"><a href="cetak_export.php" class="nav-link text-white">Cetak / Export</a></li>
            <li class="nav-item mb-2"><a href="about_me.php" class="nav-link text-white">About Me</a></li>
        <?php endif; ?>

        <!-- Menu untuk Guru & Admin -->
        <?php if ($role === 'guru' || $role === 'admin'): ?>
            <li class="nav-item mb-2"><a href="catat_pelanggaran.php" class="nav-link text-white">Catat Pelanggaran</a></li>
            <li class="nav-item mb-2"><a href="tindakan.php" class="nav-link text-white">Tindakan</a></li>
            <li class="nav-item mb-2"><a href="laporan.php" class="nav-link text-white">Laporan</a></li>
            <li class="nav-item mb-2"><a href="riwayat.php" class="nav-link text-white">Riwayat</a></li>
            <li class="nav-item mb-2"><a href="rekap_poin.php" class="nav-link text-white">Rekap Poin</a></li>
            <li class="nav-item mb-2"><a href="about_me.php" class="nav-link text-white">About Me</a></li>
        <?php endif; ?>

        <hr class="text-secondary">

        <li class="nav-item">
            <a href="logout.php" class="nav-link text-white">Logout</a>
        </li>
    </ul>
</div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2>Dashboard</h2>

            <p>Pengguna: <strong><?= htmlspecialchars($nama); ?></strong> | Akses: <strong><?= strtoupper(htmlspecialchars($role)); ?></strong></p>

            <div class="row">

                <!-- CARD DATA SISWA -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Siswa
                            </h5>

                            <h2>
                                120
                            </h2>

                        </div>

                    </div>
                </div>


                <!-- CARD DATA GURU -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Data Guru
                            </h5>

                            <h2>
                                25
                            </h2>

                        </div>

                    </div>
                </div>


                <!-- CARD KELAS -->
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                Kelas
                            </h5>

                            <h2>
                                12
                            </h2>

                        </div>

                    </div>
                </div>

            </div>

        </main>

    </div>
</div>
</body>
</html>