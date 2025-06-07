<?php
session_start();
require 'config/db.php';

if (isset($_SESSION['user'])) {
    header('Location: menu.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email y contraseña son requeridos.";
    } else {
        $stmt = $pdo->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.email=? AND u.activo=1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerar ID de sesión para prevenir fijación de sesión
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'email' => $user['email'],
                'rol' => $user['rol_nombre'], // Usar el nombre del rol obtenido del JOIN
                'empresa_id' => $user['empresa_id']
            ];
            header('Location: menu.php');
            exit;
        } else {
            $error = "Credenciales incorrectas o usuario inactivo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            width: 100%;
            max-width: 420px;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
            color: #212529;
        }
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .error-message {
            color: #dc3545;
            background-color: #f8d7da;
            border-color: #f5c2c7;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="text-center mb-4">
            <i class="bi bi-boxes" style="font-size: 3rem; color: #0d6efd;"></i>
        </div>
        <h2 class="text-center">Iniciar Sesión</h2>
        
        <?php if($error): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
            </div>
        </form>
        <p class="mt-4 text-center"><a href="register_empresa.php">Registrar nueva empresa</a></p>
        <p class="mt-2 text-center text-muted"><a href="recuperar.php">¿Olvidaste tu contraseña?</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>