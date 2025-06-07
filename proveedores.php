<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, telefono, correo, direccion, empresa_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['correo'], $_POST['direccion'], $user['empresa_id']]);
}

// DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM proveedores WHERE id=? AND empresa_id=?");
    $stmt->execute([$_GET['delete'], $user['empresa_id']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE proveedores SET nombre=?, telefono=?, correo=?, direccion=? WHERE id=? AND empresa_id=?");
    $stmt->execute([$_POST['nombre'], $_POST['telefono'], $_POST['correo'], $_POST['direccion'], $_POST['edit_id'], $user['empresa_id']]);
}

// READ
$stmt = $pdo->prepare("SELECT * FROM proveedores WHERE empresa_id=?");
$stmt->execute([$user['empresa_id']]);
$proveedores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Proveedores</title></head>
<body>
<h2>Proveedores</h2>
<form method="post">
    Nombre: <input name="nombre" required>
    Teléfono: <input name="telefono">
    Correo: <input name="correo">
    Dirección: <input name="direccion">
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Dirección</th><th>Acciones</th></tr>
<?php foreach($proveedores as $p): ?>
<tr>
 <form method="post">
    <td><?= $p['id'] ?></td>
    <td><input name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>"></td>
    <td><input name="telefono" value="<?= htmlspecialchars($p['telefono']) ?>"></td>
    <td><input name="correo" value="<?= htmlspecialchars($p['correo']) ?>"></td>
    <td><input name="direccion" value="<?= htmlspecialchars($p['direccion']) ?>"></td>
    <td>
        <input type="hidden" name="edit_id" value="<?= $p['id'] ?>">
        <button type="submit">Editar</button>
        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
    </td>
 </form>
</tr>
<?php endforeach ?>
</table>
</body>
</html>