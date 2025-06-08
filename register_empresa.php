<?php
session_start();
require 'config/db.php';

$error = $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registrar empresa
    $pdo->prepare("INSERT INTO empresas (nombre, ruc, direccion, telefono, email) VALUES (?, ?, ?, ?, ?)")
        ->execute([$_POST['nombre'], $_POST['ruc'], $_POST['direccion'], $_POST['telefono'], $_POST['email']]);
    $empresa_id = $pdo->lastInsertId();

    // Crear admin
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    // Obtener el rol admin
    $rol = $pdo->query("SELECT id FROM roles WHERE nombre='admin'")->fetch();
    $pdo->prepare("INSERT INTO usuarios (nombre, email, password, empresa_id, rol_id) VALUES (?, ?, ?, ?, ?)")
        ->execute([$_POST['admin_nombre'], $_POST['admin_email'], $password, $empresa_id, $rol['id']]);
    $msg = 'Empresa y usuario administrador creados. Ya puedes iniciar sesión.';
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="style.css">
<head><title>Registrar Empresa</title></head>
<body>
<h2>Registrar Empresa</h2>
<?php if($msg): ?><p style="color:green"><?= $msg ?></p><?php endif; ?>
<form method="post">
    <h3>Datos de la Empresa</h3>
    Nombre: <input name="nombre" required><br>
    RUC: <input name="ruc"><br>
    Dirección: <input name="direccion"><br>
    Teléfono: <input name="telefono"><br>
    Email: <input name="email"><br>
    <h3>Usuario Administrador</h3>
    Nombre: <input name="admin_nombre" required><br>
    Email: <input name="admin_email" type="email" required><br>
    Contraseña: <input name="password" type="password" required><br>
    <button type="submit">Registrar</button>
</form>
<p><a href="login.php">Volver a login</a></p>
</body>
</html>