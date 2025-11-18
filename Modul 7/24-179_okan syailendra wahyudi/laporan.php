<?php
// --- 1. KONEKSI DATABASE ---
include 'koneksi.php';

// Helper function
function formatRupiah($angka) {
    return 'RP. ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tglString) {
    if (!$tglString) return '-';
    setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
    $date = new DateTime($tglString);
    return strftime('%d %b %Y', $date->getTimestamp());
}

// --- 2. CEK BATAS TANGGAL DI DATABASE (LOGIKA BARU) ---
// Kita ambil tanggal paling awal dan paling akhir yang ada di database
$sqlCek = "SELECT MIN(tanggal) as min_tgl, MAX(tanggal) as max_tgl FROM penjualan";
$resCek = $conn->query($sqlCek);
$dataDb = $resCek->fetch_assoc();

$dbMinDate = $dataDb['min_tgl']; // Tanggal paling lama di DB
$dbMaxDate = $dataDb['max_tgl']; // Tanggal paling baru di DB

// Default tanggal di form
$startDate = $_GET['startDate'] ?? date('Y-m-01'); 
$endDate   = $_GET['endDate'] ?? date('Y-m-d');    

$filteredData = [];
$totalPendapatan = 0;
$jumlahTransaksi = 0;
$chartLabels = [];
$chartTotals = [];
$pesanError = "";
$pesanInfo = ""; // Variabel baru untuk saran tanggal

// --- 3. VALIDASI INPUT USER VS DATABASE ---

// Cek 1: Apakah database kosong?
if (empty($dbMinDate)) {
    $pesanError = "Database Penjualan masih kosong. Belum ada data sama sekali.";
} 
// Cek 2: Validasi Logika Tanggal
elseif ($startDate > $endDate) {
    $pesanError = "Tanggal Mulai tidak boleh lebih besar dari Tanggal Akhir!";
}
// Cek 3: Apakah Tanggal Awal user sebelum data yang tersedia?
elseif ($startDate < $dbMinDate) {
    $pesanError = "Data tidak ditemukan untuk tanggal <b>" . formatTanggal($startDate) . "</b>.";
    $pesanInfo  = "Data di sistem baru mulai tersedia dari tanggal: <b>" . formatTanggal($dbMinDate) . "</b>";
}
// Cek 4: Apakah Tanggal Akhir user melebihi data yang tersedia?
elseif ($endDate > $dbMaxDate) {
    $pesanError = "Data belum tersedia sampai tanggal <b>" . formatTanggal($endDate) . "</b>.";
    $pesanInfo  = "Data terakhir yang tercatat di sistem adalah tanggal: <b>" . formatTanggal($dbMaxDate) . "</b>";
}
else {
    // --- 4. JIKA LOLOS VALIDASI, AMBIL DATA ---
    $sql = "SELECT * FROM penjualan WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['subtotal'] = $row['harga'] * $row['jumlah'];
            $filteredData[] = $row;
            $totalPendapatan += $row['subtotal'];
            $jumlahTransaksi++; 
            
            $chartLabels[] = formatTanggal($row['tanggal']) . " (" . $row['nama_barang'] . ")"; 
            $chartTotals[] = $row['subtotal'];
        }
    } else {
        // Fallback jika lolos validasi tanggal tapi query kosong (jarang terjadi jika validasi di atas benar)
        $pesanError = "Data tidak ditemukan pada rentang tanggal tersebut.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filter-container { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; display: flex; gap: 15px; align-items: center; }
        .tombol-aksi { margin-bottom: 20px; }
        button { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .btn-green { background-color: #28a745; }
        #cetakBtn { background-color: #007bff; }
        #excelBtn { background-color: #1d6f42; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        
        #totalContainer { display: flex; gap: 40px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; margin-top: 20px; }
        .total-box span { display: block; color: #6c757d; font-size: 14px; }
        .total-box strong { display: block; font-size: 24px; color: #343a40; margin-top: 5px; }
        .total-box.highlight strong { color: #28a745; }
        
        /* Style untuk Pesan Error/Info */
        .alert { padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 10px; text-align: center; border: 1px solid #f5c6cb; }
        .info-box { padding: 15px; background-color: #cce5ff; color: #004085; border-radius: 4px; margin-bottom: 20px; text-align: center; border: 1px solid #b8daff; }

        @media print {
            .filter-container, .tombol-aksi, .alert, .info-box { display: none !important; }
            .container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Laporan Penjualan</h1>

        <form class="filter-container" method="GET">
            <label>Mulai:</label>
            <input type="date" name="startDate" value="<?php echo $startDate; ?>" required>
            <label>Sampai:</label>
            <input type="date" name="endDate" value="<?php echo $endDate; ?>" required>
            <button type="submit" class="btn-green">Tampilkan</button>
        </form>

        <?php if (!empty($pesanError)): ?>
            <div class="alert">
                <strong>Perhatian!</strong> <br> <?php echo $pesanError; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($pesanInfo)): ?>
            <div class="info-box">
                ℹ️ <?php echo $pesanInfo; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($pesanError) && !empty($filteredData)): ?>
            
            <div class="tombol-aksi">
                <button id="cetakBtn" onclick="window.print()">🖨️ Cetak</button>
                <button id="excelBtn" onclick="downloadExcel()">📄 Excel</button>
            </div>

            <h2>Grafik Pendapatan</h2>
            <canvas id="salesChart" width="400" height="120"></canvas>
            <br>

            <h2>Rincian Data</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th> 
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredData as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo formatTanggal($item['tanggal']); ?></td>
                            <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                            <td class="text-right"><?php echo formatRupiah($item['harga']); ?></td>
                            <td class="text-right"><?php echo $item['jumlah']; ?></td>
                            <td class="text-right"><strong><?php echo formatRupiah($item['subtotal']); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="totalContainer">
                <div class="total-box">
                    <span>Jumlah Orang (No Pembelian)</span>
                    <strong><?php echo $jumlahTransaksi; ?> Orang</strong>
                </div>
                <div class="total-box highlight">
                    <span>Total Penghasilan</span>
                    <strong><?php echo formatRupiah($totalPendapatan); ?></strong>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <script>
        function downloadExcel() {
            const s = document.getElementsByName('startDate')[0].value;
            const e = document.getElementsByName('endDate')[0].value;
            window.location.href = `export_excel.php?startDate=${s}&endDate=${e}`;
        }

        <?php if (!empty($filteredData)): ?>
            new Chart(document.getElementById('salesChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartLabels); ?>,
                    datasets: [{
                        label: 'Pendapatan',
                        data: <?php echo json_encode($chartTotals); ?>,
                        backgroundColor: '#28a745'
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
        <?php endif; ?>
    </script>
</body>
</html>