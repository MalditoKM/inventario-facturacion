<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO mantenimientos (vehiculo, descripcion, costo, empresa_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['vehiculo'], $_POST['descripcion'], $_POST['costo'], $user['empresa_id']]);
}

// DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM mantenimientos WHERE id=? AND empresa_id=?");
    $stmt->execute([$_GET['delete'], $user['empresa_id']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE mantenimientos SET vehiculo=?, descripcion=?, costo=? WHERE id=? AND empresa_id=?");
    $stmt->execute([$_POST['vehiculo'], $_POST['descripcion'], $_POST['costo'], $_POST['edit_id'], $user['empresa_id']]);
}

// READ
$stmt = $pdo->prepare("SELECT * FROM mantenimientos WHERE empresa_id=?");
$stmt->execute([$user['empresa_id']]);
$mantenimientos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Mantenimientos</title></head>
<body>
<h2>Mantenimientos de Vehículos</h2>
<form method="post">
    Vehículo: <input name="vehiculo" required>
    Descripción: <input name="descripcion">
    Costo: <input name="costo" type="number" step="0.01">
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Vehículo</th><th>Descripción</th><th>Costo</th><th>Acciones</th></tr>
<?php foreach($mantenimientos as $m): ?>
<tr>
 <form method="post">
    <td><?= $m['id'] ?></td>
    <td><input name="vehiculo" value="<?= htmlspecialchars($m['vehiculo']) ?>"></td>
    <td><input name="descripcion" value="<?= htmlspecialchars($m['descripcion']) ?>"></td>
    <td><input name="costo" type="number" step="0.01" value="<?= $m['costo'] ?>"></td>
    <td>
        <input type="hidden" name="edit_id" value="<?= $m['id'] ?>">
        <button type="submit">Editar</button>
        <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
    </td>
 </form>
</tr>
<?php endforeach ?>
</table>
</body>
</html>