<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Get and validate sort parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$order = ($sort === 'oldest') ? 'ASC' : 'DESC';

header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

try {
    // Query untuk mendapatkan aktivitas terbaru
    $query = "
        (SELECT 
            id, 
            username, 
            created_at, 
            'user_baru' AS type,
            NULL AS flower_name
        FROM users 
        WHERE is_verified = 1
        ORDER BY created_at $order 
        LIMIT 5)
        
        UNION ALL
        
        (SELECT 
            id, 
            admin_name AS username, 
            login_time AS created_at, 
            'admin_login' AS type,
            NULL AS flower_name
        FROM admin_logs 
        ORDER BY login_time $order 
        LIMIT 5)
        
        ORDER BY created_at $order
        LIMIT 10
    ";
    
    $stmt = $pdo->query($query);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug output (bisa dihapus setelah testing)
    error_log("Sort: $sort, Order: $order");
    error_log("Activities fetched: " . count($activities));
    
    echo json_encode($activities);
    
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}