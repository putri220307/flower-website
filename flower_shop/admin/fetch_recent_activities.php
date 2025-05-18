<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

try {
    $stmt = $pdo->query("
        (SELECT 
            id, 
            username, 
            created_at, 
            'user_baru' AS type
        FROM users 
        ORDER BY created_at DESC 
        LIMIT 10)
        
        UNION ALL
        
        (SELECT 
            id, 
            admin_name AS username, 
            login_time AS created_at, 
            'admin_login' AS type
        FROM admin_logs 
        ORDER BY login_time DESC 
        LIMIT 10)
        
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}