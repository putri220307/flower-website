<?php
include '../includes/header.php';
require '../config/database.php';


$comment_id = $_GET['id'] ?? 0;
$product_id = $_GET['product_id'] ?? 0;

// Get the comment data
$stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ? AND user_id = ?");
$stmt->execute([$comment_id, $_SESSION['user_id']]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    header("Location: comments.php?id=" . $product_id);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_comment = $_POST['comment'] ?? '';
    
    if (!empty($new_comment)) {
        $stmt = $pdo->prepare("UPDATE comments SET comment = ? WHERE id = ?");
        $stmt->execute([$new_comment, $comment_id]);
        
        header("Location: comments.php?id=" . $product_id);
        exit;
    }
}
?>

<main>
    <div class="container">
        <h1>Edit Comment</h1>
        
        <form method="POST">
            <div class="form-group">
                <textarea name="comment" required><?= htmlspecialchars($comment['comment']) ?></textarea>
            </div>
            <div class="form-actions">
                <a href="comments.php?id=<?= $product_id ?>" class="cancel-btn">Cancel</a>
                <button type="submit" class="save-btn">Save Changes</button>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>