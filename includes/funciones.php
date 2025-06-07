<?php
function notificar($msg, $url="/", $para_email=false) {
    $_SESSION['notificaciones'][] = ['msg'=>$msg, 'url'=>$url];
    if ($para_email) {
        // Requiere configuración de correo en tu servidor
        mail("destinatario@dominio.com", "Notificación", $msg." ".$url, "From: sistema@tudominio.com");
    }
}
?>