<?php
session_start();

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pengecekan session dengan redirect contextual
if (!isset($_SESSION['loggedin'], $_SESSION['user_id'], $_SESSION['username'])) {
    $product_id = $_GET['id'] ?? 0;
    header("Location: ../auth/login.php?from=comment&product_id=" . $product_id);
    exit;
}

require '../config/database.php';
include '../includes/header.php';

// Validasi product_id
$product_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if (!$product_id) {
    die("Invalid product ID");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['comment'] ?? '');
    
    if (!empty($comment)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (product_id, user_id, comment) VALUES (?, ?, ?)");
            $stmt->execute([$product_id, $_SESSION['user_id'], htmlspecialchars($comment)]);
            
            header("Location: comments.php?id=$product_id");
            exit;
        } catch (PDOException $e) {
            die("Error saving comment: " . $e->getMessage());
        }
    }
}

// Ambil data produk
try {
    $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header("Location: ../index.php");
        exit;
    }
    
    $product_name = $product['name'];
} catch (PDOException $e) {
    die("Error loading product: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Comment</title>
    <link rel="stylesheet" href="../assets/css/products.css">
</head>
<body>
    <main>
        <div class="container">
            <h1 class="add-comment-title">Add Comment for <?= htmlspecialchars($product_name) ?></h1>
            
            <form method="POST" class="comment-form">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                
                <div class="form-group">
                    <label for="comment">Your Comment</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="comments.php?id=<?= $product_id ?>" class="cancel-btn">Cancel</a>
                    <button type="submit" class="submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

<?php include '../includes/footer.php'; ?>