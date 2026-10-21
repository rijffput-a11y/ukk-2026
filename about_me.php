<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$nama_user = $_SESSION['nama_user'] ?? 'Junita Putri Nur Rahman';
$role = $_SESSION['role'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>About Me - Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; z-index: 100; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 20px; }
        .profile-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid #0d6efd; }
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
                <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
                <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="about_me.php" class="active">About Me</a></li>
            <?php endif; ?>

            <?php if ($role === 'guru'): ?>
                <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
                <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
                <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
                <li class="nav-item mb-1"><a href="about_me.php" >About Me</a></li>
            <?php endif; ?>
        </ul>
        <hr>
        <a href="logout.php" class="text-danger">Logout</a>
    </div>
    
    <div class="main-content">
        <h2>About Me</h2>
        <p>Informasi profil pengembang dan pengelola sistem.</p>
        <hr>

        <div class="row">
            <!-- Kolom Kiri: Foto & Identitas Singkat -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm text-center p-4 h-100">
                    <div class="mb-3">
                        <!-- Menggunakan file potoku.webp -->
                        <img src="potoku.webp" alt="Foto Profil" class="profile-img mx-auto">
                    </div>
                    <h4>Junita Putri Nur Rahman</h4>
                    <p class="text-muted mb-1">XII RPL 2</p>
                    <div>
                        <span class="badge bg-primary">Software Engineering</span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail Profil / Tabel -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Profil Singkat</h4>
                        <table class="table table-borderless align-middle">
                            <tr>
                                <th style="width: 30%;">Nama Lengkap</th>
                                <td style="width: 70%;">: Junita Putri Nur Rahman</td>
                            </tr>
                            <tr>
                                <th>Kelas / Jurusan</th>
                                <td>: XII RPL 2 / Rekayasa Perangkat Lunak</td>
                            </tr>
                            <tr>
                                <th>Sekolah</th>
                               <td>: SMK Muhammadiyah</td>
                            </tr>
                            <tr>
                                <th>Fokus Keahlian</th>
                                <td>: Web Development (PHP, MySQL, Bootstrap 5) & Database Design</td>
                            </tr>
                            <tr>
                                <th>Tentang Proyek</th>
                                <td>: Sistem Informasi Pelanggaran Siswa ini dikembangkan untuk mempermudah pencatatan, pemantauan poin kedisiplinan, serta pengelolaan tindakan siswa secara terstruktur.</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>