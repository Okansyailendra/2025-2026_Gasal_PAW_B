<?php
require 'koneksi.php';

$pelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY nama")->fetchAll();
$barang = $pdo->query("SELECT * FROM barang ORDER BY nama_barang")->fetchAll();
$messages = [];

// --- fungsi buat nomor nota otomatis (format: N001, N002, dst) ---
function buatNomorNota($pdo) {
    $row = $pdo->query("SELECT nomor_nota FROM nota ORDER BY nota_id DESC LIMIT 1")->fetch();
    if ($row && preg_match('/N(\d+)/', $row['nomor_nota'], $m)) {
        $angka = (int)$m[1] + 1;
    } else {
        $angka = 1;
    }
    return 'N' . str_pad($angka, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = buatNomorNota($pdo); // otomatis N001, N002, dst
    $tgl = date('Y-m-d H:i:s');
    $pelanggan_id = (int)$_POST['pelanggan_id'];
    $user_id = 1; // default user
    $barang_ids = $_POST['barang_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO nota (nomor_nota, tgl, pelanggan_id, user_id, total_nota) VALUES (?,?,?,?,0)");
        $stmt->execute([$nomor, $tgl, $pelanggan_id, $user_id]);
        $nota_id = $pdo->lastInsertId();

        $total = 0;
        foreach ($barang_ids as $i => $bid) {
            if ($bid == '') continue;
            $stmt = $pdo->prepare("SELECT harga FROM barang WHERE id=?");
            $stmt->execute([$bid]);
            $harga = (int)$stmt->fetchColumn();
            $qty = (int)($qtys[$i] ?? 1);
            $subtotal = $harga * $qty;
            $total += $subtotal;

            $pdo->prepare("INSERT INTO nota_item (nota_id, barang_id, harga_satuan, qty) VALUES (?,?,?,?)")
                ->execute([$nota_id, $bid, $harga, $qty]);
        }

        $pdo->prepare("UPDATE nota SET total_nota=? WHERE nota_id=?")->execute([$total, $nota_id]);
        $pdo->commit();
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $messages[] = ['type'=>'error','text'=>'Gagal menyimpan: '.$e->getMessage()];
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Input Nota</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Tambah Nota Baru</h2>
<a href="index.php">&lt; Kembali</a>
<?php foreach($messages as $m): ?>
<div class="<?= htmlspecialchars($m['type']) ?>"><?= htmlspecialchars($m['text']) ?></div>
<?php endforeach ?>

<form method="post">
  <label>Nomor Nota<br>
    <input name="nomor_nota" value="<?= buatNomorNota($pdo) ?>" readonly>
  </label><br>
  <label>Pelanggan<br>
    <select name="pelanggan_id" required>
      <option value="">-- pilih pelanggan --</option>
      <?php foreach($pelanggan as $p): ?>
        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
      <?php endforeach ?>
    </select>
  </label><br><br>

  <h4>Detail Barang</h4>
  <table id="tblBarang">
    <thead><tr><th>Barang</th><th>Qty</th><th>Aksi</th></tr></thead>
    <tbody>
      <tr>
        <td>
          <select name="barang_id[]">
            <option value="">-- pilih barang --</option>
            <?php foreach($barang as $b): ?>
              <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nama_barang']) ?> - Rp<?= number_format($b['harga']) ?></option>
            <?php endforeach ?>
          </select>
        </td>
        <td><input type="number" name="qty[]" value="1" min="1"></td>
        <td><button type="button" onclick="hapusRow(this)">X</button></td>
      </tr>
    </tbody>
  </table>
  <button type="button" onclick="tambahRow()">+ Tambah Barang</button>
  <br><br>
  <button name="add_nota" type="submit">Simpan Nota</button>
</form>

<script>
function tambahRow(){
  let tbl = document.querySelector("#tblBarang tbody");
  let row = tbl.rows[0].cloneNode(true);
  row.querySelectorAll("input").forEach(i=>i.value='');
  row.querySelector("select").selectedIndex=0;
  tbl.appendChild(row);
}
function hapusRow(btn){
  let tr = btn.closest("tr");
  let tbody = tr.parentNode;
  if(tbody.rows.length>1) tr.remove();
}
</script>
</body>
</html>
