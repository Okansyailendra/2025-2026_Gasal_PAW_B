<?php
// hapus nota & ambil barang
require 'koneksi.php';

$action = $_GET['action'] ?? '';

if ($action === 'delete_nota') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("DELETE FROM nota_item WHERE nota_id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM nota WHERE nota_id=?")->execute([$id]);
    echo json_encode(['ok'=>true]);
    exit;
}

if ($action === 'get_barang') {
    $stmt = $pdo->query("SELECT * FROM barang ORDER BY nama_barang");
    echo json_encode($stmt->fetchAll());
    exit;
}
?>
