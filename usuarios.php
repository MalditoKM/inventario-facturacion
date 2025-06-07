<?php
session_start();
require 'config/db.php';
if (!isset($_SESSION['user'])) header('Location: login.php');
$user = $_SESSION['user'];
$es_admin = in_array($user['rol'], ['admin', 'superadmin']);

# --- Borrar usuario ---
if ($es_admin && isset($_GET['borrar'])) {
    $id = intval($_GET['borrar']);
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    if ($u && ($user['rol']=='superadmin' || ($u['empresa_id']==$user['empresa_id'] && $u['rol_id']!=1))) {
        $pdo->prepare("DELETE FROM usuarios WHERE id=?")->execute([$id]);
        header("Location: usuarios.php?msg=Usuario+borrado");
        exit;
    }
}

# --- Editar usuario ---
$editando = null;
if ($es_admin && isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
    $stmt->execute([$id]);
    $editando = $stmt->fetch();
    if (!$editando) { header("Location: usuarios.php"); exit;}
}
if ($es_admin && isset($_POST['editar_usuario'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol_id = intval($_POST['rol_id']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $id = intval($_POST['id']);
    $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, rol_id=?, activo=? WHERE id=?")
        ->execute([$nombre, $email, $rol_id, $activo, $id]);
    header("Location: usuarios.php?msg=Usuario+actualizado");
    exit;
}

# --- Aprobar usuario ---
if ($es_admin && isset($_GET['aprobar'])) {
    $pdo->prepare("UPDATE usuarios SET aprobado=1 WHERE id=?")->execute([intval($_GET['aprobar'])]);
    header("Location: usuarios.php?msg=Usuario+aprobado");
    exit;
}

# --- Activar/Desactivar usuario ---
if ($es_admin && isset($_GET['cambiar_estado'])) {
    $id = intval($_GET['cambiar_estado']);
    $stmt = $pdo->prepare("SELECT activo FROM usuarios WHERE id=?");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    if ($u) {
        $nuevo_estado = $u['activo'] ? 0 : 1;
        $pdo->prepare("UPDATE usuarios SET activo=? WHERE id=?")->execute([$nuevo_estado, $id]);
    }
    header("Location: usuarios.php?msg=Estado+cambiado");
    exit;
}

# --- Restablecer contraseña (envía token por email) ---
if ($es_admin && isset($_GET['resetpass'])) {
    $id = intval($_GET['resetpass']);
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id=?");
    $stmt->execute([$id]);
    $user_reset = $stmt->fetch();
    if ($user_reset) {
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $pdo->prepare("UPDATE usuarios SET reset_token=?, reset_expires=? WHERE id=?")
            ->execute([$token, $expires, $id]);
        @mail($user_reset['email'],
            "Restablece tu contraseña",
            "Un admin ha solicitado restablecer tu contraseña. Haz click aquí: https://TUSITIO.COM/reset.php?token=$token");
    }
    header("Location: usuarios.php?msg=Enlace+de+restablecimiento+enviado");
    exit;
}

# --- Listado de usuarios ---
if ($user['rol']=='superadmin') {
    $stmt = $pdo->query("SELECT u.*, r.nombre as rol_nombre, e.nombre as empresa_nombre FROM usuarios u 
                         LEFT JOIN roles r ON u.rol_id=r.id 
                         LEFT JOIN empresas e ON u.empresa_id=e.id");
} else {
    $stmt = $pdo->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u 
                           LEFT JOIN roles r ON u.rol_id=r.id WHERE u.empresa_id=?");
    $stmt->execute([$user['empresa_id']]);
}
$usuarios = $stmt->fetchAll();
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h2>Gestión de Usuarios</h2>
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif ?>
    <?php if($editando): ?>
        <form method="post" class="card card-body mb-4">
            <input type="hidden" name="id" value="<?= $editando['id'] ?>">
            <div class="mb-2">
                <label>Nombre</label>
                <input name="nombre" class="form-control" value="<?= htmlspecialchars($editando['nombre']) ?>" required>
            </div>
            <div class="mb-2">
                <label>Email</label>
                <input name="email" class="form-control" value="<?= htmlspecialchars($editando['email']) ?>" required>
            </div>
            <div class="mb-2">
                <label>Rol</label>
                <select name="rol_id" class="form-select">
                    <?php foreach($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['id']==$editando['rol_id']?'selected':'' ?>>
                            <?= htmlspecialchars($r['nombre']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" name="activo" id="activo" <?= $editando['activo']?'checked':'' ?>>
                <label for="activo" class="form-check-label">Activo</label>
            </div>
            <button type="submit" name="editar_usuario" class="btn btn-primary">Guardar cambios</button>
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    <?php endif; ?>
    <table class="table table-bordered table-striped bg-white">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <?php if ($user['rol']=='superadmin'): ?><th>Empresa</th><?php endif; ?>
                <th>Rol</th>
                <th>Aprobado</th>
                <th>Activo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <?php if ($user['rol']=='superadmin'): ?><td><?= htmlspecialchars($u['empresa_nombre']) ?></td><?php endif; ?>
                <td><?= htmlspecialchars($u['rol_nombre']) ?></td>
                <td><?= $u['aprobado'] ? "Sí" : "No" ?>
                    <?php if (!$u['aprobado'] && $es_admin): ?>
                        <a href="?aprobar=<?= $u['id'] ?>" class="btn btn-sm btn-success">Aprobar</a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($es_admin): ?>
                        <a href="?cambiar_estado=<?= $u['id'] ?>" class="btn btn-sm <?= $u['activo']?'btn-success':'btn-secondary' ?>">
                            <?= $u['activo'] ? "Activo" : "Inactivo" ?>
                        </a>
                    <?php else: ?>
                        <?= $u['activo'] ? "Activo" : "Inactivo" ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($es_admin): ?>
                        <a href="?editar=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <?php if($u['id']!=$user['id']): ?>
                            <a href="?borrar=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro de borrar?')">Borrar</a>
                        <?php endif; ?>
                        <a href="?resetpass=<?= $u['id'] ?>" class="btn btn-sm btn-warning">Restablecer contraseña</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
</body>
</html>