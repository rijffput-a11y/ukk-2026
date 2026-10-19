<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$role = $_SESSION['role'] ?? 'guru';

// Konfigurasi Pagination (10 data per halaman)
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = $cari ? "WHERE nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%' OR nama_guru LIKE '%$cari%' OR status LIKE '%$cari%'" : "";

// Hitung total data & total halaman
$query_jumlah = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where");
$total_data = mysqli_num_rows($query_jumlah);
$total_halaman = ceil($total_data / $batas);

// Ambil data sesuai halaman
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where ORDER BY tanggal DESC LIMIT $halaman_awal, $batas");
$nomor = $halaman_awal + 1;
$jumlah_tampil = mysqli_num_rows($data);
$tampilkan_dari = $total_data > 0 ? $halaman_awal + 1 : 0;
$tampilkan_sampai = $halaman_awal + $jumlah_tampil;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Riwayat Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 20px; }
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
                <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <?php endif; ?>
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php" class="active">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Riwayat Pelanggaran Siswa</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- BARIS FILTER & PENCARIAN -->
                <div class="row mb-3 justify-content-between">
                    <div class="col-auto d-flex align-items-center">
                        <span class="me-2">Tampilkan</span>
                        <select class="form-select form-select-sm d-inline-block w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="ms-2">data</span>
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex align-items-center">
                            <label class="me-2 mb-0 fw-bold">Cari:</label>
                            <input type="text" name="cari" class="form-control form-control-sm" value="<?= htmlspecialchars($cari); ?>">
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Guru Pelapor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td class="text-center"><?= $nomor++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($r['tanggal']); ?></td>
                            <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                            <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                            <td class="text-center"><strong><?= htmlspecialchars($r['poin']); ?></strong></td>
                            <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= ($r['status'] == 'Selesai') ? 'success' : (($r['status'] == 'Diproses') ? 'warning' : 'secondary'); ?>">
                                    <?= htmlspecialchars($r['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($total_data == 0): ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada riwayat pelanggaran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- KETERANGAN & PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Menampilkan <?= $tampilkan_dari; ?> sampai <?= $tampilkan_sampai; ?> dari <?= $total_data; ?> data riwayat</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Tombol Pertama -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=1<?= $cari ? '&cari='.$cari : ''; ?>">Pertama</a>
                            </li>
                            <!-- Tombol Sebelumnya -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= $halaman - 1; ?><?= $cari ? '&cari='.$cari : ''; ?>">Sebelumnya</a>
                            </li>
                            
                            <!-- Nomor Halaman -->
                            <?php for($x = 1; $x <= max($total_halaman, 1); $x++): ?>
                                <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?halaman=<?= $x; ?><?= $cari ? '&cari='.$cari : ''; ?>"><?= $x; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Tombol Berikutnya -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= $halaman + 1; ?><?= $cari ? '&cari='.$cari : ''; ?>">Berikutnya</a>
                            </li>
                            <!-- Tombol Terakhir -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= max($total_halaman, 1); ?><?= $cari ? '&cari='.$cari : ''; ?>">Terakhir</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>