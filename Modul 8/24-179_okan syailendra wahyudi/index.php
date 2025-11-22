<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjualan - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .card-header { font-weight: bold; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Sistem Penjualan</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($page=='home')?'active':''; ?>" href="index.php?page=home">Home</a>
                    </li>

                    <?php if ($_SESSION['role'] == 'Admin') : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($page=='master')?'active':''; ?>" href="index.php?page=master">Data Master</a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link <?= ($page=='transaksi')?'active':''; ?>" href="index.php?page=transaksi">Transaksi</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= ($page=='laporan')?'active':''; ?>" href="index.php?page=laporan">Laporan</a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                           <i class="fas fa-user"></i> <?= $_SESSION['user']; ?> (<?= $_SESSION['role']; ?>)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                
                <?php 
                switch ($page) {
                    case 'home':
                        echo "<h2 class='text-primary'>Selamat Datang!</h2>";
                        echo "<p class='lead'>Halo <b>" . $_SESSION['user'] . "</b>, Anda login sebagai <b>" . $_SESSION['role'] . "</b>.</p>";
                        echo "<hr>";
                        echo "<p>Silakan pilih menu di atas untuk mulai mengelola sistem.</p>";
                        break;

                    case 'master':
                        if ($_SESSION['role'] !== 'Admin') {
                            echo "<div class='alert alert-danger'><b>AKSES DITOLAK!</b> Anda tidak memiliki izin mengakses halaman ini.</div>";
                        } else {
                            echo "<h3 class='text-primary border-bottom pb-2'>Data Master</h3>";
                            echo "<p>Halaman ini hanya dapat dilihat oleh <b>Level 1 (Admin)</b>.</p>";
                            echo "<button class='btn btn-success'>+ Tambah Data Barang</button>";
                        }
                        break;

                    case 'transaksi':
                        echo "<h3 class='text-success border-bottom pb-2'>Halaman Transaksi</h3>";
                        echo "<p>Halaman ini dapat diakses oleh Admin maupun User.</p>";
                        echo "<div class='alert alert-info'>Form transaksi penjualan akan tampil di sini.</div>";
                        break;

                    case 'laporan':
                        echo "<h3 class='text-warning border-bottom pb-2'>Halaman Laporan</h3>";
                        echo "<p>Rekapitulasi laporan penjualan bulanan.</p>";
                        echo "<table class='table table-bordered mt-3'>
                                <thead class='table-light'><tr><th>No</th><th>Tanggal</th><th>Omset</th></tr></thead>
                                <tbody><tr><td>1</td><td>20 Nov 2025</td><td>Rp 1.500.000</td></tr></tbody>
                              </table>";
                        break;

                    default:
                        echo "<div class='alert alert-warning'>Halaman tidak ditemukan!</div>";
                        break;
                }
                ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>