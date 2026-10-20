<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$t1 = $_GET['tgl_mulai'] ?? '';
$t2 = $_GET['tgl_selesai'] ?? '';
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');

$where_arr = [];
if ($t1 && $t2) $where_arr[] = "tanggal BETWEEN '$t1' AND '$t2'";
if ($cari) $where_arr[] = "(nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%' OR nama_guru LIKE '%$cari%')";
$where = $where_arr ? "WHERE " . implode(" AND ", $where_arr) : "";

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;

$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa $where"));
$pages = ceil($total / $limit);
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where ORDER BY tanggal DESC LIMIT $offset, $limit");
$no = $offset + 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Laporan Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 20px; }
        @media print {
            .sidebar, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body>
    <div class="sidebar d-flex flex-column p-3 no-print">
        <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-1"><a href="dashboard.php">Dashboard</a></li>
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
            </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Laporan Pelanggaran Siswa</h2>
        <div class="no-print"><a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr></div>

        <div class="card shadow-sm mb-4 no-print"><div class="card-body">
            <form method="GET" class="row g-3">
                <?php if ($cari): ?><input type="hidden" name="cari" value="<?= htmlspecialchars($cari); ?>"><?php endif; ?>
                <div class="col-md-3"><label class="form-label">Tanggal Mulai:</label><input type="date" name="tgl_mulai" class="form-control" value="<?= htmlspecialchars($t1); ?>" required></div>
                <div class="col-md-3"><label class="form-label">Tanggal Selesai:</label><input type="date" name="tgl_selesai" class="form-control" value="<?= htmlspecialchars($t2); ?>" required></div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="laporan.php" class="btn btn-secondary me-2">Reset</a>
                    <button type="button" onclick="window.print()" class="btn btn-success">Cetak / Print</button>
                </div>
            </form>
        </div></div>

        <div class="text-center mb-4 d-none d-print-block">
            <h2>LAPORAN DATA PELANGGARAN SISWA</h2>
            <h4>SMK MUHAMMADIYAH</h4>
            <?php if ($t1 && $t2): ?><p class="mb-0">Periode: <strong><?= $t1; ?></strong> s/d <strong><?= $t2; ?></strong></p><?php endif; ?>
            <hr class="border-2 border-dark">
        </div>

        <div class="card shadow-sm"><div class="card-body">
            <div class="row mb-3 justify-content-between no-print">
                <div class="col-auto">Tampilkan <select class="form-select form-select-sm d-inline-block w-auto"><option>10</option></select> data</div>
                <div class="col-auto">
                    <form method="GET" class="d-flex align-items-center">
                        <?php if ($t1): ?><input type="hidden" name="tgl_mulai" value="<?= htmlspecialchars($t1); ?>"><?php endif; ?>
                        <?php if ($t2): ?><input type="hidden" name="tgl_selesai" value="<?= htmlspecialchars($t2); ?>"><?php endif; ?>
                        <label class="me-2 mb-0 fw-bold">Cari:</label>
                        <input type="text" name="cari" class="form-control form-control-sm" value="<?= htmlspecialchars($cari); ?>">
                    </form>
                </div>
            </div>

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pelapor</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php while($r = mysqli_fetch_assoc($data)): ?>
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
                    <?php endwhile; if(!$total): ?>
                    <tr><td colspan="8" class="text-center text-muted">Data laporan tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3 no-print">
                <small class="text-muted">Menampilkan <?= $total ? $offset + 1 : 0; ?> - <?= min($offset + $limit, $total); ?> dari <?= $total; ?> data</small>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=1&tgl_mulai=<?= $t1; ?>&tgl_selesai=<?= $t2; ?>&cari=<?= $cari; ?>">Pertama</a></li>
                    <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h-1; ?>&tgl_mulai=<?= $t1; ?>&tgl_selesai=<?= $t2; ?>&cari=<?= $cari; ?>">Sebelumnya</a></li>
                    <?php for($i=1; $i<=$pages; $i++): ?>
                        <li class="page-item <?= $h == $i ? 'active' : ''; ?>"><a class="page-link" href="?halaman=<?= $i; ?>&tgl_mulai=<?= $t1; ?>&tgl_selesai=<?= $t2; ?>&cari=<?= $cari; ?>"><?= $i; ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h+1; ?>&tgl_mulai=<?= $t1; ?>&tgl_selesai=<?= $t2; ?>&cari=<?= $cari; ?>">Berikutnya</a></li>
                    <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $pages; ?>&tgl_mulai=<?= $t1; ?>&tgl_selesai=<?= $t2; ?>&cari=<?= $cari; ?>">Terakhir</a></li>
                </ul>
            </div>
        </div></div>

        <div class="row mt-5 text-center d-none d-print-flex">
            <div class="col-8"></div>
            <div class="col-4"><p>Guru Piket / Petugas,</p><br><br><br><p><strong>( ______________________ )</strong></p></div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>