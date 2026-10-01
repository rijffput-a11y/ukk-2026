<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = $_POST['status_aktif'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_tahun_ajaran SET nama='$nama', tanggal_mulai='$tanggal_mulai', tanggal_selesai='$tanggal_selesai', status_aktif='$status_aktif' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_tahun_ajaran (nama, tanggal_mulai, tanggal_selesai, status_aktif) VALUES ('$nama', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')");
    }
    header("Location: kelola_tahun_ajaran.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_tahun_ajaran WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_tahun_ajaran.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran WHERE id='{$_GET['edit']}'")) : ['id'=>'','nama'=>'','tanggal_mulai'=>'','tanggal_selesai'=>'','status_aktif'=>''];
$data = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran");
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Kelola Tahun Ajaran</title></head>
<body>
    <h2>Kelola Tahun Ajaran</h2>
    <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit['id']; ?>">
        <label>Tahun Ajaran (Contoh: 2026/2027):</label><br>
        <input type="text" name="nama" value="<?= $edit['nama']; ?>" required><br>
        
        <label>Tanggal Mulai:</label><br>
        <input type="date" name="tanggal_mulai" value="<?= $edit['tanggal_mulai']; ?>" required><br>

        <label>Tanggal Selesai:</label><br>
        <input type="date" name="tanggal_selesai" value="<?= $edit['tanggal_selesai']; ?>" required><br>

        <label>Status Aktif (1 = Aktif, 0 = Tidak Aktif):</label><br>
        <input type="number" name="status_aktif" min="0" max="1" value="<?= $edit['status_aktif']; ?>" required><br><br>
        
        <button type="submit" name="simpan">Simpan</button>
        <?php if($edit['id']): ?><a href="kelola_tahun_ajaran.php">Batal</a><?php endif; ?>
    </form><br><hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Tahun Ajaran</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Status Aktif</th>
            <th>Aksi</th>
        </tr>
        <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r['nama']; ?></td>
            <td><?= $r['tanggal_mulai']; ?></td>
            <td><?= $r['tanggal_selesai']; ?></td>
            <td><?= $r['status_aktif']; ?></td>
            <td>
                <a href="kelola_tahun_ajaran.php?edit=<?= $r['id']; ?>">Edit</a> | 
                <a href="kelola_tahun_ajaran.php?hapus=<?= $r['id']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>