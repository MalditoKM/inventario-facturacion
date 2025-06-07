<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO clientes (nombre, cedula, telefono, correo, direccion, empresa_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['nombre'], $_POST['cedula'], $_POST['telefono'], $_POST['correo'], $_POST['direccion'], $user['empresa_id']]);
}

// DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id=? AND empresa_id=?");
    $stmt->execute([$_GET['delete'], $user['empresa_id']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $pdo->prepare("UPDATE clientes SET nombre=?, cedula=?, telefono=?, correo=?, direccion=? WHERE id=? AND empresa_id=?");
    $stmt->execute([$_POST['nombre'], $_POST['cedula'], $_POST['telefono'], $_POST['correo'], $_POST['direccion'], $_POST['edit_id'], $user['empresa_id']]);
}

// READ
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE empresa_id=?");
$stmt->execute([$user['empresa_id']]);
$clientes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Clientes</title></head>
<body>
<h2>Clientes</h2>
<form method="post">
    Nombre: <input name="nombre" required>
    Cédula: <input name="cedula">
    Teléfono: <input name="telefono">
    Correo: <input name="correo">
    Dirección: <input name="direccion">
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Correo</th><th>Dirección</th><th>Acciones</th></tr>
<?php foreach($clientes as $c): ?>
<tr>
 <form method="post">
    <td><?= $c['id'] ?></td>
    <td><input name="nombre" value="<?= htmlspecialchars($c['nombre']) ?>"></td>
    <td><input name="cedula" value="<?= htmlspecialchars($c['cedula']) ?>"></td>
    <td><input name="telefono" value="<?= htmlspecialchars($c['telefono']) ?>"></td>
    <td><input name="correo" value="<?= htmlspecialchars($c['correo']) ?>"></td>
    <td><input name="direccion" value="<?= htmlspecialchars($c['direccion']) ?>"></td>
    <td>
        <input type="hidden" name="edit_id" value="<?= $c['id'] ?>">
        <button type="submit">Editar</button>
        <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
    </td>
 </form>
</tr>
<?php endforeach ?>
</table>
</body>
</html>