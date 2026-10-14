<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

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
    <meta charset="UTF-8">
    <title>Cetak Data Pelanggaran Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; padding: 20px; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- TOMBOL AKSI & FILTER (TIDAK IKUT DICETAK) -->
    <div class="card shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai:</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= htmlspecialchars($tgl_mulai); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai:</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= htmlspecialchars($tgl_selesai); ?>" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">Filter Data</button>
                    <button type="button" onclick="window.print()" class="btn btn-success me-2">Cetak / Print</button>
                    <a href="dashboard.php" class="btn btn-secondary" >Dashboard</a> 
                    <a href="logout.php" class="btn btn-secondary" >Logout</a>

                </div>
            </form>
        </div>
    </div>

    <!-- HEADER LAPORAN -->
    <div class="text-center mb-4">
        <h2>LAPORAN DATA PELANGGARAN SISWA</h2>
        <h4>SMK MUHAMMADIYAH</h4>
        <?php if (!empty($tgl_mulai) && !empty($tgl_selesai)): ?>
            <p class="mb-0">Periode: <strong><?= $tgl_mulai; ?></strong> s/d <strong><?= $tgl_selesai; ?></strong></p>
        <?php endif; ?>
        <hr class="border-2 border-dark">
    </div>

    <!-- TABEL CETAK -->
    <table class="table table-bordered align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Pelanggaran</th>
                <th>Poin</th>
                <th>Guru Pelapor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; if(mysqli_num_rows($data) > 0): ?>
                <?php while($r = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center"><?= $r['tanggal']; ?></td>
                    <td><?= $r['nama_siswa']; ?></td>
                    <td><?= $r['nama_kelas']; ?></td>
                    <td><?= $r['nama_pelanggaran']; ?></td>
                    <td class="text-center"><strong><?= $r['poin']; ?></strong></td>
                    <td><?= $r['nama_guru']; ?></td>
                    <td class="text-center"><?= $r['status']; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN LAPORAN (TAMPIL SAAT PRINT) -->
    <div class="row mt-5 text-center">
        <div class="col-8"></div>
        <div class="col-4">
            <p>Petugas / Guru Piket,</p>
            <br><br><br>
            <p><strong>( ______________________ )</strong></p>
        </div>
    </div>

</body>
</html>