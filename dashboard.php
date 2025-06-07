<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
$empresa_id = $user['empresa_id'];

// Resúmenes para tarjetas
$count = function($tabla) use ($pdo, $empresa_id) {
    $campo_empresa = in_array($tabla, ['usuarios','empresas','roles']) ? '1=1' : 'empresa_id=?';
    $stmt = $pdo->prepare("SELECT COUNT(*) as c FROM $tabla WHERE $campo_empresa");
    $stmt->execute(in_array($tabla, ['usuarios','empresas','roles']) ? [] : [$empresa_id]);
    return $stmt->fetch()['c'] ?? 0;
};
$productos = $count('productos');
$usuarios = $count('usuarios');
$clientes = $count('clientes');
$facturas = $count('facturas');

// Facturas por mes (gráfico)
$stmt = $pdo->prepare("SELECT DATE_FORMAT(fecha, '%Y-%m') as mes, COUNT(*) as total FROM facturas WHERE empresa_id=? GROUP BY mes ORDER BY mes DESC LIMIT 6");
$stmt->execute([$empresa_id]);
$datos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$labels = array_reverse(array_keys($datos));
$data = array_reverse(array_values($datos));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .card-icon { font-size:2.5rem; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2 class="mb-4">Panel de Control</h2>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="card-icon mb-1"><i class="bi bi-box-seam"></i></div>
                    <h5 class="card-title"><?= $productos ?></h5>
                    <p class="card-text">Productos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="card-icon mb-1"><i class="bi bi-people"></i></div>
                    <h5 class="card-title"><?= $usuarios ?></h5>
                    <p class="card-text">Usuarios</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="card-icon mb-1"><i class="bi bi-person"></i></div>
                    <h5 class="card-title"><?= $clientes ?></h5>
                    <p class="card-text">Clientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="card-icon mb-1"><i class="bi bi-receipt"></i></div>
                    <h5 class="card-title"><?= $facturas ?></h5>
                    <p class="card-text">Facturas</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">Facturas por mes</div>
      <div class="card-body">
        <canvas id="facturasChart" height="90"></canvas>
      </div>
    </div>
    <div class="mb-4">
    <?php if ($user['rol'] === 'superadmin'): ?>
        <div class="alert alert-primary">Acceso total a la plataforma y gestión de empresas.</div>
    <?php elseif ($user['rol'] === 'admin'): ?>
        <div class="alert alert-success">Administra tu empresa, usuarios y operaciones.</div>
    <?php elseif ($user['rol'] === 'ventas'): ?>
        <div class="alert alert-info">Acceso a clientes y facturación.</div>
    <?php elseif ($user['rol'] === 'almacen'): ?>
        <div class="alert alert-warning">Gestiona inventario, compras y proveedores.</div>
    <?php else: ?>
        <div class="alert alert-secondary">Panel de usuario estándar.</div>
    <?php endif ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('facturasChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Facturas',
            data: <?= json_encode($data) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
        }]
    },
    options: {scales: {y: { beginAtZero: true }}}
});
</script>
</body>
</html>