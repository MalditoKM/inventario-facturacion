<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .side-nav {
            min-height: 100vh;
            min-width: 220px;
            background: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        .side-nav .nav-link.active {
            background: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="side-nav d-flex flex-column p-3">
        <a href="dashboard.php" class="nav-link mb-2"><i class="bi bi-bar-chart-fill"></i> Dashboard</a>
        <a href="usuarios.php" class="nav-link mb-2"><i class="bi bi-people-fill"></i> Usuarios</a>
        <a href="invitar_usuario.php" class="nav-link mb-2"><i class="bi bi-envelope-plus-fill"></i> Invitar usuario</a>
        <a href="recuperar.php" class="nav-link mb-2"><i class="bi bi-key-fill"></i> Recuperar contraseña</a>
        <a href="logout.php" class="nav-link mt-auto text-danger"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </nav>
</body>
</html>