<?php
session_start();
require '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to save products']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is missing']);
    exit;
}

$product_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if save exists
    $stmt = $pdo->prepare("SELECT * FROM saved_products WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $saved = $stmt->fetch();

    if ($saved) {
        // Unsave
        $stmt = $pdo->prepare("DELETE FROM saved_products WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'success', 'action' => 'unsaved']);
    } else {
        // Save
        $stmt = $pdo->prepare("INSERT INTO saved_products (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'success', 'action' => 'saved']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}