<?php
require '../config/db.php';
header('Content-Type: application/json');
$empresa_id = $_GET['empresa_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM ordenes_trabajo WHERE empresa_id=?");
$stmt->execute([$empresa_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>