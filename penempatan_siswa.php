<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

$role = $_SESSION['role'];
$nama_user = $_SESSION['nama_user'] ?? 'Administrator Utama';

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $siswa_id = $_POST['siswa_id']; 
    $ta_id = $_POST['tahun_ajaran_id']; 
    $kelas_id = $_POST['kelas_id'];
    $mulai = $_POST['tanggal_mulai']; 
    $selesai = $_POST['tanggal_selesai']; 
    $status = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_kelas_siswa SET siswa_id='$siswa_id', tahun_ajaran_id='$ta_id', kelas_id='$kelas_id', tanggal_mulai='$mulai', tanggal_selesai='$selesai', status_aktif='$status' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_kelas_siswa (siswa_id, tahun_ajaran_id, kelas_id, tanggal_mulai, tanggal_selesai, status_aktif) VALUES ('$siswa_id', '$ta_id', '$kelas_id', '$mulai', '$selesai', '$status')");
    }
    header("Location: penempatan_siswa.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_kelas_siswa WHERE id='{$_GET['hapus']}'");
    header("Location: penempatan_siswa.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_kelas_siswa WHERE id='{$_GET['edit']}'")) : ['id'=>'','siswa_id'=>'','tahun_ajaran_id'=>'','kelas_id'=>'','tanggal_mulai'=>'','tanggal_selesai'=>'','status_aktif'=>'1'];

// Konfigurasi Pagination (10 data per halaman)
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = $cari ? "WHERE ts.nama LIKE '%$cari%' OR ta.nama LIKE '%$cari%' OR tk.nama LIKE '%$cari%'" : "";

// Hitung total data & total halaman
$query_jumlah = mysqli_query($koneksi, "SELECT tks.* FROM t_kelas_siswa tks LEFT JOIN t_siswa ts ON tks.siswa_id=ts.id LEFT JOIN t_tahun_ajaran ta ON tks.tahun_ajaran_id=ta.id LEFT JOIN t_kelas tk ON tks.kelas_id=tk.id $where");
$total_data = mysqli_num_rows($query_jumlah);
$total_halaman = ceil($total_data / $batas);

// Ambil data sesuai halaman
$data = mysqli_query($koneksi, "SELECT tks.*, ts.nama AS n_siswa, ta.nama AS n_ta, tk.nama AS n_kelas FROM t_kelas_siswa tks LEFT JOIN t_siswa ts ON tks.siswa_id=ts.id LEFT JOIN t_tahun_ajaran ta ON tks.tahun_ajaran_id=ta.id LEFT JOIN t_kelas tk ON tks.kelas_id=tk.id $where LIMIT $halaman_awal, $batas");
$nomor = $halaman_awal + 1;
$jumlah_tampil = mysqli_num_rows($data);
$tampilkan_dari = $total_data > 0 ? $halaman_awal + 1 : 0;
$tampilkan_sampai = $halaman_awal + $jumlah_tampil;

$opt_siswa = mysqli_query($koneksi, "SELECT * FROM t_siswa");
$opt_ta = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");
$opt_kelas = mysqli_query($koneksi, "SELECT * FROM t_kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Penempatan Siswa</title>
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
            <li class="nav-item mb-1"><a href="penempatan_siswa.php" class="active">Penempatan Siswa</a></li>
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
            <h2>Penempatan Siswa</h2>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPenempatan">+ Tambah Penempatan</button>
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
                            <th>Siswa</th>
                            <th>Tahun Ajaran</th>
                            <th>Kelas</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= htmlspecialchars($r['n_siswa']); ?></td>
                            <td><?= htmlspecialchars($r['n_ta']); ?></td>
                            <td><?= htmlspecialchars($r['n_kelas']); ?></td>
                            <td><?= htmlspecialchars($r['tanggal_mulai']); ?></td>
                            <td><?= htmlspecialchars($r['tanggal_selesai']); ?></td>
                            <td><?= htmlspecialchars($r['status_aktif']); ?></td>
                            <td class="text-center">
                                <a href="penempatan_siswa.php?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a> | 
                                <a href="penempatan_siswa.php?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($total_data == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Data penempatan siswa tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- KETERANGAN & PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Menampilkan <?= $tampilkan_dari; ?> sampai <?= $tampilkan_sampai; ?> dari <?= $total_data; ?> data penempatan</small>
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

    <!-- MODAL TAMBAH / EDIT PENEMPATAN SISWA -->
    <div class="modal fade" id="modalPenempatan" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= $edit['id'] ? 'Edit Penempatan Siswa' : 'Tambah Penempatan Siswa'; ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= $edit['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Siswa:</label>
                <select name="siswa_id" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php 
                    $opt_siswa_modal = mysqli_query($koneksi, "SELECT * FROM t_siswa");
                    while($s = mysqli_fetch_assoc($opt_siswa_modal)): 
                    ?>
                        <option value="<?= $s['id']; ?>" <?= ($edit['siswa_id'] == $s['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($s['nama']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tahun Ajaran:</label>
                <select name="tahun_ajaran_id" class="form-control" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    <?php 
                    $opt_ta_modal = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");
                    while($ta = mysqli_fetch_assoc($opt_ta_modal)): 
                    ?>
                        <option value="<?= $ta['id']; ?>" <?= ($edit['tahun_ajaran_id'] == $ta['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($ta['nama']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Kelas:</label>
                <select name="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php 
                    $opt_kelas_modal = mysqli_query($koneksi, "SELECT * FROM t_kelas");
                    while($tk = mysqli_fetch_assoc($opt_kelas_modal)): 
                    ?>
                        <option value="<?= $tk['id']; ?>" <?= ($edit['kelas_id'] == $tk['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($tk['nama']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Mulai:</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($edit['tanggal_mulai']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Selesai:</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($edit['tanggal_selesai']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status Aktif:</label>
                <input type="number" name="status_aktif" class="form-control" min="0" max="1" value="<?= htmlspecialchars($edit['status_aktif']); ?>" required>
            </div>
          </div>
          <div class="modal-footer">
            <a href="penempatan_siswa.php" class="btn btn-secondary">Tutup / Batal</a>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if(isset($_GET['edit'])): ?>
<script>
    // Otomatis membuka modal jika parameter ?edit= aktif
    var myModal = new bootstrap.Modal(document.getElementById('modalPenempatan'));
    myModal.show();
</script>
<?php endif; ?>
</body>
</html>