<?php
function oldm($key) {
    global $old;
    return isset($old[$key]) ? htmlspecialchars($old[$key]) : '';
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Data Mahasiswa</title>
</head>
<body>
    <h2>Form Input Data Mahasiswa</h2>
    <form method="post" action="mahasiswa.php">
        <label>NIM (12 digit):</label><br>
        <input type="text" name="nim" value="<?php echo oldm('nim'); ?>"><br><br>

        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" value="<?php echo oldm('nama'); ?>"><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo oldm('email'); ?>"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password"><br><br>

        <input type="submit" value="Kirim">
        <input type="reset" value="Reset">
    </form>
</body>
</html>
