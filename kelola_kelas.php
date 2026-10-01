<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama']; // Menggunakan 'nama' sesuai database
    $tingkat = $_POST['tingkat'];
    $jurusan = $_POST['jurusan'];
    
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_kelas SET nama='$nama', tingkat='$tingkat', jurusan='$jurusan' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_kelas (nama, tingkat, jurusan, status_aktif) VALUES ('$nama', '$tingkat', '$jurusan', 1)");
    }
    header("Location: kelola_kelas.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_kelas WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_kelas.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_kelas WHERE id='{$_GET['edit']}'")) : ['id'=>'','nama'=>'','tingkat'=>'','jurusan'=>''];
$data = mysqli_query($koneksi, "SELECT * FROM t_kelas");
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Kelola Kelas</title></head>
<body>
    <h2>Kelola Kelas</h2>
    <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit['id']; ?>">
        <label>Nama Kelas (Contoh: XII RPL 1):</label><br>
        <input type="text" name="nama" value="<?= $edit['nama']; ?>" required><br>
        
        <label>Tingkat (Contoh: X, XI, XII):</label><br>
        <input type="text" name="tingkat" value="<?= $edit['tingkat']; ?>" required><br>
        
        <label>Jurusan (Contoh: RPL, TKJ):</label><br>
        <input type="text" name="jurusan" value="<?= $edit['jurusan']; ?>" required><br><br>
        
        <button type="submit" name="simpan">Simpan</button>
        <?php if($edit['id']): ?><a href="kelola_kelas.php">Batal</a><?php endif; ?>
    </form><br><hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>No</th><th>Nama</th><th>Tingkat</th><th>Jurusan</th><th>Aksi</th></tr>
        <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r['nama']; ?></td>
            <td><?= $r['tingkat']; ?></td>
            <td><?= $r['jurusan']; ?></td>
            <td>
                <a href="kelola_kelas.php?edit=<?= $r['id']; ?>">Edit</a> | 
                <a href="kelola_kelas.php?hapus=<?= $r['id']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>