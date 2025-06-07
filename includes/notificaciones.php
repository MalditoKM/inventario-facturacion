<?php
if (!empty($_SESSION['notificaciones'])) {
    foreach ($_SESSION['notificaciones'] as $n) {
        echo "<div class='alert alert-info'>{$n['msg']} <a href='{$n['url']}'>Ver</a></div>";
    }
    $_SESSION['notificaciones'] = [];
}
?>