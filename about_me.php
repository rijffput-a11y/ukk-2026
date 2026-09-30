<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
// Menggunakan nama Anda secara langsung
$nama = "Junita Putri Nur Rahman";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me - Sistem Pelanggaran</title>
    <!-- CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">
                <h4 class="text-white mb-4">My Website</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item mb-2">
                        <a href="dashboard.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="about.php" class="nav-link active"><i class="bi bi-person-circle me-2"></i> About Me</a>
                    </li>
                    <hr class="text-secondary">
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="mb-4">
                    <h1 class="fw-bold">About Me</h1>
                    <p class="text-muted">Informasi profil pengembang atau pengguna sistem.</p>
                    <hr>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
                            <div class="card-body">
                                <div class="mb-3">
                                    <i class="bi bi-person-circle text-secondary" style="font-size: 5rem;"></i>
                                </div>
                                <h3 class="fw-bold mb-1"><?= htmlspecialchars($nama); ?></h3>
                                <p class="text-muted mb-3"><span class="badge bg-danger"><?= strtoupper(htmlspecialchars($role)); ?></span></p>
                                <hr>
                                <p class="text-start mb-2"><strong>Aplikasi:</strong> Sistem Informasi Pelanggaran Siswa</p>
                                <p class="text-start mb-2"><strong>Institusi:</strong> SMK Muhammadiyah</p>
                                <p class="text-start mb-0"><strong>Tahun:</strong> 2026</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>