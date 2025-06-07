<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// Leer datos
$stmt = $pdo->prepare("SELECT * FROM empresas WHERE id=?");
$stmt->execute([$user['empresa_id']]);
$empresa = $stmt->fetch();

// Actualizar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE empresas SET nombre=?, ruc=?, direccion=?, telefono=?, email=? WHERE id=?");
    $stmt->execute([$_POST['nombre'], $_POST['ruc'], $_POST['direccion'], $_POST['telefono'], $_POST['email'], $user['empresa_id']]);
    header("Location: configuracion.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Configuración Empresa</title></head>
<body>
<h2>Configuración de la Empresa</h2>
<form method="post">
    Nombre: <input name="nombre" value="<?= htmlspecialchars($empresa['nombre']) ?>" required><br>
    RUC: <input name="ruc" value="<?= htmlspecialchars($empresa['ruc']) ?>"><br>
    Dirección: <input name="direccion" value="<?= htmlspecialchars($empresa['direccion']) ?>"><br>
    Teléfono: <input name="telefono" value="<?= htmlspecialchars($empresa['telefono']) ?>"><br>
    Email: <input name="email" value="<?= htmlspecialchars($empresa['email']) ?>"><br>
    <button type="submit">Guardar</button>
</form>
</body>
</html>