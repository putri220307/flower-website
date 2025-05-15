<?php
// File: /admin/auth/check-login.php
session_start();

// Redirect ke halaman login jika tidak ada session admin
if (!isset($_SESSION['admin_loggedin'])) {
    header("Location: ../login.php");
    exit;
}

// Optional: Validasi tambahan (contoh: cek username di database)
require '../config/database.php';

if (isset($_SESSION['admin_username'])) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$_SESSION['admin_username']]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        // Jika username tidak ditemukan di database
        session_unset();
        session_destroy();
        header("Location: ../login.php?error=invalid_session");
        exit;
    }
}
?>