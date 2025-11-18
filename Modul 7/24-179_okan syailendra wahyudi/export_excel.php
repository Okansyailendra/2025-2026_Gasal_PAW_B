<?php
include 'koneksi.php';

function formatRupiah($angka) {
    return 'RP. ' . number_format($angka, 0, ',', '.');
}

$startDate = $_GET['startDate'] ?? date('Y-m-01');
$endDate   = $_GET['endDate'] ?? date('Y-m-d');

$filteredData = [];
$totalPendapatan = 0;
$jumlahTransaksi = 0;

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
        $jumlahTransaksi++; // Hitung Baris
    }
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"Laporan_Penjualan.xls\"");
?>

<h3>Laporan Penjualan <?php echo $startDate; ?> sd <?php echo $endDate; ?></h3>

<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($filteredData)): ?>
            <tr><td colspan="6" align="center">Data Kosong</td></tr>
        <?php else: ?>
            <?php foreach ($filteredData as $index => $item): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $item['tanggal']; ?></td>
                    <td><?php echo $item['nama_barang']; ?></td>
                    <td style="mso-number-format:General;"><?php echo $item['harga']; ?></td>
                    <td style="mso-number-format:General;"><?php echo $item['jumlah']; ?></td>
                    <td><?php echo $item['subtotal']; ?></td>
                </tr>
            <?php endforeach; ?>
            
            <tr><td colspan="6" style="background-color:#000; height:2px;"></td></tr>
            
            <tr>
                <td colspan="5" align="right"><strong>Jumlah Orang (Total Transaksi):</strong></td>
                <td><strong><?php echo $jumlahTransaksi; ?></strong></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><strong>Total Penghasilan:</strong></td>
                <td style="background-color: #yellow;"><strong><?php echo formatRupiah($totalPendapatan); ?></strong></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>