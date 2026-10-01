<?php
require_once 'koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nip = $_POST['nip']; $nama = $_POST['nama']; $email = $_POST['email'];
    if ($id) {
        mysqli_query($koneksi, "UPDATE t_guru SET nip='$nip', nama='$nama', email='$email' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "INSERT INTO t_guru (nip, nama, email) VALUES ('$nip', '$nama', '$email')");
    }
    header("Location: kelola_guru.php"); exit();
}

if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM t_guru WHERE id='{$_GET['hapus']}'");
    header("Location: kelola_guru.php"); exit();
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id='{$_GET['edit']}'")) : ['id'=>'','nip'=>'','nama'=>'','email'=>''];
$data = mysqli_query($koneksi, "SELECT * FROM t_guru");
?>

<!DOCTYPE html>
<html lang="id">
<head><title>Kelola Guru</title></head>
<body>
    <h2>Kelola Guru</h2>
    <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a><hr>

    <form method="POST">
        <input type="hidden" name="id" value="<?= $edit['id']; ?>">
        <label>NIP:</label><br><input type="text" name="nip" value="<?= $edit['nip']; ?>" required><br>
        <label>Nama:</label><br><input type="text" name="nama" value="<?= $edit['nama']; ?>" required><br>
        <label>Email:</label><br><input type="email" name="email" value="<?= $edit['email']; ?>" required><br><br>
        <button type="submit" name="simpan">Simpan</button>
        <?php if($edit['id']): ?><a href="kelola_guru.php">Batal</a><?php endif; ?>
    </form><br><hr>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>No</th><th>NIP</th><th>Nama</th><th>Email</th><th>Aksi</th></tr>
        <?php $no=1; while($r = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r['nip']; ?></td>
            <td><?= $r['nama']; ?></td>
            <td><?= $r['email']; ?></td>
            <td>
                <a href="kelola_guru.php?edit=<?= $r['id']; ?>">Edit</a> | 
                <a href="kelola_guru.php?hapus=<?= $r['id']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>