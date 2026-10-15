<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_user'] ?? 'Administrator';

$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';

$where = "";
if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
    $where = "WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'";
}

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Laporan Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #0d6efd; }
        .main-content { margin-left: 250px; padding: 20px; }

        /* CSS KHUSUS UNTUK CETAK / PRINT */
        @media print {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            .no-print { display: none !important; }
            body { background-color: #fff !important; }
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column p-3 no-print">
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
                <li class="nav-item mb-1"><a href="laporan.php" class="active">laporan</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
            <?php endif; ?>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="main-content">
        <h2>Laporan Pelanggaran Siswa</h2>
        <div class="no-print">
            <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>
        </div>

        <!-- FILTER TANGGAL & TOMBOL CETAK (TIDAK IKUT DICETAK) -->
        <div class="card shadow-sm mb-4 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai:</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="<?= htmlspecialchars($tgl_mulai); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai:</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="<?= htmlspecialchars($tgl_selesai); ?>" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="laporan.php" class="btn btn-secondary me-2">Reset</a>
                        <button type="button" onclick="window.print()" class="btn btn-success">Cetak / Print Laporan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- HEADER LAPORAN KHUSUS CETAK (MUNCUL SAAT DIPRINT) -->
        <div class="text-center mb-4 d-none d-print-block">
            <h2>LAPORAN DATA PELANGGARAN SISWA</h2>
            <h4>SMK MUHAMMADIYAH</h4>
            <?php if (!empty($tgl_mulai) && !empty($tgl_selesai)): ?>
                <p class="mb-0">Periode: <strong><?= $tgl_mulai; ?></strong> s/d <strong><?= $tgl_selesai; ?></strong></p>
            <?php endif; ?>
            <hr class="border-2 border-dark">
        </div>

        <!-- TABEL LAPORAN -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Guru Pelapor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= $r['tanggal']; ?></td>
                            <td><?= $r['nama_siswa']; ?></td>
                            <td><?= $r['nama_kelas']; ?></td>
                            <td><?= $r['nama_pelanggaran']; ?></td>
                            <td class="text-center"><span class="badge bg-danger"><?= $r['poin']; ?></span></td>
                            <td><?= $r['nama_guru']; ?></td>
                            <td class="text-center"><span class="badge bg-info text-dark"><?= $r['status']; ?></span></td>
                        </tr>
                        <?php endwhile; if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="8" class="text-center">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TANDA TANGAN (HANYA MUNCUL SAAT DIPRINT) -->
        <div class="row mt-5 text-center d-none d-print-flex">
            <div class="col-8"></div>
            <div class="col-4">
                <p>Guru Piket / Petugas,</p>
                <br><br><br>
                <p><strong>( ______________________ )</strong></p>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>