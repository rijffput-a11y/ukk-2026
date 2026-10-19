<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_user'];

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_siswa SET nis='$nis', nisn='$nisn', nama='$nama', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', alamat='$alamat' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_siswa (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif) VALUES ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir', '$alamat', 1)");
    }
    header("Location: kelola_siswa.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_siswa WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_siswa.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id='{$_GET['edit']}'")) : ['id'=>'','nis'=>'','nisn'=>'','nama'=>'','jenis_kelamin'=>'','tanggal_lahir'=>'','alamat'=>''];

// Konfigurasi Pagination (10 data per halaman)
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = $cari ? "WHERE nis LIKE '%$cari%' OR nisn LIKE '%$cari%' OR nama LIKE '%$cari%' OR alamat LIKE '%$cari%'" : "";

// Hitung total data & total halaman
$query_jumlah = mysqli_query($koneksi, "SELECT * FROM t_siswa $where");
$total_data = mysqli_num_rows($query_jumlah);
$total_halaman = ceil($total_data / $batas);

// Ambil data sesuai halaman
$data = mysqli_query($koneksi, "SELECT * FROM t_siswa $where LIMIT $halaman_awal, $batas");
$nomor = $halaman_awal + 1;
$jumlah_tampil = mysqli_num_rows($data);
$tampilkan_dari = $total_data > 0 ? $halaman_awal + 1 : 0;
$tampilkan_sampai = $halaman_awal + $jumlah_tampil;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa</title>
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

    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column p-3">
        <h4 class="text-white mb-4">Pelanggaran Siswa</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item mb-1"><a href="dashboard.php">Dashboard</a></li>
            <?php if ($role === 'admin'): ?>
                <li class="nav-item mb-1"><a href="kelola_guru.php">Kelola Guru</a></li>
                <li class="nav-item mb-1"><a href="kelola_siswa.php" class="active">Kelola Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_tahun_ajaran.php">Kelola Tahun Ajaran</a></li>
                <li class="nav-item mb-1"><a href="penempatan_siswa.php">Penempatan Siswa</a></li>
                <li class="nav-item mb-1"><a href="kelola_wali_kelas.php">Kelola Wali Kelas</a></li>
                <li class="nav-item mb-1"><a href="kelola_kategori_pelanggaran.php">Kelola Kategori Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="laporan.php">laporan</a></li>
                <li class="nav-item mb-1"><a href="kelola_jenis_pelanggaran.php">Kelola Jenis Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
            <?php endif; ?>

            <?php if ($role === 'guru'): ?>
                <li class="nav-item mb-1"><a href="catat_pelanggaran.php">Catat Pelanggaran</a></li>
                <li class="nav-item mb-1"><a href="tindakan.php">Tindakan</a></li>
                <li class="nav-item mb-1"><a href="riwayat.php">Riwayat</a></li>
                <li class="nav-item mb-1"><a href="rekap_poin.php">Rekap Poin</a></li>
            <?php endif; ?>
        </ul>
        <hr>
        <a href="logout.php" class="text-danger">Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Kelola Siswa</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalSiswa">+ Tambah Siswa</button>
        </div>
        <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

        <!-- TABEL DATA SISWA -->
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
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>L/P</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= htmlspecialchars($r['nis']); ?></td>
                            <td><?= htmlspecialchars($r['nisn']); ?></td>
                            <td><?= htmlspecialchars($r['nama']); ?></td>
                            <td><?= htmlspecialchars($r['jenis_kelamin']); ?></td>
                            <td><?= htmlspecialchars($r['tanggal_lahir']); ?></td>
                            <td><?= htmlspecialchars($r['alamat']); ?></td>
                            <td class="text-center">
                                <a href="kelola_siswa.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="kelola_siswa.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($total_data == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Data siswa tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- KETERANGAN & PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Menampilkan <?= $tampilkan_dari; ?> sampai <?= $tampilkan_sampai; ?> dari <?= $total_data; ?> data siswa</small>
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

    <!-- MODAL TAMBAH / EDIT SISWA -->
    <div class="modal fade" id="modalSiswa" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= $edit['id'] ? 'Edit Data Siswa' : 'Tambah Data Siswa'; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= $edit['id']; ?>">
            <div class="mb-3">
                <label class="form-label">NIS:</label>
                <input type="text" name="nis" class="form-control" value="<?= htmlspecialchars($edit['nis']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">NISN:</label>
                <input type="text" name="nisn" class="form-control" value="<?= htmlspecialchars($edit['nisn']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Siswa:</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($edit['nama']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Kelamin (L/P):</label>
                <input type="text" name="jenis_kelamin" class="form-control" value="<?= htmlspecialchars($edit['jenis_kelamin']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Lahir:</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($edit['tanggal_lahir']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat:</label>
                <textarea name="alamat" class="form-control" rows="2" required><?= htmlspecialchars($edit['alamat']); ?></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <a href="kelola_siswa.php" class="btn btn-secondary">Tutup / Batal</a>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if(isset($_GET['edit'])): ?>
    <script>
        // Otomatis membuka modal jika parameter ?edit= aktif
        var myModal = new bootstrap.Modal(document.getElementById('modalSiswa'));
        myModal.show();
    </script>
    <?php endif; ?>
</body>
</html>