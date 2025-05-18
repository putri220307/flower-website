<?php
// Pastikan path ini sesuai dengan struktur folder Anda
require __DIR__.'/../config/database.php';

// Setup SSE (Server-Sent Events)
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Fungsi untuk kirim event
function sendEvent($msg) {
    echo "data: $msg\n\n";
    ob_flush();
    flush();
}

// Cek session admin (sesuaikan dengan sistem Anda)
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    sendEvent('error:Unauthorized');
    exit;
}

// Config
$lastChecked = time();
$maxExecutionTime = 60; // 1 menit

// Main loop
while (true) {
    if (connection_aborted()) break;
    
    // 1. Cek user baru
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM users 
        WHERE created_at >= FROM_UNIXTIME(?)
    ");
    $stmt->execute([$lastChecked]);
    $newUsers = $stmt->fetch()['count'];
    
    // 2. Cek admin login
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM admin_logs 
        WHERE login_time >= FROM_UNIXTIME(?)
    ");
    $stmt->execute([$lastChecked]);
    $newLogins = $stmt->fetch()['count'];
    
    // Jika ada aktivitas baru
    if ($newUsers > 0 || $newLogins > 0) {
        sendEvent('update');
        $lastChecked = time(); // Reset waktu cek
    }
    
    // Batasi waktu eksekusi
    if (time() - $_SERVER['REQUEST_TIME'] > $maxExecutionTime) {
        break;
    }
    
    sleep(3); // Cek setiap 3 detik
}
?>