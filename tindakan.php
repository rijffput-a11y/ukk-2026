<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Proses update status tindakan
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    mysqli_query($koneksi, "UPDATE t_pelanggaran_siswa SET status='$status' WHERE id='$id'");
    header("Location: tindakan.php"); exit();
}

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa ORDER BY tanggal DESC");
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

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Pelanggaran</th>
                            <th>Poin</th>
                            <th>Pelapor</th>
                            <th>Status</th>
                            <th>Aksi</th>
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
                            <td class="text-center"><strong><?= $r['poin']; ?></strong></td>
                            <td><?= $r['nama_guru']; ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= ($r['status'] == 'Selesai') ? 'success' : (($r['status'] == 'Diproses') ? 'warning' : 'secondary'); ?>">
                                    <?= $r['status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="правля btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['id']; ?>">Proses</button>
                            </td>
                        </tr>

                        <!-- Modal Update Status -->
                        <div class="modal fade" id="editModal<?= $r['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Status Tindakan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $r['id']; ?>">
                                            <p>Siswa: <strong><?= $r['nama_siswa']; ?></strong></p>
                                            <p>Pelanggaran: <strong><?= $r['nama_pelanggaran']; ?></strong></p>
                                            <div class="mb-3">
                                                <label class="form-label">Status Penanganan:</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="Pending" <?= ($r['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Diproses" <?= ($r['status'] == 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                                    <option value="Selesai" <?= ($r['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="update_status" class="btn btn-success">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="9" class="text-center">Belum ada data tindakan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>