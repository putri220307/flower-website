<?php
session_start();

// Hapus semua data session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman sebelumnya atau home
$redirect = $_SERVER['HTTP_REFERER'] ?? '/flower_shop/index.php';
header("Location: $redirect");
exit;
?>