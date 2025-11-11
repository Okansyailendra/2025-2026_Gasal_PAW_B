<?php
require 'koneksi.php';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT n.*, p.nama AS pelanggan_nama
    FROM nota n
    LEFT JOIN pelanggan p ON n.pelanggan_id = p.id
    WHERE n.nota_id = ?
");
$stmt->execute([$id]);
$nota = $stmt->fetch();

$items = $pdo->prepare("
    SELECT ni.*, b.nama_barang
    FROM nota_item ni
    LEFT JOIN barang b ON ni.barang_id = b.id
    WHERE ni.nota_id = ?
");
$items->execute([$id]);
$detail = $items->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Nota</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Detail Nota #<?= $nota['nota_id'] ?></h2>
    <a href="index.php">&lt; Kembali ke daftar</a><br><br>
    <p>
        <b>Nomor:</b> <?= htmlspecialchars($nota['nomor_nota']) ?><br>
        <b>Tanggal:</b> <?= $nota['tgl'] ?><br>
        <b>Pelanggan:</b> <?= htmlspecialchars($nota['pelanggan_nama']) ?><br>
        <b>Status:</b> <?= $nota['status'] ?>
    </p>

    <h3>Daftar Barang</h3>
<table>
<thead><tr><th>No</th><th>Nama Barang</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
<tbody>
<?php $no=1; foreach($detail as $d): ?>
<tr>
  <td><?= $no++ ?></td>
  <td><?= htmlspecialchars($d['nama_barang']) ?></td>
  <td><?= number_format($d['harga_satuan']) ?></td>
  <td><?= $d['qty'] ?></td>
  <td><?= number_format($d['subtotal']) ?></td>
</tr>
<?php endforeach ?>
</tbody>
</table>

<h4>Total Nota: Rp<?= number_format($nota['total_nota']) ?></h4>
</body>
</html>
