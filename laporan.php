<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_user'] ?? 'Administrator';

$tgl_mulai = $_GET['tgl_mulai'] ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';
$cari = $_GET['cari'] ?? '';

$where_arr = [];
if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
    $where_arr[] = "tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'";
}
if (!empty($cari)) {
    $where_arr[] = "(nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%' OR nama_guru LIKE '%$cari%')";
}

$where = count($where_arr) > 0 ? "WHERE " . implode(" AND ", $where_arr) : "";

// Konfigurasi Pagination (10 data per halaman)
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

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
                    <?php if (!empty($cari)): ?>
                        <input type="hidden" name="cari" value="<?= htmlspecialchars($cari); ?>">
                    <?php endif; ?>
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
                <!-- BARIS FILTER & PENCARIAN -->
                <div class="row mb-3 justify-content-between no-print">
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
                            <?php if (!empty($tgl_mulai)): ?><input type="hidden" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai); ?>"><?php endif; ?>
                            <?php if (!empty($tgl_selesai)): ?><input type="hidden" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai); ?>"><?php endif; ?>
                            <label class="me-2 mb-0 fw-bold">Cari:</label>
                            <input type="text" name="cari" class="form-control form-control-sm" value="<?= htmlspecialchars($cari); ?>">
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
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
                        <?php while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td class="text-center"><?= $nomor++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($r['tanggal']); ?></td>
                            <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                            <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                            <td class="text-center"><span class="badge bg-danger"><?= htmlspecialchars($r['poin']); ?></span></td>
                            <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                            <td class="text-center"><span class="badge bg-info text-dark"><?= htmlspecialchars($r['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($total_data == 0): ?>
                        <tr><td colspan="8" class="text-center text-muted">Data laporan pelanggaran tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- KETERANGAN & PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3 no-print">
                    <small class="text-muted">Menampilkan <?= $tampilkan_dari; ?> sampai <?= $tampilkan_sampai; ?> dari <?= $total_data; ?> data laporan</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Tombol Pertama -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=1<?= $tgl_mulai ? '&tgl_mulai='.$tgl_mulai : ''; ?><?= $tgl_selesai ? '&tgl_selesai='.$tgl_selesai : ''; ?><?= $cari ? '&cari='.$cari : ''; ?>">Pertama</a>
                            </li>
                            <!-- Tombol Sebelumnya -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= $halaman - 1; ?><?= $tgl_mulai ? '&tgl_mulai='.$tgl_mulai : ''; ?><?= $tgl_selesai ? '&tgl_selesai='.$tgl_selesai : ''; ?><?= $cari ? '&cari='.$cari : ''; ?>">Sebelumnya</a>
                            </li>
                            
                            <!-- Nomor Halaman -->
                            <?php for($x = 1; $x <= max($total_halaman, 1); $x++): ?>
                                <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?halaman=<?= $x; ?><?= $tgl_mulai ? '&tgl_mulai='.$tgl_mulai : ''; ?><?= $tgl_selesai ? '&tgl_selesai='.$tgl_selesai : ''; ?><?= $cari ? '&cari='.$cari : ''; ?>"><?= $x; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Tombol Berikutnya -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= $halaman + 1; ?><?= $tgl_mulai ? '&tgl_mulai='.$tgl_mulai : ''; ?><?= $tgl_selesai ? '&tgl_selesai='.$tgl_selesai : ''; ?><?= $cari ? '&cari='.$cari : ''; ?>">Berikutnya</a>
                            </li>
                            <!-- Tombol Terakhir -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= max($total_halaman, 1); ?><?= $tgl_mulai ? '&tgl_mulai='.$tgl_mulai : ''; ?><?= $tgl_selesai ? '&tgl_selesai='.$tgl_selesai : ''; ?><?= $cari ? '&cari='.$cari : ''; ?>">Terakhir</a>
                            </li>
                        </ul>
                    </nav>
                </div>
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