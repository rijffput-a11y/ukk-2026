<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// Query untuk merekap total poin per siswa berdasarkan tabel t_pelanggaran_siswa
$data = mysqli_query($koneksi, "SELECT siswa_id, nama_siswa, nama_kelas, SUM(poin) AS total_poin FROM t_pelanggaran_siswa GROUP BY siswa_id ORDER BY total_poin DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Rekap Poin Pelanggaran</title>
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
            <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php" class="active">Rekap Poin</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Rekap Poin Pelanggaran Siswa</h2>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th width="15%">Total Poin</th>
                            <th width="20%">Status Peringatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($r = mysqli_fetch_assoc($data)): 
                            $poin = $r['total_poin'];
                            // Menentukan status berdasarkan jumlah poin
                            if ($poin >= 100) {
                                $badge = '<span class="badge bg-danger">Berat / SP 3</span>';
                            } elseif ($poin >= 50) {
                                $badge = '<span class="badge bg-warning text-dark">Sedang / SP 2</span>';
                            } elseif ($poin > 0) {
                                $badge = '<span class="badge bg-info text-dark">Ringan / SP 1</span>';
                            } else {
                                $badge = '<span class="badge bg-success">Aman</span>';
                            }
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= $r['nama_siswa']; ?></td>
                            <td><?= $r['nama_kelas']; ?></td>
                            <td class="text-center"><strong><?= $poin; ?></strong></td>
                            <td class="text-center"><?= $badge; ?></td>
                        </tr>
                        <?php endwhile; if(mysqli_num_rows($data)==0): ?>
                        <tr><td colspan="5" class="text-center">Belum ada data rekap poin.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>