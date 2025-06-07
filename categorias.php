<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $pdo->prepare("INSERT INTO categorias (nombre, empresa_id) VALUES (?, ?)")->execute([$_POST['nombre'], $user['empresa_id']]);
}

// DELETE
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM categorias WHERE id=? AND empresa_id=?")->execute([$_GET['delete'], $user['empresa_id']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $pdo->prepare("UPDATE categorias SET nombre=? WHERE id=? AND empresa_id=?")->execute([$_POST['nombre'], $_POST['edit_id'], $user['empresa_id']]);
}

// READ
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE empresa_id=?");
$stmt->execute([$user['empresa_id']]);
$categorias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Categorías</title></head>
<body>
<h2>Categorías</h2>
<form method="post">
    Nombre: <input name="nombre" required>
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr>
<?php foreach($categorias as $c): ?>
<tr>
 <form method="post">
    <td><?= $c['id'] ?></td>
    <td><input name="nombre" value="<?= htmlspecialchars($c['nombre']) ?>"></td>
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