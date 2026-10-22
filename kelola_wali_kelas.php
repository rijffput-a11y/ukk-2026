<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// 1. PROSES SIMPAN (TAMBAH / EDIT)
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $gid = $_POST['guru_id'];
    $kid = $_POST['kelas_id'];
    $tid = $_POST['tahun_ajaran_id'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_wali_kelas SET guru_id='$gid', kelas_id='$kid', tahun_ajaran_id='$tid', updated_at=NOW() WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_wali_kelas (guru_id, kelas_id, tahun_ajaran_id, created_at, updated_at) VALUES ('$gid', '$kid', '$tid', NOW(), NOW())");
    }
    header("Location: kelola_wali_kelas.php"); exit();
}

// 2. PROSES HAPUS
if (isset($_GET['hapus'])) {
    $id_wali = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM t_wali_kelas WHERE id='$id_wali'");
    header("Location: kelola_wali_kelas.php"); exit();
}

$aksi = $_GET['aksi'] ?? '';
$edit_id = $_GET['edit'] ?? '';
$edit = $edit_id ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_wali_kelas WHERE id='$edit_id'")) : null;

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');
$where = $cari ? "WHERE tg.nama LIKE '%$cari%' OR tk.nama LIKE '%$cari%' OR ta.nama LIKE '%$cari%'" : "";

$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT tw.id FROM t_wali_kelas tw JOIN t_guru tg ON tw.guru_id=tg.id JOIN t_kelas tk ON tw.kelas_id=tk.id JOIN t_tahun_ajaran ta ON tw.tahun_ajaran_id=ta.id $where"));
$pages = ceil($total / $limit);
$data = mysqli_query($koneksi, "SELECT tw.*, tg.nama AS nama_guru, tk.nama AS nama_kelas, ta.nama AS tahun_ajaran FROM t_wali_kelas tw JOIN t_guru tg ON tw.guru_id=tg.id JOIN t_kelas tk ON tw.kelas_id=tk.id JOIN t_tahun_ajaran ta ON tw.tahun_ajaran_id=ta.id $where LIMIT $offset, $limit");
$no = $offset + 1;

$opt_guru = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE status_aktif=1 ORDER BY nama ASC");
$opt_kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE status_aktif=1 ORDER BY nama ASC");
$opt_ta = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE status_aktif=1 ORDER BY nama DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Wali Kelas</title>
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
            <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
            <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
            <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_wali_kelas.php" class="active">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <?php if ($aksi === 'tambah' || $edit): ?>
            <!-- ================= FORM HALAMAN PENUH ================= -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><?= $edit ? 'Edit Wali Kelas' : 'Tambah Wali Kelas'; ?></h2>
                <a href="kelola_wali_kelas.php" class="btn btn-secondary btn-sm">&larr; Kembali</a>
            </div>
            <hr>

            <div class="card shadow-sm"><div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? ''; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Guru:</label>
                        <select name="guru_id" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php mysqli_data_seek($opt_guru, 0); while($g = mysqli_fetch_assoc($opt_guru)): ?>
                                <option value="<?= $g['id']; ?>" <?= (($edit['guru_id'] ?? '') == $g['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($g['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Kelas:</label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php mysqli_data_seek($opt_kelas, 0); while($k = mysqli_fetch_assoc($opt_kelas)): ?>
                                <option value="<?= $k['id']; ?>" <?= (($edit['kelas_id'] ?? '') == $k['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($k['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Tahun Ajaran:</label>
                        <select name="tahun_ajaran_id" class="form-control" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php mysqli_data_seek($opt_ta, 0); while($t = mysqli_fetch_assoc($opt_ta)): ?>
                                <option value="<?= $t['id']; ?>" <?= (($edit['tahun_ajaran_id'] ?? '') == $t['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($t['nama']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="kelola_wali_kelas.php" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div></div>

        <?php else: ?>
            <!-- ================= TABEL UTAMA ================= -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2>Kelola Wali Kelas</h2>
                <a href="?aksi=tambah" class="btn btn-primary btn-sm">+ Tambah</a>
            </div>
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

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr><th>No</th><th>Nama Guru</th><th>Kelas</th><th>Tahun Ajaran</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php while($r = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($r['nama_guru']); ?></td>
                                <td><?= htmlspecialchars($r['nama_kelas']); ?></td>
                                <td><?= htmlspecialchars($r['tahun_ajaran']); ?></td>
                                <td class="text-center">
                                    <a href="?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data wali kelas ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; if(!$total): ?>
                            <tr><td colspan="5" class="text-center text-muted">Data wali kelas tidak ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
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
        <?php endif; ?>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>