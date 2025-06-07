<?php
session_start();
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];

// Ejemplo de notificaciones dinámicas
require_once 'config/db.php';
$notificaciones = [
    'bajo_stock' => 0,
    'facturas_pendientes' => 0,
];
// Solo para roles que pueden ver productos/facturas
if (in_array($user['rol'], ['admin', 'superadmin', 'almacen', 'ventas'])) {
    // Productos con stock <= 5
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE empresa_id=? AND stock<=5");
    $stmt->execute([$user['empresa_id']]);
    $notificaciones['bajo_stock'] = $stmt->fetchColumn();

    // Facturas pendientes (si tienes un campo 'pendiente', si no, omite esto)
    // $stmt = $pdo->prepare("SELECT COUNT(*) FROM facturas WHERE empresa_id=? AND estado='pendiente'");
    // $stmt->execute([$user['empresa_id']]);
    // $notificaciones['facturas_pendientes'] = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menú principal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body { background: #f8f9fa; }
    .sidebar { min-width: 230px; max-width: 230px; min-height: 100vh; background: #212529; color: #fff; }
    .sidebar a { color: #fff; }
    .sidebar .nav-link.active { background: #495057; }
    .sidebar .bi { font-size: 1.2rem; }
    .sidebar .badge { background: #ffc107; color: #212529; }
    .sidebar .submenu { padding-left: 1.5rem; }
    .sidebar-header { font-size: 1.3rem; font-weight: bold; padding: 1rem 1rem 0.5rem 1rem; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar flex-column p-3">
        <div class="sidebar-header mb-3">
            <i class="bi bi-boxes"></i> Inventario
        </div>
        <ul class="nav nav-pills flex-column mb-auto">
            <li>
                <a href="menu.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : '' ?>">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
            </li>
            <?php if (in_array($user['rol'], ['admin', 'superadmin', 'almacen'])): ?>
            <li>
                <a href="productos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : '' ?>">
                    <i class="bi bi-bag"></i> Productos
                    <?php if ($notificaciones['bajo_stock']): ?>
                        <span class="badge rounded-pill"><?= $notificaciones['bajo_stock'] ?></span>
                    <?php endif ?>
                </a>
            </li>
            <li>
                <a href="categorias.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categorias.php' ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i> Categorías
                </a>
            </li>
            <?php endif; ?>
            <?php if (in_array($user['rol'], ['admin', 'superadmin', 'ventas'])): ?>
            <li>
                <a href="clientes.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'clientes.php' ? 'active' : '' ?>">
                    <i class="bi bi-person"></i> Clientes
                </a>
            </li>
            <li>
                <a href="facturas.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'facturas.php' ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i> Facturación
                    <?php if ($notificaciones['facturas_pendientes']): ?>
                        <span class="badge rounded-pill"><?= $notificaciones['facturas_pendientes'] ?></span>
                    <?php endif ?>
                </a>
            </li>
            <?php endif; ?>
            <?php if (in_array($user['rol'], ['admin', 'superadmin', 'almacen'])): ?>
            <li>
                <a href="proveedores.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'proveedores.php' ? 'active' : '' ?>">
                    <i class="bi bi-truck"></i> Proveedores
                </a>
            </li>
            <li>
                <a href="compras.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'compras.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart"></i> Compras
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="mantenimientos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'mantenimientos.php' ? 'active' : '' ?>">
                    <i class="bi bi-tools"></i> Mantenimientos
                </a>
            </li>
            <?php if (in_array($user['rol'], ['admin', 'superadmin'])): ?>
            <li>
                <a class="nav-link" data-bs-toggle="collapse" href="#adminSubmenu" role="button" aria-expanded="false" aria-controls="adminSubmenu">
                    <i class="bi bi-gear"></i> Administración <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse submenu" id="adminSubmenu">
                    <a href="usuarios.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Usuarios</a>
                    <a href="configuracion.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'configuracion.php' ? 'active' : '' ?>"><i class="bi bi-building"></i> Configuración empresa</a>
                </div>
            </li>
            <?php endif; ?>
            <?php if ($user['rol'] == 'superadmin'): ?>
            <li>
                <a class="nav-link" data-bs-toggle="collapse" href="#superSubmenu" role="button" aria-expanded="false" aria-controls="superSubmenu">
                    <i class="bi bi-shield-lock"></i> Superadmin <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse submenu" id="superSubmenu">
                    <a href="empresas.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'empresas.php' ? 'active' : '' ?>"><i class="bi bi-diagram-3"></i> Empresas</a>
                    <a href="roles.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'roles.php' ? 'active' : '' ?>"><i class="bi bi-person-badge"></i> Roles</a>
                </div>
            </li>
            <?php endif; ?>
            <li>
                <a href="administrativos.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'administrativos.php' ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart"></i> Administrativos
                </a>
            </li>
            <li>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </li>
        </ul>
        <div class="mt-5 ps-2 small text-muted">Versión 1.0</div>
    </nav>
    <div class="flex-fill p-4">
        <!-- Aquí va el contenido de cada módulo -->