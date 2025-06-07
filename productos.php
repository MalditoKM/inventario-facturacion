<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $categoria_id = $_POST['categoria_id'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $empresa_id = $user['empresa_id'];

    $imagen = null;
    if ($_FILES['imagen']['name']) {
        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/$filename");
        $imagen = $filename;
    }
    $codigo_barras = str_pad(rand(1, 999999999999), 12, "0", STR_PAD_LEFT);

    $pdo->prepare("INSERT INTO productos (nombre, descripcion, categoria_id, precio, stock, imagen, codigo_barras, empresa_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
        ->execute([$nombre, $descripcion, $categoria_id, $precio, $stock, $imagen, $codigo_barras, $empresa_id]);
}

// DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id=? AND empresa_id=?");
    $stmt->execute([$_GET['delete'], $user['empresa_id']]);
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $imagen = null;
    if ($_FILES['imagen']['name']) {
        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], "uploads/$filename");
        $imagen = $filename;
    }
    $query = "UPDATE productos SET nombre=?, descripcion=?, categoria_id=?, precio=?, stock=?";
    $params = [$_POST['nombre'], $_POST['descripcion'], $_POST['categoria_id'], $_POST['precio'], $_POST['stock']];
    if ($imagen) { $query .= ", imagen=?"; $params[] = $imagen; }
    $query .= " WHERE id=? AND empresa_id=?";
    $params[] = $_POST['edit_id'];
    $params[] = $user['empresa_id'];
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
}

// READ
$stmt = $pdo->prepare("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id=c.id WHERE p.empresa_id=?");
$stmt->execute([$user['empresa_id']]);
$productos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Productos</title></head>
<body>
<h2>Productos</h2>
<form method="post" enctype="multipart/form-data">
    Nombre: <input name="nombre" required>
    Descripción: <input name="descripcion">
    Categoría: <input name="categoria_id" type="number">
    Precio: <input name="precio" type="number" step="0.01">
    Stock: <input name="stock" type="number">
    Imagen: <input type="file" name="imagen">
    <button name="add" type="submit">Agregar</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Imagen</th><th>Código Barras</th><th>Acciones</th></tr>
<?php foreach($productos as $p): ?>
<tr>
 <form method="post" enctype="multipart/form-data">
    <td><?= $p['id'] ?></td>
    <td><input name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>"></td>
    <td><input name="descripcion" value="<?= htmlspecialchars($p['descripcion']) ?>"></td>
    <td><input name="categoria_id" value="<?= htmlspecialchars($p['categoria_id']) ?>"></td>
    <td><input name="precio" type="number" step="0.01" value="<?= $p['precio'] ?>"></td>
    <td><input name="stock" type="number" value="<?= $p['stock'] ?>"></td>
    <td>
        <?php if($p['imagen']): ?>
            <img src="uploads/<?= $p['imagen'] ?>" width="60"><br>
        <?php endif ?>
        <input type="file" name="imagen">
    </td>
    <td>
        <?= $p['codigo_barras'] ?><br>
        <img src="codigos_barras.php?code=<?= $p['codigo_barras'] ?>">
    </td>
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