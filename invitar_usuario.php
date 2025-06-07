<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
if (!in_array($user['rol'], ['admin', 'superadmin'])) die('No autorizado');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    $rol_id = $_POST['rol_id'];
    $empresa_id = $user['empresa_id'];
    $token = bin2hex(random_bytes(16));
    $existe = $pdo->prepare("SELECT * FROM usuarios WHERE email=?");
    $existe->execute([$email]);
    if ($existe->fetch()) {
        $msg = "Ese email ya existe.";
    } else {
        $pdo->prepare("INSERT INTO usuarios (nombre, email, empresa_id, rol_id, token, aprobado, activo) VALUES (?, ?, ?, ?, ?, 0, 1)")
            ->execute([$nombre, $email, $empresa_id, $rol_id, $token]);
        @mail($email, "Invitación a sistema", 
            "Te han invitado, completa tu registro aquí: " .
            "https://TUSITIO.COM/registro_inv.php?token=$token&email=$email");
        $msg = "Invitación enviada.";
    }
}
$roles = $pdo->query("SELECT * FROM roles WHERE nombre!='superadmin'")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invitar Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Invitar Usuario</h2>
    <?php if($msg) echo "<div class='alert alert-info'>$msg</div>"; ?>
    <form method="post" class="card card-body">
        <div class="mb-3">
            <label>Nombre</label>
            <input name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input name="email" type="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Rol</label>
            <select name="rol_id" class="form-select">
                <?php foreach($roles as $r) echo "<option value='{$r['id']}'>".htmlspecialchars($r['nombre'])."</option>"; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Invitar</button>
    </form>
</div>
</body>
</html>