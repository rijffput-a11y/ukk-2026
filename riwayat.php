<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa ORDER BY tanggal DESC");
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
                        </tr>
                        <?php endwhile; if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="8" class="text-center">Belum ada riwayat pelanggaran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>