<?php
echo "<h2>Eksplorasi Validasi Server-side di PHP</h2>";


   //1. REGULAR EXPRESSIONS (preg_match)

echo "<h3>1. Regular Expressions</h3>";
$name = "Okan-Syailendra";
$pattern = "/^[a-zA-Z'\- ]+$/";
if (preg_match($pattern, $name)) {
    echo "Nama '$name' valid (hanya huruf, tanda - dan ').<br>";
} else {
    echo "Nama '$name' tidak valid!<br>";
}


   //2. STRING FUNCTIONS (trim, strtolower, strtoupper)

echo "<h3>2. String Functions</h3>";
$email = "   ExAmple@GMAIL.com   ";
echo "Sebelum trim(): '$email'<br>";
$cleanEmail = trim($email); // hapus spasi
echo "Sesudah trim(): '$cleanEmail'<br>";
echo "Huruf kecil: " . strtolower($cleanEmail) . "<br>";
echo "Huruf besar: " . strtoupper($cleanEmail) . "<br>";


   //3. FILTER FUNCTIONS (filter_var, filter_input)

echo "<h3>3. Filter Functions</h3>";
$validEmail = "user@example.com";
$invalidEmail = "user@@example";
if (filter_var($validEmail, FILTER_VALIDATE_EMAIL)) {
    echo "Email '$validEmail' valid.<br>";
} else {
    echo "Email '$validEmail' tidak valid.<br>";
}
if (filter_var($invalidEmail, FILTER_VALIDATE_EMAIL)) {
    echo "Email '$invalidEmail' valid.<br>";
} else {
    echo "Email '$invalidEmail' tidak valid.<br>";
}

$url = "https://www.trunojoyo.ac.id";
if (filter_var($url, FILTER_VALIDATE_URL)) {
    echo "URL '$url' valid.<br>";
} else {
    echo "URL '$url' tidak valid.<br>";
}


   //4. TYPE TESTING (is_numeric, is_string, is_float)

echo "<h3>4. Type Testing</h3>";
$nim = "240411100179";
if (is_numeric($nim)) echo "NIM berisi angka (numeric).<br>";
else echo "NIM bukan angka.<br>";

$nama = "Okan";
if (is_string($nama)) echo "Nama adalah string.<br>";

$ipk = 3.75;
if (is_float($ipk)) echo "IPK berupa float (bilangan desimal).<br>";


   //5. DATE VALIDATION (checkdate)

echo "<h3>5. Date Validation</h3>";
$bulan = 2;
$hari = 29;
$tahun = 2024;
if (checkdate($bulan, $hari, $tahun)) {
    echo "Tanggal $hari/$bulan/$tahun valid.<br>";
} else {
    echo "Tanggal $hari/$bulan/$tahun tidak valid.<br>";
}

$tahun2 = 2025;
if (checkdate($bulan, $hari, $tahun2)) {
    echo "Tanggal $hari/$bulan/$tahun2 valid.<br>";
} else {
    echo "Tanggal $hari/$bulan/$tahun2 tidak valid (Feb 29 tidak ada).<br>";
}

echo "<hr><b>Selesai eksplorasi validasi server-side.</b>";
?>
