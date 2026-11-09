<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// 1. PROSES UPDATE STATUS TINDAKAN
if (isset($_POST['update_status'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    mysqli_query($koneksi, "UPDATE t_pelanggaran_siswa SET status='$status', updated_at=NOW() WHERE id='$id'");
    header("Location: tindakan.php"); exit();
}

// 2. PROSES HAPUS DATA TINDAKAN
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM t_pelanggaran_siswa WHERE id='$id_hapus'");
    header("Location: tindakan.php"); exit();
}

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');

$where_clauses = [];
if ($cari) {
    $where_clauses[] = "(nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%' OR status LIKE '%$cari%')";
}

// Cek apakah guru yang login terdaftar sebagai wali kelas aktif
$user_id = $_SESSION['user_id'];
$q_wk = mysqli_query($koneksi, "SELECT twk.kelas_id FROM t_wali_kelas twk JOIN t_guru tg ON twk.guru_id = tg.id WHERE tg.user_id = '$user_id' AND twk.status_aktif = 1");

$arr_kelas = [];
while ($wk = mysqli_fetch_assoc($q_wk)) {
    $arr_kelas[] = $wk['kelas_id'];
}
$is_wali_kelas = !empty($arr_kelas);

// Jika BUKAN admin dan merupakan wali kelas, batasi hanya kelas yang diwalikelaskan
if ($_SESSION['role'] !== 'admin' && $is_wali_kelas) {
    $id_kelas_list = implode(',', $arr_kelas);
    $where_clauses[] = "kelas_id IN ($id_kelas_list)";
}

$where = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

$total_query = mysqli_query($koneksi, "SELECT 1 FROM t_pelanggaran_siswa $where");
$total = $total_query ? mysqli_num_rows($total_query) : 0;
$pages = $total > 0 ? ceil($total / $limit) : 1;
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
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
        <?php endif; ?>

        <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
        <li class="nav-item mb-1"><a href="tindakan.php" class="active">Tindakan</a></li>
        <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
        <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>

        <!-- Menu Laporan: Tampil untuk Admin ATAU Guru yang berstatus Wali Kelas -->
        <?php if (($_SESSION['role'] === 'admin') || $is_wali_kelas): ?>
            <li class="nav-item mb-1"><a href="laporan.php">Laporan</a></li>
        <?php endif; ?>

        <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
    </ul>
    <hr><a href="logout.php" class="text-danger">Logout</a>
</div>

    <div class="main-content">
        <h2>Tindakan & Penanganan Pelanggaran</h2>
        <hr>

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

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pelapor</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($data && mysqli_num_rows($data) > 0) {
                            while($r = mysqli_fetch_assoc($data)): 
                                $bg = $r['status'] == 'Selesai' ? 'success' : ($r['status'] == 'Diproses' ? 'warning' : 'secondary');
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center"><?= htmlspecialchars($r['tanggal']); ?></td>
                            <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                            <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                            <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                            <td class="text-center"><strong><?= htmlspecialchars($r['poin']); ?></strong></td>
                            <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                            <td class="text-center"><span class="badge bg-<?= $bg; ?>"><?= htmlspecialchars($r['status']); ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#m<?= $r['id']; ?>">Proses</button>
                                <a href="?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Yakin ingin menghapus data tindakan ini?')">Hapus</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="m<?= $r['id']; ?>" tabindex="-1">
                            <div class="modal-dialog"><div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header"><h5 class="modal-title">Ubah Status Tindakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $r['id']; ?>">
                                        <p>Siswa: <strong><?= htmlspecialchars($r['nama_siswa']); ?></strong></p>
                                        <p>Pelanggaran: <strong><?= htmlspecialchars($r['nama_pelanggaran']); ?></strong></p>
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
                        <?php 
                            endwhile; 
                        } else { 
                        ?>
                        <tr><td colspan="9" class="text-center text-muted">Belum ada data tindakan pelanggaran.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Menampilkan <?= $total ? $offset + 1 : 0; ?> s.d. <?= min($offset + $limit, $total); ?> dari <?= $total; ?> data</small>
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