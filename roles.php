<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
if ($user['rol'] !== 'superadmin') die('No autorizado');

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $pdo->prepare("INSERT INTO roles (nombre) VALUES (?)")->execute([$_POST['nombre']]);
}

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM roles WHERE id=?")->execute([$_GET['delete']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $pdo->prepare("UPDATE roles SET nombre=? WHERE id=?")->execute([$_POST['nombre'], $_POST['edit_id']]);
}

// READ
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Roles</title></head>
<body>
<h2>Roles</h2>
<form method="post">
    Nombre: <input name="nombre" required>
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>
<?php foreach($roles as $r): ?>
<tr>
 <form method="post">
    <td><?= $r['id'] ?></td>
    <td><input name="nombre" value="<?= htmlspecialchars($r['nombre']) ?>"></td>
    <td>
        <input type="hidden" name="edit_id" value="<?= $r['id'] ?>">
        <button type="submit">Editar</button>
        <a href="?delete=<?= $r['id'] ?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
    </td>
 </form>
</tr>
<?php endforeach ?>
</table>
</body>
</html>