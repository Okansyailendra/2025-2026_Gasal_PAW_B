<?php
include 'config.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $nama     = $_POST['nama'];
    $role     = '2';

    $check = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan, silakan pilih yang lain.";
    } else {
        $query = "INSERT INTO user (username, password, nama, role) VALUES ('$username', '$password', '$nama', '$role')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Registrasi Berhasil! Silakan Login.');
                    document.location.href = 'login.php';
                  </script>";
        } else {
            $error = "Registrasi Gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .register-container { max-width: 550px; margin-top: 50px; }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center">
        <div class="register-container card shadow-lg p-5">
            <div class="text-center mb-4">
                <h3 class="text-primary fw-bold">Daftar Akun Baru</h3>
                <p class="text-muted">Isi data diri untuk bergabung</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-lg" id="nama" name="nama" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Buat username unik" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Buat password kuat" required>
                </div>
                <div class="d-grid">
                    <button type="submit" name="register" class="btn btn-success btn-lg">DAFTAR SEKARANG</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted">Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Login disini</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>