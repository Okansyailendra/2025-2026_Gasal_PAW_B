<?php
require 'koneksi.php';

$nota = $pdo->query("
    SELECT n.*, p.nama AS pelanggan_nama
    FROM nota n
    LEFT JOIN pelanggan p ON n.pelanggan_id = p.id
    ORDER BY n.tgl ASC
")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Daftar Nota</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Daftar Nota (Master)</h2>
<a href="input_nota.php" class="btn">+ Tambah Nota Baru</a>
<br><br>
<table>
<thead><tr><th>ID</th><th>Nomor</th><th>Tanggal</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($nota as $n): ?>
<tr>
  <td><?= $n['nota_id'] ?></td>
  <td><?= htmlspecialchars($n['nomor_nota']) ?></td>
  <td><?= $n['tgl'] ?></td>
  <td><?= htmlspecialchars($n['pelanggan_nama']) ?></td>
  <td><?= number_format($n['total_nota']) ?></td>
  <td><?= $n['status'] ?></td>
  <td>
    <a href="nota_detail.php?id=<?= $n['nota_id'] ?>">Lihat Detail</a> |
    <a href="#" onclick="hapusNota(<?= $n['nota_id'] ?>)">Hapus</a>
  </td>
</tr>
<?php endforeach ?>
</tbody>
</table>

<script>
function hapusNota(id){
  if(!confirm("Hapus nota #" + id + "?")) return;
  fetch("ajax.php?action=delete_nota", {
    method:"POST",
    body:new URLSearchParams({id:id})
  }).then(r=>r.json()).then(()=>location.reload());
}
</script>
</body>
</html>
