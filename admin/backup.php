<?php
require '../includes/session.php';
if ($_SESSION['user']['rol_id'] != 1) die("Solo el super admin puede hacer backup");
$filename = "backup_" . date("Ymd_His") . ".sql";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename");
passthru("mysqldump -u root inventario_facturacion");
exit;
?>