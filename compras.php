<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

$proveedores = $pdo->prepare("SELECT * FROM proveedores WHERE empresa_id=?");
$proveedores->execute([$user['empresa_id']]);
$proveedores = $proveedores->fetchAll();

$productos = $pdo->prepare("SELECT * FROM productos WHERE empresa_id=?");
$productos->execute([$user['empresa_id']]);
$productos = $productos->fetchAll();

// CREAR COMPRA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $proveedor_id = $_POST['proveedor_id'];
    $total = 0;
    foreach ($_POST['producto_id'] as $i => $prod_id) {
        $total += $_POST['cantidad'][$i] * $_POST['precio'][$i];
    }
    $pdo->prepare("INSERT INTO compras (proveedor_id, empresa_id, usuario_id, total) VALUES (?, ?, ?, ?)")
        ->execute([$proveedor_id, $user['empresa_id'], $user['id'], $total]);
    $compra_id = $pdo->lastInsertId();
    foreach ($_POST['producto_id'] as $i => $prod_id) {
        $pdo->prepare("INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)")
            ->execute([$compra_id, $prod_id, $_POST['cantidad'][$i], $_POST['precio'][$i]]);
        // Actualizar stock
        $pdo->prepare("UPDATE productos SET stock = stock + ? WHERE id=?")->execute([$_POST['cantidad'][$i], $prod_id]);
    }
}

// ELIMINAR COMPRA
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM compras WHERE id=? AND empresa_id=?")->execute([$_GET['delete'], $user['empresa_id']]);
    $pdo->prepare("DELETE FROM detalle_compra WHERE compra_id=?")->execute([$_GET['delete']]);
}

// LISTAR COMPRAS
$stmt = $pdo->prepare("SELECT c.*, p.nombre AS proveedor FROM compras c LEFT JOIN proveedores p ON c.proveedor_id=p.id WHERE c.empresa_id=? ORDER BY c.id DESC");
$stmt->execute([$user['empresa_id']]);
$compras = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Compras</title></head>
<body>
<h2>Compras</h2>
<form method="post">
    Proveedor:
    <select name="proveedor_id" required>
        <?php foreach($proveedores as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach ?>
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
    <button name="add" type="submit">Registrar compra</button>
</form>
<hr>
<table border="1">
<tr><th>ID</th><th>Proveedor</th><th>Total</th><th>Fecha</th><th>Acciones</th></tr>
<?php foreach($compras as $c): ?>
<tr>
    <td><?= $c['id'] ?></td>
    <td><?= htmlspecialchars($c['proveedor']) ?></td>
    <td>$<?= number_format($c['total'],2) ?></td>
    <td><?= $c['fecha'] ?></td>
    <td>
        <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</a>
    </td>
</tr>
<?php endforeach ?>
</table>
</body>
</html>