<?php
require 'config/db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $pdo->prepare("UPDATE usuarios SET reset_token=?, reset_expires=? WHERE id=?")
            ->execute([$token, $expires, $user['id']]);
        @mail($user['email'],
            "Recupera tu contraseña",
            "Haz click aquí para cambiar tu contraseña: https://TUSITIO.COM/reset.php?token=$token");
    }
    $msg = "Si el correo existe, se envió un enlace para restablecer la contraseña.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Recuperar Contraseña</h2>
    <form method="post" class="card card-body">
        <div class="mb-3">
            <label>Email:</label>
            <input name="email" type="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Recuperar</button>
    </form>
    <?php if($msg) echo "<div class='alert alert-info mt-3'>$msg</div>"; ?>
</div>
</body>
</html>