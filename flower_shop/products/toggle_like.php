<?php
session_start();
require '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to like products']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is missing']);
    exit;
}

$product_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if like exists
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $like = $stmt->fetch();

    if ($like) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'success', 'action' => 'unliked']);
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'success', 'action' => 'liked']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}