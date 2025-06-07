<?php
session_start();
if (!isset($_SESSION['user'])) header('Location: login.php');
?>
<!DOCTYPE html>
<html>
<head><title>Administrativos</title></head>
<body>
<h2>Módulo Administrativo</h2>
<p>Este módulo puede ser usado para reportes, gestión de permisos, auditoría, y otras tareas administrativas específicas de tu empresa.<br>
(Implementa aquí las funciones administrativas que requieras)</p>
</body>
</html>