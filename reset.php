<?php
require 'config/db.php';
$msg = '';
if (isset($_GET['token'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE reset_token=? AND reset_expires>=NOW()");
    $stmt->execute([$_GET['token']]);
    $user = $stmt->fetch();
    if (!$user) die("Token inválido o expirado.");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE usuarios SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?")
            ->execute([$pass, $user['id']]);
        $msg = "Contraseña actualizada. <a href='login.php'>Iniciar sesión</a>";
    }
} else {
    die("Token requerido.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Restablecer Contraseña</h2>
    <?php if($msg) { echo "<div class='alert alert-success'>$msg</div>"; exit; } ?>
    <form method="post" class="card card-body">
        <div class="mb-3">
            <label>Nueva contraseña:</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Cambiar</button>
    </form>
</div>
</body>
</html>