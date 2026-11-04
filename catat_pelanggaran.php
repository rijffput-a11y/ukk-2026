<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $siswa_id = $_POST['siswa_id'];
    $pelanggaran_id = $_POST['pelanggaran_id'];
    $tanggal = $_POST['tanggal'];
    $user_id = $_SESSION['user_id'];

    // 1. Ambil data Siswa & Kelas (berdasarkan penempatan siswa yang aktif atau data siswa)
    $s_query = mysqli_query($koneksi, "SELECT s.nama AS n_sis, k.id AS k_id, k.nama AS n_kls FROM t_siswa s LEFT JOIN t_kelas_siswa ks ON s.id=ks.siswa_id LEFT JOIN t_kelas k ON ks.kelas_id=k.id WHERE s.id='$siswa_id' LIMIT 1");
    $s = mysqli_fetch_assoc($s_query);
    $kelas_id = $s['k_id'] ?? 33; // Default ke XII RPL 2 jika belum ada penempatan
    $nama_kelas = $s['n_kls'] ?? 'XII RPL 2';

    // 2. Ambil data Jenis Pelanggaran & Kategori ID-nya
    $p_query = mysqli_query($koneksi, "SELECT id, pelanggaran_kategori_id, nama, poin FROM t_pelanggaran WHERE id='$pelanggaran_id' LIMIT 1");
    $p = mysqli_fetch_assoc($p_query);

    // 3. Ambil data Guru berdasarkan user_id yang sedang login
    $g_query = mysqli_query($koneksi, "SELECT id, nama FROM t_guru WHERE user_id='$user_id' LIMIT 1");
    $g = mysqli_fetch_assoc($g_query);
    $guru_id = $g['id'] ?? 1; // Default ke ID guru pertama jika tidak ditemukan
    $nama_guru = $g['nama'] ?? 'Administrator';

    // 4. Ambil Tahun Ajaran yang sedang Aktif
    $ta_query = mysqli_query($koneksi, "SELECT id FROM t_tahun_ajaran WHERE status_aktif=1 LIMIT 1");
    $ta = mysqli_fetch_assoc($ta_query);
    $tahun_ajaran_id = $ta['id'] ?? 2; // Default ke ID tahun ajaran aktif

    // 5. Eksekusi Insert ke tabel t_pelanggaran_siswa
    $query_insert = "INSERT INTO t_pelanggaran_siswa 
        (tahun_ajaran_id, siswa_id, nama_siswa, kelas_id, nama_kelas, pelanggaran_id, nama_pelanggaran, pelanggaran_kategori_id, guru_id, nama_guru, tanggal, poin, status, created_at, updated_at) 
        VALUES 
        ('$tahun_ajaran_id', '$siswa_id', '{$s['n_sis']}', '$kelas_id', '$nama_kelas', '$pelanggaran_id', '{$p['nama']}', '{$p['pelanggaran_kategori_id']}', '$guru_id', '$nama_guru', '$tanggal', '{$p['poin']}', 'Pending', NOW(), NOW())";

    if (mysqli_query($koneksi, $query_insert)) {
        header("Location: catat_pelanggaran.php?sukses=1"); 
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($koneksi) . "');</script>";
    }
}

$opt_s = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY nama ASC");
$opt_p = mysqli_query($koneksi, "SELECT * FROM t_pelanggaran WHERE status_aktif='1' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Catat Pelanggaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f8f9fa; } .sidebar { min-height: 100vh; background: #212529; color: #fff; position: fixed; width: 250px; top: 0; left: 0; } .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; } .sidebar a:hover, .sidebar a.active { color: #fff; background: #0d6efd; } .main-content { margin-left: 250px; padding: 20px; }</style>
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

        <!-- Menu Laporan HANYA TAMPIL jika user yang login adalah Wali Kelas (Aldi Maulana) -->
        <?php if (isset($_SESSION['is_wali_kelas']) && $_SESSION['is_wali_kelas'] === true): ?>
            <li class="nav-item mb-1"><a href="laporan.php">Laporan</a></li>
        <?php endif; ?>

        <li class="nav-item mb-1"><a href="about_me.php">About Me</a></li>
    </ul>
    <hr><a href="logout.php" class="text-danger">Logout</a>
    </div>
    <div class="main-content">
        <h2>Catat Pelanggaran</h2>
        <hr>

        <?php if (isset($_GET['sukses'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data pelanggaran berhasil dicatat!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm"><div class="card-body">
            <form method="POST">
                <div class="mb-3"><label class="form-label">Tanggal:</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required></div>
                <div class="mb-3"><label class="form-label">Siswa:</label><select name="siswa_id" class="form-control" required><option value="">-- Pilih Siswa --</option><?php while($s = mysqli_fetch_assoc($opt_s)): ?><option value="<?= $s['id']; ?>"><?= $s['nis'].' - '.$s['nama']; ?></option><?php endwhile; ?></select></div>
                <div class="mb-3"><label class="form-label">Pelanggaran:</label><select name="pelanggaran_id" class="form-control" required><option value="">-- Pilih Pelanggaran --</option><?php while($p = mysqli_fetch_assoc($opt_p)): ?><option value="<?= $p['id']; ?>"><?= $p['nama'].' (Poin: '.$p['poin'].')'; ?></option><?php endwhile; ?></select></div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            </form>
        </div></div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>