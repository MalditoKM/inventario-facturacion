<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

$clientes = $pdo->prepare("SELECT * FROM clientes WHERE empresa_id=?");
$clientes->execute([$user['empresa_id']]);
$clientes = $clientes->fetchAll();

$productos = $pdo->prepare("SELECT * FROM productos WHERE empresa_id=?");
$productos->execute([$user['empresa_id']]);
$productos = $productos->fetchAll();

// CREAR FACTURA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $cliente_id = $_POST['cliente_id'];
    $total = 0;
    foreach ($_POST['producto_id'] as $i => $prod_id) {
        $total += $_POST['cantidad'][$i] * $_POST['precio'][$i];
    }
    $codigo_barras = str_pad(rand(1, 999999999999), 12, "0", STR_PAD_LEFT);
    $pdo->prepare("INSERT INTO facturas (cliente_id, empresa_id, usuario_id, total, codigo_barras) VALUES (?, ?, ?, ?, ?)")
        ->execute([$cliente_id, $user['empresa_id'], $user['id'], $total, $codigo_barras]);
    $factura_id = $pdo->lastInsertId();
    foreach ($_POST['producto_id'] as $i => $prod_id) {
        $pdo->prepare("INSERT INTO detalle_factura (factura_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)")
            ->execute([$factura_id, $prod_id, $_POST['cantidad'][$i], $_POST['precio'][$i]]);
        // Actualizar stock
        $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id=?")->execute([$_POST['cantidad'][$i], $prod_id]);
    }
}

// ELIMINAR FACTURA
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM facturas WHERE id=? AND empresa_id=?")->execute([$_GET['delete'], $user['empresa_id']]);
    $pdo->prepare("DELETE FROM detalle_factura WHERE factura_id=?")->execute([$_GET['delete']]);
}

// LISTAR FACTURAS
$stmt = $pdo->prepare("SELECT f.*, c.nombre as cliente FROM facturas f LEFT JOIN clientes c ON f.cliente_id=c.id WHERE f.empresa_id=? ORDER BY f.id DESC");
$stmt->execute([$user['empresa_id']]);
$facturas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Facturas</title></head>
<body>
<h2>Facturas</h2>
<form method="post">
    Cliente:
    <select name="cliente_id" required>
        <?php foreach($clientes as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option><?php endforeach ?>
    </select>
    <hr>
    <table>
        <tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr>
        <?php for($i=0; $i<3; $i++): ?>
        <tr>
            <td>
                <select name="producto_id[]">
                    <?php foreach($productos as $prod): ?><option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nombre']) ?></option><?php endforeach ?>
                </select>
            </td>
            <td><input name="cantidad[]" type="number" min="0"></td>
            <td><input name="precio[]" type="number" step="0.01"></td>
        </tr>
        <?php endfor ?>
    </table>
    <button name="add" type="submit">Registrar factura</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Cliente</th><th>Total</th><th>Fecha</th><th>Código Barras</th><th>Acciones</th></tr>
<?php foreach($facturas as $f): ?>
<tr>
    <td><?= $f['id'] ?></td>
    <td><?= htmlspecialchars($f['cliente']) ?></td>
    <td>$<?= number_format($f['total'],2) ?></td>
    <td><?= $f['fecha'] ?></td>
    <td>
        <?= $f['codigo_barras'] ?><br>
        <img src="codigos_barras.php?code=<?= $f['codigo_barras'] ?>">
    </td>
    <td>
        <a href="?delete=<?= $f['id'] ?>" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</a>
    </td>
</tr>
<?php endforeach ?>
</table>
</body>
</html>