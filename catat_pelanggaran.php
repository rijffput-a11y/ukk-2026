<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
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
    else { echo "<script>alert('Gagal menyimpan.');</script>"; }
}

if (isset($_POST['tambah_pelanggaran_baru'])) {
    $kat_id = $_POST['pelanggaran_kategori_id'];
    $nama_p = mysqli_real_escape_string($koneksi, $_POST['nama_pelanggaran']);
    $poin = (int)$_POST['poin'];
    mysqli_query($koneksi, "INSERT INTO t_pelanggaran (pelanggaran_kategori_id, nama, poin, status_aktif, created_at, updated_at) VALUES ('$kat_id', '$nama_p', '$poin', '1', NOW(), NOW())");
    header("Location: catat_pelanggaran.php?tambah_sukses=1"); exit();
}

$opt_s = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY nama ASC");
$opt_p = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif='1' ORDER BY nama ASC");
$opt_kat = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran_kategori ORDER BY nama ASC");
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
        .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; }
        .main-content { margin-left: 250px; padding: 20px; }
        input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
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
            <?php if (isset($_SESSION['is_wali_kelas']) && $_SESSION['is_wali_kelas'] === true): ?>
                <li class="nav-item mb-1"><a href="laporan.php">Laporan</a></li>
            <?php endif; ?>
            <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
        </ul>
        <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>

    <div class="main-content">
        <h2>Catat Pelanggaran Siswa</h2>
        <hr>

        <?php if (isset($_GET['sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">Data pelanggaran berhasil dicatat!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (isset($_GET['tambah_sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">Jenis pelanggaran baru berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card shadow-sm"><div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal:</label>
                    <input type="date" name="tanggal" class="form-control bg-light" value="<?= date('Y-m-d'); ?>" readonly style="cursor: not-allowed;">
                    <small class="text-muted">Tanggal otomatis terset ke hari ini.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Siswa:</label>
                    <select name="siswa_id" class="form-control select2" required>
                        <option value="">-- Cari & Pilih Siswa --</option>
                        <?php while($s = mysqli_fetch_assoc($opt_s)): ?>
                            <option value="<?= $s['id']; ?>"><?= $s['nis'].' - '.$s['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold mb-0">Pelanggaran:</label>
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggaran">+ Tambah Pelanggaran Baru</button>
                    </div>
                    <select name="pelanggaran_id" class="form-control select2 mt-2" required>
                        <option value="">-- Cari & Pilih Pelanggaran --</option>
                        <?php while($p = mysqli_fetch_assoc($opt_p)): ?>
                            <option value="<?= $p['id']; ?>"><?= $p['nama'].' (Poin: '.$p['poin'].')'; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Pelanggaran</button>
            </form>
        </div></div>
    </div>

    <!-- MODAL TAMBAH PELANGGARAN -->
    <div class="modal fade" id="modalTambahPelanggaran" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form method="POST">
                <div class="modal-header"><h5 class="modal-title">Tambah Jenis Pelanggaran Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori:</label>
                        <select name="pelanggaran_kategori_id" class="form-control select2-modal" required style="width: 100%;">
                            <option value="">-- Cari & Pilih Kategori --</option>
                            <?php while($kat = mysqli_fetch_assoc($opt_kat)): ?>
                                <option value="<?= $kat['id']; ?>"><?= $kat['nama']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pelanggaran:</label>
                        <input type="text" name="nama_pelanggaran" class="form-control" placeholder="Contoh: Merokok" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Poin:</label>
                        <input type="number" name="poin" class="form-control" placeholder="Contoh: 15" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_pelanggaran_baru" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div></div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() { 
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' }); 
        // Mengaktifkan select2 di dalam modal agar bisa dicari
        $('#modalTambahPelanggaran').on('shown.bs.modal', function () {
            $('.select2-modal').select2({ theme: 'bootstrap-5', width: '100%', dropdownParent: $('#modalTambahPelanggaran') });
        });
    });
</script>
</body>
</html>