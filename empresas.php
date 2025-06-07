<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
if ($user['rol'] !== 'superadmin') die('No autorizado');

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $pdo->prepare("INSERT INTO empresas (nombre, ruc, direccion, telefono, email) VALUES (?, ?, ?, ?, ?)")
        ->execute([$_POST['nombre'], $_POST['ruc'], $_POST['direccion'], $_POST['telefono'], $_POST['email']]);
}

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM empresas WHERE id=?")->execute([$_GET['delete']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $pdo->prepare("UPDATE empresas SET nombre=?, ruc=?, direccion=?, telefono=?, email=? WHERE id=?")
        ->execute([$_POST['nombre'], $_POST['ruc'], $_POST['direccion'], $_POST['telefono'], $_POST['email'], $_POST['edit_id']]);
}

// READ
$empresas = $pdo->query("SELECT * FROM empresas")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Empresas</title></head>
<body>
<h2>Empresas</h2>
<form method="post">
    Nombre: <input name="nombre" required>
    RUC: <input name="ruc">
    Dirección: <input name="direccion">
    Teléfono: <input name="telefono">
    Email: <input name="email">
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Nombre</th><th>RUC</th><th>Dirección</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr>
<?php foreach($empresas as $e): ?>
<tr>
 <form method="post">
    <td><?= $e['id'] ?></td>
    <td><input name="nombre" value="<?= htmlspecialchars($e['nombre']) ?>"></td>
    <td><input name="ruc" value="<?= htmlspecialchars($e['ruc']) ?>"></td>
    <td><input name="direccion" value="<?= htmlspecialchars($e['direccion']) ?>"></td>
    <td><input name="telefono" value="<?= htmlspecialchars($e['telefono']) ?>"></td>
    <td><input name="email" value="<?= htmlspecialchars($e['email']) ?>"></td>
    <td>
        <input type="hidden" name="edit_id" value="<?= $e['id'] ?>">
        <button type="submit">Editar</button>
        <a href="?delete=<?= $e['id'] ?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
    </td>
 </form>
</tr>
<?php endforeach ?>
</table>
</body>
</html>