<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$aksi = $_GET['aksi'] ?? '';

if (isset($_POST['simpan_catatan'])) {
    $siswa_id = $_POST['siswa_id'];
    $pelanggaran_id = $_POST['pelanggaran_id'];
    $tanggal = date('Y-m-d');
    $user_id = $_SESSION['user_id'];

    $s = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT s.nama AS n_sis, k.id AS k_id, k.nama AS n_kls FROM t_siswa s LEFT JOIN t_kelas_siswa ks ON s.id=ks.siswa_id LEFT JOIN t_kelas k ON ks.kelas_id=k.id WHERE s.id='$siswa_id' LIMIT 1"));
    $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, pelanggaran_kategori_id, nama, poin FROM t_pelanggaran WHERE id='$pelanggaran_id' LIMIT 1"));
    $g = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id, nama FROM t_guru WHERE user_id='$user_id' LIMIT 1"));
    $ta = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM t_tahun_ajaran WHERE status_aktif=1 LIMIT 1"));

    $query_insert = "INSERT INTO t_pelanggaran_siswa (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, poin, status, created_at, updated_at) VALUES ('" . ($ta['id'] ?? 2) . "', '$siswa_id', '{$s['n_sis']}', '" . ($s['k_id'] ?? 33) . "', '" . ($s['n_kls'] ?? 'XII RPL 2') . "', '$pelanggaran_id', '{$p['nama']}', '{$p['pelanggaran_kategori_id']}', '" . ($g['id'] ?? 1) . "', '" . ($g['nama'] ?? 'Administrator') . "', '$tanggal', '{$p['poin']}', 'Pending', NOW(), NOW())";

    if (mysqli_query($koneksi, $query_insert)) { header("Location: catat_pelanggaran.php?sukses=1"); exit(); }
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');

$where = $cari ? "WHERE (nama_siswa LIKE '%$cari%' OR nama_kelas LIKE '%$cari%' OR nama_pelanggaran LIKE '%$cari%')" : "";
$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM t_pelanggaran_siswa $where"));
$pages = ceil($total / $limit) ?: 1;
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_siswa $where ORDER BY tanggal DESC, id DESC LIMIT $offset, $limit");
$no = $offset + 1;

$opt_s = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY nama ASC");
$opt_p = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif='1' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Catat Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; z-index: 1000; }
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
            <li class="nav-item mb-1"><a href="catat_pelanggaran.php" class="active">Catat Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
            <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
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
        <?php if ($aksi == 'tambah'): ?>
            <!-- HALAMAN FORM TAMBAH -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Tambah Catat Pelanggaran</h2>
                <a href="catat_pelanggaran.php" class="btn btn-secondary btn-sm">&larr; Kembali</a>
            </div>
            <hr>
            <div class="card shadow-sm"><div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal:</label>
                        <input type="date" class="form-control bg-light" value="<?= date('Y-m-d'); ?>" readonly style="cursor: not-allowed;">
                        <small class="text-muted">Otomatis hari, bulan, dan tahun hari ini.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Siswa:</label>
                        <select name="siswa_id" class="form-control select2-siswa" required style="width: 100%;">
                            <option value="">-- Cari & Pilih Siswa --</option>
                            <?php while($s = mysqli_fetch_assoc($opt_s)): ?>
                                <option value="<?= $s['id']; ?>"><?= $s['nis'].' - '.$s['nama']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pelanggaran:</label>
                        <select name="pelanggaran_id" class="form-control select2-pelanggaran" required style="width: 100%;">
                            <option value="">-- Cari & Pilih Pelanggaran --</option>
                            <?php while($p = mysqli_fetch_assoc($opt_p)): ?>
                                <option value="<?= $p['id']; ?>"><?= $p['nama'].' (Poin: '.$p['poin'].')'; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="catat_pelanggaran.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="simpan_catatan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div></div>

        <?php else: ?>
            <!-- HALAMAN UTAMA TABEL -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Catat Pelanggaran Siswa</h2>
                <a href="catat_pelanggaran.php?aksi=tambah" class="btn btn-primary btn-sm">+ Tambah</a>
            </div>
            <hr>

            <?php if (isset($_GET['sukses'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">Data pelanggaran berhasil dicatat!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card shadow-sm"><div class="card-body">
                <div class="row mb-3 justify-content-between align-items-center">
                    <div class="col-auto d-flex align-items-center">
                        Tampilkan 
                        <select name="limit" class="form-select form-select-sm mx-2" style="width: 70px;" onchange="location = this.value;">
                            <option value="?limit=10&cari=<?= $cari; ?>" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                            <option value="?limit=25&cari=<?= $cari; ?>" <?= $limit == 25 ? 'selected' : ''; ?>>25</option>
                        </select> data
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex align-items-center">
                            <input type="hidden" name="limit" value="<?= $limit; ?>">
                            <span class="me-2">Cari:</span>
                            <input type="text" name="cari" class="form-control form-control-sm" value="<?= htmlspecialchars($cari); ?>" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr><th>No</th><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Pelanggaran</th><th>Poin</th><th>Pelapor</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php while($r = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($r['tanggal']); ?></td>
                                <td><?= htmlspecialchars($r['nama_siswa']); ?></td>
                                <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                                <td><?= htmlspecialchars($r['nama_pelanggaran']); ?></td>
                                <td><strong><?= htmlspecialchars($r['poin']); ?></strong></td>
                                <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                                <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($r['status']); ?></span></td>
                            </tr>
                            <?php endwhile; if(!$total): ?>
                            <tr><td colspan="8" class="text-center text-muted">Belum ada data catatan pelanggaran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Menampilkan <?= $total ? $offset + 1 : 0; ?> s.d. <?= min($offset + $limit, $total); ?> dari <?= $total; ?> data</small>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=1&limit=<?= $limit; ?>&cari=<?= $cari; ?>">Pertama</a></li>
                        <li class="page-item <?= $h <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h-1; ?>&limit=<?= $limit; ?>&cari=<?= $cari; ?>">Sebelumnya</a></li>
                        <?php for($i=1; $i<=$pages; $i++): ?>
                            <li class="page-item <?= $h == $i ? 'active' : ''; ?>"><a class="page-link" href="?halaman=<?= $i; ?>&limit=<?= $limit; ?>&cari=<?= $cari; ?>"><?= $i; ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $h+1; ?>&limit=<?= $limit; ?>&cari=<?= $cari; ?>">Berikutnya</a></li>
                        <li class="page-item <?= $h >= $pages ? 'disabled' : ''; ?>"><a class="page-link" href="?halaman=<?= $pages; ?>&limit=<?= $limit; ?>&cari=<?= $cari; ?>">Terakhir</a></li>
                    </ul>
                </div>
            </div></div>
        <?php endif; ?>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() { 
        $('.select2-siswa').select2({ theme: 'bootstrap-5', width: '100%' });
        $('.select2-pelanggaran').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
</body>
</html>