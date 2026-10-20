<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if (isset($_POST['update_status'])) {
    mysqli_query($koneksi, "UPDATE t_pelanggaran_siswa SET status='{$_POST['status']}' WHERE id='{$_POST['id']}'");
    header("Location: tindakan.php"); exit();
}

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');
$where = $cari ? "WHERE nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%' OR status LIKE '%$cari%'" : "";

$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa $where"));
$pages = ceil($total / $limit);
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where ORDER BY tanggal DESC LIMIT $offset, $limit");
$no = $offset + 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Tindakan Pelanggaran</title>
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
                <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <?php endif; ?>
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php" class="active">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Tindakan & Penanganan Pelanggaran</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm"><div class="card-body">
            <div class="row mb-3 justify-content-between">
                <div class="col-auto">Tampilkan <select class="form-select form-select-sm d-inline-block w-auto"><option>10</option></select> data</div>
                <div class="col-auto">
                    <form method="GET" class="d-flex align-items-center">
                        <label class="me-2 mb-0 fw-bold">Cari:</label>
                        <input type="text" name="cari" class="form-control form-control-sm" value="<?= htmlspecialchars($cari); ?>">
                    </form>
                </div>
            </div>

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pelapor</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($r = mysqli_fetch_assoc($data)): 
                        $bg = $r['status'] == 'Selesai' ? 'success' : ($r['status'] == 'Diproses' ? 'warning' : 'secondary');
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= $r['tanggal']; ?></td>
                        <td><?= $r['nama_siswa']; ?></td>
                        <td><?= $r['nama_kelas']; ?></td>
                        <td><?= $r['nama_pelanggaran']; ?></td>
                        <td class="text-center"><strong><?= $r['poin']; ?></strong></td>
                        <td><?= $r['nama_guru']; ?></td>
                        <td class="text-center"><span class="badge bg-<?= $bg; ?>"><?= $r['status']; ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#m<?= $r['id']; ?>">Proses</button>
                        </td>
                    </tr>

                    <div class="modal fade" id="m<?= $r['id']; ?>" tabindex="-1">
                        <div class="modal-dialog"><div class="modal-content">
                            <form method="POST">
                                <div class="modal-header"><h5 class="modal-title">Ubah Status Tindakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $r['id']; ?>">
                                    <p>Siswa: <strong><?= $r['nama_siswa']; ?></strong></p>
                                    <p>Pelanggaran: <strong><?= $r['nama_pelanggaran']; ?></strong></p>
                                    <div class="mb-3">
                                        <label class="form-label">Status Penanganan:</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Pending" <?= $r['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Diproses" <?= $r['status'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                            <option value="Selesai" <?= $r['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="update_status" class="btn btn-success">Simpan</button>
                                </div>
                            </form>
                        </div></div>
                    </div>
                    <?php endwhile; if(!$total): ?>
                    <tr><td colspan="9" class="text-center text-muted">Belum ada data tindakan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Menampilkan <?= $total ? $offset + 1 : 0; ?> - <?= min($offset + $limit, $total); ?> dari <?= $total; ?> data</small>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=1&cari=<?= $cari; ?>">Pertama</a></li>
                    <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h-1; ?>&cari=<?= $cari; ?>">Sebelumnya</a></li>
                    <?php for($i=1; $i<=$pages; $i++): ?>
                        <li class="page-item <?= $h == $i ? 'active' : ''; ?>"><a class="page-link" href="?halaman=<?= $i; ?>&cari=<?= $cari; ?>"><?= $i; ?></a></li>
                    <?php endfor; ?>
                    <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h+1; ?>&cari=<?= $cari; ?>">Berikutnya</a></li>
                    <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $pages; ?>&cari=<?= $cari; ?>">Terakhir</a></li>
                </ul>
            </div>
        </div></div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>