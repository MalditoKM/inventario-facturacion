<?php
require 'config/db.php';
$msg = '';
if (isset($_GET['token'], $_GET['email'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND token=? AND aprobado=0 AND password IS NULL");
    $stmt->execute([$_GET['email'], $_GET['token']]);
    $user = $stmt->fetch();
    if (!$user) die("Invitación inválida o ya usada.");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE usuarios SET password=?, token=NULL WHERE id=?")
            ->execute([$password, $user['id']]);
        $msg = "Registro completado. Espera aprobación del administrador.";
    }
} else {
    die("Invitación inválida.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Completar Registro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Completa tu registro</h2>
    <?php if($msg) { echo "<div class='alert alert-success'>$msg</div>"; exit; } ?>
    <form method="post" class="card card-body">
        <div class="mb-3">
            <label>Email:</label>
            <input class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Nombre:</label>
            <input class="form-control" value="<?= htmlspecialchars($user['nombre']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label>Contraseña:</label>
            <input name="password" type="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Crear cuenta</button>
    </form>
</div>
</body>
</html>