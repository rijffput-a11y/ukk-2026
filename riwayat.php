<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');

$where_clauses = ["status = 'Selesai'"];
if ($cari) { $where_clauses[] = "(nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%')"; }

$where = "WHERE " . implode(' AND ', $where_clauses);
$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa $where"));
$pages = ceil($total / $limit) ?: 1;
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where ORDER BY nama_kelas ASC, tanggal DESC LIMIT $offset, $limit");
$no = $offset + 1;
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
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
                <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
                <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <?php endif; ?>
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php" class="active">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            
            <?php 
            $user_id_sb = $_SESSION['user_id'];
            $q_chk_wk = mysqli_query($koneksi, "SELECT 1 FROM t_wali_kelas twk JOIN t_guru tg ON twk.guru_id = tg.id WHERE tg.user_id = '$user_id_sb' AND twk.status_aktif = 1");
            if (($_SESSION['role'] === 'admin') || mysqli_num_rows($q_chk_wk) > 0): 
            ?>
                <li class="nav-item mb-1"><a href="laporan.php">Laporan</a></li>
            <?php endif; ?>

            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Riwayat Pelanggaran Siswa (Selesai)</h2>
        <hr>
        <div class="card shadow-sm"><div class="card-body">
            <div class="row mb-3 justify-content-between">
                <div class="col-auto">Total: <b><?= $total; ?></b> data</div>
                <div class="col-auto">
                    <form method="GET" class="d-flex align-items-center">
                        <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari..." value="<?= htmlspecialchars($cari); ?>">
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pelapor</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($r['tanggal']); ?></td>
                            <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                            <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                            <td class="text-center"><strong><?= htmlspecialchars($r['poin']); ?></strong></td>
                            <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                            <td class="text-center"><span class="badge bg-success">Selesai</span></td>
                        </tr>
                        <?php endwhile; if(!$total): ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada riwayat pelanggaran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Hal <?= $h; ?> dari <?= $pages; ?></small>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h-1; ?>&cari=<?= $cari; ?>">Sebelumnya</a></li>
                    <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h+1; ?>&cari=<?= $cari; ?>">Berikutnya</a></li>
                </ul>
            </div>
        </div></div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>