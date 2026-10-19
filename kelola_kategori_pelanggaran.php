<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_user'] ?? 'Administrator Utama';

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama']; 
    $deskripsi = $_POST['deskripsi']; 
    $status = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_pelanggaran_kategori SET nama='$nama', deskripsi='$deskripsi', status_aktif='$status' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_pelanggaran_kategori (nama, deskripsi, status_aktif) VALUES ('$nama', '$deskripsi', '$status')");
    }
    header("Location: kelola_kategori_pelanggaran.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_pelanggaran_kategori WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_kategori_pelanggaran.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori WHERE id='{$_GET['edit']}'")) : ['id'=>'','nama'=>'','deskripsi'=>'','status_aktif'=>'1'];

// Konfigurasi Pagination (10 data per halaman)
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = $cari ? "WHERE nama LIKE '%$cari%' OR deskripsi LIKE '%$cari%'" : "";

// Hitung total data & total halaman
$query_jumlah = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori $where");
$total_data = mysqli_num_rows($query_jumlah);
$total_halaman = ceil($total_data / $batas);

// Ambil data sesuai halaman
$data = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori $where LIMIT $halaman_awal, $batas");
$nomor = $halaman_awal + 1;
$jumlah_tampil = mysqli_num_rows($data);
$tampilkan_dari = $total_data > 0 ? $halaman_awal + 1 : 0;
$tampilkan_sampai = $halaman_awal + $jumlah_tampil;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Kategori Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #0d6efd; }
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
            <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
            <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php" class="active">Kelola Kategori Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
            <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Kelola Kategori Pelanggaran</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalKategori">+ Tambah Kategori</button>
        </div>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- BARIS FILTER & PENCARIAN -->
                <div class="row mb-3 justify-content-between">
                    <div class="col-auto d-flex align-items-center">
                        <span class="me-2">Tampilkan</span>
                        <select class="form-select form-select-sm d-inline-block w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="ms-2">data</span>
                    </div>
                    <div class="col-auto">
                        <form method="GET" class="d-flex align-items-center">
                            <label class="me-2 mb-0 fw-bold">Cari:</label>
                            <input type="text" name="cari" class="form-control form-control-sm" value="<?= htmlspecialchars($cari); ?>">
                        </form>
                    </div>
                </div>

                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= htmlspecialchars($r['nama']); ?></td>
                            <td><?= htmlspecialchars($r['deskripsi']); ?></td>
                            <td><?= htmlspecialchars($r['status_aktif']); ?></td>
                            <td class="text-center">
                                <a href="kelola_kategori_pelanggaran.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a> | 
                                <a href="kelola_kategori_pelanggaran.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($total_data == 0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Data kategori pelanggaran tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- KETERANGAN & PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Menampilkan <?= $tampilkan_dari; ?> sampai <?= $tampilkan_sampai; ?> dari <?= $total_data; ?> data kategori</small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Tombol Pertama -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=1<?= $cari ? '&cari='.$cari : ''; ?>">Pertama</a>
                            </li>
                            <!-- Tombol Sebelumnya -->
                            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= $halaman - 1; ?><?= $cari ? '&cari='.$cari : ''; ?>">Sebelumnya</a>
                            </li>
                            
                            <!-- Nomor Halaman -->
                            <?php for($x = 1; $x <= max($total_halaman, 1); $x++): ?>
                                <li class="page-item <?= ($halaman == $x) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?halaman=<?= $x; ?><?= $cari ? '&cari='.$cari : ''; ?>"><?= $x; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Tombol Berikutnya -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= $halaman + 1; ?><?= $cari ? '&cari='.$cari : ''; ?>">Berikutnya</a>
                            </li>
                            <!-- Tombol Terakhir -->
                            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?halaman=<?= max($total_halaman, 1); ?><?= $cari ? '&cari='.$cari : ''; ?>">Terakhir</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH / EDIT KATEGORI -->
    <div class="modal fade" id="modalKategori" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= $edit['id'] ? 'Edit Kategori Pelanggaran' : 'Tambah Kategori Pelanggaran'; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= $edit['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nama Kategori:</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($edit['nama']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi:</label>
                <textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($edit['deskripsi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Status Aktif:</label>
                <input type="number" name="status_aktif" class="form-control" min="0" max="1" value="<?= htmlspecialchars($edit['status_aktif']); ?>" required>
            </div>
          </div>
          <div class="modal-footer">
            <a href="kelola_kategori_pelanggaran.php" class="btn btn-secondary">Tutup / Batal</a>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if(isset($_GET['edit'])): ?>
<script>
    // Otomatis membuka modal jika parameter ?edit= aktif
    var myModal = new bootstrap.Modal(document.getElementById('modalKategori'));
    myModal.show();
</script>
<?php endif; ?>
</body>
</html>