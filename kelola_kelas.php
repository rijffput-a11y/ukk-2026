<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];
    $status = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_kelas SET nama='$nama', tingkat='$tingkat', jurusan='$jurusan', status_aktif='$status' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif) VALUES ('$nama', '$tingkat', '$jurusan', '$status')");
    }
    header("Location: kelola_kelas.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_kelas WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_kelas.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE id='{$_GET['edit']}'")) : ['id'=>'','nama'=>'','tingkat'=>'','jurusan'=>'','status_aktif'=>'1'];

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');
$where = $cari ? "WHERE nama LIKE '%$cari%' OR tingkat LIKE '%$cari%' OR jurusan LIKE '%$cari%'" : "";

$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM t_kelas $where"));
$pages = ceil($total / $limit);
$data = mysqli_query($koneksi, "SELECT * FROM t_kelas $where LIMIT $offset, $limit");
$no = $offset + 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Kelas</title>
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
            <li class="nav-item mb-1"><a href="kelola_kelas.php" class="active">Kelola Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
            <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Kelola Kelas</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKelas">+ Tambah</button>
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

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr><th>No</th><th>Nama Kelas</th><th>Tingkat</th><th>Jurusan</th><th>Status</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($r = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($r['nama']); ?></td>
                        <td><?= htmlspecialchars($r['tingkat']); ?></td>
                        <td><?= htmlspecialchars($r['jurusan']); ?></td>
                        <td><?= htmlspecialchars($r['status_aktif']); ?></td>
                        <td class="text-center">
                            <a href="?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; if(!$total): ?>
                    <tr><td colspan="6" class="text-center text-muted">Data kelas tidak ditemukan.</td></tr>
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

    <div class="modal fade" id="modalKelas" tabindex="-1">
      <div class="modal-dialog"><form method="POST" class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><?= $edit['id'] ? 'Edit' : 'Tambah'; ?> Kelas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="id" value="<?= $edit['id']; ?>">
            <div class="mb-3"><label class="form-label">Nama Kelas:</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($edit['nama']); ?>" required></div>
            <div class="mb-3"><label class="form-label">Tingkat:</label><input type="text" name="tingkat" class="form-control" value="<?= htmlspecialchars($edit['tingkat']); ?>" placeholder="Contoh: XII"></div>
            <div class="mb-3"><label class="form-label">Jurusan:</label><input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($edit['jurusan']); ?>" placeholder="Contoh: RPL"></div>
            <div class="mb-3"><label class="form-label">Status Aktif:</label><input type="number" name="status_aktif" class="form-control" min="0" max="1" value="<?= htmlspecialchars($edit['status_aktif']); ?>" required></div>
        </div>
        <div class="modal-footer"><a href="kelola_kelas.php" class="btn btn-secondary">Batal</a><button type="submit" name="simpan" class="btn btn-primary">Simpan</button></div>
      </form></div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if(isset($_GET['edit'])): ?>
<script>new bootstrap.Modal(document.getElementById('modalKelas')).show();</script>
<?php endif; ?>
</body>
</html>