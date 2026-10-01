<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

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
$data = mysqli_query($koneksi, "SELECT * FROM t_siswa");
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Kelola Siswa</title></head>
<body>
    <h2>Kelola Siswa</h2>
    <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit['id']; ?>">
        <label>NIS:</label><br><input type="text" name="nis" value="<?= $edit['nis']; ?>" required><br>
        <label>NISN:</label><br><input type="text" name="nisn" value="<?= $edit['nisn']; ?>" required><br>
        <label>Nama Siswa:</label><br><input type="text" name="nama" value="<?= $edit['nama']; ?>" required><br>
        <label>Jenis Kelamin (L/P):</label><br><input type="text" name="jenis_kelamin" value="<?= $edit['jenis_kelamin']; ?>" required><br>
        <label>Tanggal Lahir:</label><br><input type="date" name="tanggal_lahir" value="<?= $edit['tanggal_lahir']; ?>" required><br>
        <label>Alamat:</label><br><textarea name="alamat" required><?= $edit['alamat']; ?></textarea><br><br>
        
        <button type="submit" name="simpan">Simpan</button>
        <?php if($edit['id']): ?><a href="kelola_siswa.php">Batal</a><?php endif; ?>
    </form><br><hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>L/P</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r['nis']; ?></td>
            <td><?= $r['nisn']; ?></td>
            <td><?= $r['nama']; ?></td>
            <td><?= $r['jenis_kelamin']; ?></td>
            <td><?= $r['tanggal_lahir']; ?></td>
            <td><?= $r['alamat']; ?></td>
            <td>
                <a href="kelola_siswa.php?edit=<?= $r['id']; ?>">Edit</a> | 
                <a href="kelola_siswa.php?hapus=<?= $r['id']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>