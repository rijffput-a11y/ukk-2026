<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$role = $_SESSION['role'] ?? ''; $user_id = $_SESSION['user_id'] ?? '';
$nama_user = $_SESSION['nama_user'] ?? $_SESSION['name'] ?? '';
$is_wali_kelas = $_SESSION['is_wali_kelas'] ?? false;
if ($role === 'guru' && !$is_wali_kelas) { echo "<script>alert('Akses ditolak!'); window.location='dashboard.php';</script>"; exit(); }
$tgl_mulai = $_GET['tgl_mulai'] ?? ''; $tgl_selesai = $_GET['tgl_selesai'] ?? '';
$where_date = (!empty($tgl_mulai) && !empty($tgl_selesai)) ? "AND p.tanggal BETWEEN '".mysqli_real_escape_string($koneksi, $tgl_mulai)."' AND '".mysqli_real_escape_string($koneksi, $tgl_selesai)."'" : "";
if ($role === 'admin') {
    $q_lap = mysqli_query($koneksi, "SELECT p.*, s.nis FROM t_pelanggaran_siswa p LEFT JOIN t_siswa s ON p.siswa_id = s.id WHERE 1=1 $where_date ORDER BY p.nama_siswa ASC");
    $kelas_binaan = '';
} else {
    $q_wk = mysqli_query($koneksi, "SELECT k.nama FROM t_wali_kelas tw JOIN t_kelas k ON tw.kelas_id = k.id JOIN t_guru g ON tw.guru_id = g.id WHERE (g.user_id = '$user_id' OR g.nama LIKE '%$nama_user%') AND tw.status_aktif = 1 LIMIT 1");
    $kelas_binaan = ($r_wk = mysqli_fetch_assoc($q_wk)) ? $r_wk['nama'] : '';
    $q_lap = mysqli_query($koneksi, "SELECT p.*, s.nis FROM t_pelanggaran_siswa p LEFT JOIN t_siswa s ON p.siswa_id = s.id WHERE p.nama_kelas = '$kelas_binaan' $where_date ORDER BY p.nama_siswa ASC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Laporan Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; z-index: 1000; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 30px; }
        @media print { .sidebar, .no-print { display: none !important; } .main-content { margin-left: 0 !important; padding: 0 !important; } }
    </style>
</head>
<body>
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
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php" class="active">Laporan</a></li>
        <?php endif; ?>
        <?php if ($role === 'guru'): ?>
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
            <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <?php if ($is_wali_kelas): ?><li class="nav-item mb-1"><a href="laporan.php" class="active">Laporan</a></li><?php endif; ?>
        <?php endif; ?>
        <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
    </ul>
    <hr><a href="logout.php" class="text-danger">Logout</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2>Laporan Pelanggaran Siswa</h2>
        <span class="badge bg-secondary">Login: <?= htmlspecialchars($nama_user); ?> (<?= ucfirst($role); ?>)</span>
    </div>

    <div class="card shadow-sm mb-4 no-print"><div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3"><label class="form-label fw-bold">Dari Tanggal:</label><input type="date" name="tgl_mulai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_mulai); ?>" required></div>
            <div class="col-md-3"><label class="form-label fw-bold">Sampai Tanggal:</label><input type="date" name="tgl_selesai" class="form-control form-control-sm" value="<?= htmlspecialchars($tgl_selesai); ?>" required></div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="laporan.php" class="btn btn-secondary btn-sm">Reset</a>
                <button type="button" onclick="window.print()" class="btn btn-success btn-sm ms-auto">Cetak Rekap</button>
                <button type="button" onclick="exportExcel()" class="btn btn-warning btn-sm text-dark">Export Excel</button>
            </div>
        </form>
    </div></div>

    <?php if ($role !== 'admin' && !empty($kelas_binaan)): ?>
        <div class="alert alert-info">Kelas binaan Anda: <strong><?= htmlspecialchars($kelas_binaan); ?></strong></div>
    <?php endif; ?>

    <div class="card shadow-sm"><div class="card-body">
        <h5 class="card-title mb-3">Rekapitulasi Data Pelanggaran (Urut A-Z)</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle" id="tabel-laporan">
                <thead class="table-dark">
                    <tr><th>No</th><th>Tanggal</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pencatat</th><th class="no-print text-center">Aksi Status Peringatan</th></tr>
                </thead>
                <tbody>
                    <?php if ($q_lap && mysqli_num_rows($q_lap) > 0): $no = 1; while ($r = mysqli_fetch_assoc($q_lap)): 
                        $sid = $r['siswa_id'] ?? '';
                        $q_tot = !empty($sid) ? mysqli_query($koneksi, "SELECT SUM(poin) as total FROM t_pelanggaran_siswa WHERE siswa_id='$sid'") : mysqli_query($koneksi, "SELECT SUM(poin) as total FROM t_pelanggaran_siswa WHERE nama_siswa='".mysqli_real_escape_string($koneksi, $r['nama_siswa'])."'");
                        $tp = (int)(mysqli_fetch_assoc($q_tot)['total'] ?? $r['poin']);
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $r['tanggal']; ?></td>
                        <td><?= htmlspecialchars($r['nis'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                        <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                        <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                        <td><span class="badge bg-danger"><?= $r['poin']; ?> (Total: <?= $tp; ?>)</span></td>
                        <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                        <td class="no-print text-center">
                            <?php if ($tp >= 75): ?>
                                <button type="button" onclick="cetakSurat('SP3', '<?= htmlspecialchars($r['nama_siswa'], ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nis'] ?? '-', ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nama_kelas'], ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nama_pelanggaran'], ENT_QUOTES); ?>', '<?= $tp; ?>', '<?= $r['tanggal']; ?>')" class="btn btn-dark btn-sm">Cetak SP3</button>
                            <?php elseif ($tp >= 50): ?>
                                <button type="button" onclick="cetakSurat('SP2', '<?= htmlspecialchars($r['nama_siswa'], ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nis'] ?? '-', ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nama_kelas'], ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nama_pelanggaran'], ENT_QUOTES); ?>', '<?= $tp; ?>', '<?= $r['tanggal']; ?>')" class="btn btn-warning btn-sm text-dark">Cetak SP2</button>
                            <?php else: ?>
                                <button type="button" onclick="cetakSurat('SP1', '<?= htmlspecialchars($r['nama_siswa'], ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nis'] ?? '-', ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nama_kelas'], ENT_QUOTES); ?>', '<?= htmlspecialchars($r['nama_pelanggaran'], ENT_QUOTES); ?>', '<?= $tp; ?>', '<?= $r['tanggal']; ?>')" class="btn btn-info btn-sm text-white">Cetak SP1</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center text-muted">Belum ada data laporan pelanggaran.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div></div>
</div>

<script>
function cetakSurat(jenisSP, namaSiswa, nis, kelas, pelanggaran, poin, tanggal) {
    let win = window.open('', '', 'height=750,width=850');
    win.document.write('<html><head><title>Surat ' + jenisSP + '</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"><style>body { padding: 40px; font-family: Arial, sans-serif; }</style></head><body>');
    win.document.write('<div class="text-center mb-4"><h5>PEMERINTAH PROVINSI / KABUPATEN</h5><h4>SEKOLAH MENENGAH KEJURUAN (SMK)</h4><hr></div>');
    win.document.write('<h4 class="text-center mb-4">SURAT ' + jenisSP + '</h4>');
    win.document.write('<p>Kepada Yth.<br>Orang Tua/Wali Murid: <strong>' + namaSiswa + '</strong> (Kelas: ' + kelas + ' / NIS: ' + nis + ')<br>di Tempat</p>');
    win.document.write('<p>Dengan hormat, siswa/i tersebut memiliki total akumulasi poin <strong>' + poin + '</strong> dengan pelanggaran terakhir: <strong>' + pelanggaran + '</strong> pada tanggal ' + tanggal + '.</p>');
    win.document.write('<br><table width="100%"><tr><td align="center">Orang Tua / Wali<br><br><br>( ____________ )</td><td align="center">Wali Kelas / Guru BK<br><br><br>( ____________ )</td></tr></table>');
    win.document.write('</body></html>');
    setTimeout(() => { win.print(); win.close(); }, 500);
}
function exportExcel() {
    let t = document.getElementById('tabel-laporan').cloneNode(true);
    t.querySelectorAll('.no-print').forEach(el => el.remove());
    let a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob([`<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%;}th,td{border:1px solid black;padding:5px;}</style></head><body><table>${t.outerHTML}</table></body></html>`], { type: 'application/vnd.ms-excel' }));
    a.download = 'Laporan_Pelanggaran_Siswa.xls'; a.click();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>