<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// 1. PROSES SIMPAN (TAMBAH / EDIT)
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status = $_POST['status_aktif'] == 'Aktif' ? 1 : 0;
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if (!empty($password) && $password !== $konfirmasi) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.history.back();</script>";
        exit();
    }
    
    if ($id) {
        $d_old = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT user_id FROM t_guru WHERE id='$id'"));
        mysqli_query($koneksi, "UPDATE t_guru SET nip='$nip', nama='$nama', email='$email', status_aktif='$status' WHERE id='$id'");
        
        if ($d_old['user_id']) {
            $pass_query = !empty($password) ? ", password='".password_hash($password, PASSWORD_DEFAULT)."'" : "";
            mysqli_query($koneksi, "UPDATE t_users SET name='$nama', email='$email' $pass_query WHERE id='{$d_old['user_id']}'");
        }
    } else {
        $hashed = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : password_hash('123456', PASSWORD_DEFAULT);
        mysqli_query($koneksi, "INSERT INTO t_users (name, email, password, role) VALUES ('$nama', '$email', '$hashed', 'guru')");
        $user_id_baru = mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO t_guru (nip, nama, email, status_aktif, user_id) VALUES ('$nip', '$nama', '$email', '$status', '$user_id_baru')");
    }
    header("Location: kelola_guru.php"); exit();
}

// 2. PROSES HAPUS (MEMBERSIHKAN RELASI)
if (isset($_GET['hapus'])) {
    $id_guru = $_GET['hapus'];
    $get_guru = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT user_id FROM t_guru WHERE id='$id_guru'"));
    
    mysqli_query($koneksi, "DELETE FROM t_pelanggaran_siswa WHERE guru_id='$id_guru'");
    mysqli_query($koneksi, "DELETE FROM t_wali_kelas WHERE guru_id='$id_guru'");
    mysqli_query($koneksi, "DELETE FROM t_guru WHERE id='$id_guru'");
    if ($get_guru['user_id']) {
        mysqli_query($koneksi, "DELETE FROM t_users WHERE id='{$get_guru['user_id']}'");
    }
    header("Location: kelola_guru.php"); exit();
}

$aksi = $_GET['aksi'] ?? '';
$edit_id = $_GET['edit'] ?? '';
$edit = $edit_id ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id='$edit_id'")) : null;

$limit = 10;
$h = max(1, (int)($_GET['halaman'] ?? 1));
$offset = ($h - 1) * $limit;
$cari = mysqli_real_escape_string($koneksi, $_GET['cari'] ?? '');
$where = $cari ? "WHERE nip LIKE '%$cari%' OR nama LIKE '%$cari%' OR email LIKE '%$cari%'" : "";
$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM t_guru $where"));
$pages = ceil($total / $limit);
$data = mysqli_query($koneksi, "SELECT * FROM t_guru $where LIMIT $offset, $limit");
$no = $offset + 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Kelola Guru</title>
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
            <li class="nav-item mb-1"><a href="kelola_guru.php" class="active">Kelola Guru</a></li>
            <li class="nav-item mb-1"><a href="kelola_siswa.php">Kelola Siswa</a></li>
            <li class="nav-item mb-1"><a href="kelola_kelas.php">Kelola Kelas</a></li>
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
        <?php if ($aksi === 'tambah' || $edit): ?>
            <!-- FORM HALAMAN PENUH -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><?= $edit ? 'Edit Data Guru' : 'Tambah Data Guru'; ?></h2>
                <a href="kelola_guru.php" class="btn btn-secondary btn-sm">&larr; Kembali</a>
            </div>
            <hr>
            <div class="card shadow-sm"><div class="card-body">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? ''; ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIP (Maks. 18 Angka):</label>
                        <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($edit['nip'] ?? ''); ?>" maxlength="18" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Guru:</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($edit['nama'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email:</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Login:</label>
                        <input type="password" name="password" class="form-control" placeholder="<?= $edit ? 'Kosongkan jika tidak diubah' : 'Masukkan password'; ?>" <?= $edit ? '' : 'required'; ?>>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Password:</label>
                        <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Aktif:</label>
                        <select name="status_aktif" class="form-control" required>
                            <option value="Aktif" <?= (($edit['status_aktif'] ?? 1) == 1) ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Tidak Aktif" <?= (($edit['status_aktif'] ?? 1) == 0) ? 'selected' : ''; ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="kelola_guru.php" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div></div>
        <?php else: ?>
            <!-- TABEL UTAMA & PAGINATION -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2>Kelola Guru</h2>
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
                            <tr><th>No</th><th>NIP</th><th>Nama Guru</th><th>Email</th><th>Status</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php while($r = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($r['nip']); ?></td>
                                <td><?= htmlspecialchars($r['nama']); ?></td>
                                <td><?= htmlspecialchars($r['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?= ($r['status_aktif'] == 1) ? 'success' : 'secondary'; ?>">
                                        <?= ($r['status_aktif'] == 1) ? 'Aktif' : 'Tidak Aktif'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?edit=<?= $r['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?hapus=<?= $r['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data guru ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; if(!$total): ?>
                            <tr><td colspan="6" class="text-center text-muted">Data guru tidak ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION HALAMAN -->
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