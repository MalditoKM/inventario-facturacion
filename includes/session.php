<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$timeout = 1800;
if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active'] > $timeout)) {
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_active'] = time();
?>