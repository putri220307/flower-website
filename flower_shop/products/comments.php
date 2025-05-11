<?php
include '../includes/header.php';
require '../config/database.php';

// Ambil ID produk dari URL
$product_id = $_GET['id'] ?? 0;

// Query untuk mendapatkan nama produk
$stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit;
}

// Query untuk mendapatkan komentar
$sort = $_GET['sort'] ?? 'newest';
$orderBy = match($sort) {
    'a-z' => 'username ASC',
    'z-a' => 'username DESC',
    'oldest' => 'created_at ASC',
    default => 'created_at DESC'
};

$stmt = $pdo->prepare("
    SELECT comments.*, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.product_id = ? 
    ORDER BY $orderBy
");

$stmt->execute([$product_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="container">
        <h1 class="comments-title">Comment</h1>
        
        <!-- Filter Dropdown -->
        <div class="comments-filter">
            <div class="filter-dropdown">
                <button class="filter-btn">Sort by</button>
                <div class="dropdown-content">
                    <a href="?id=<?= $product_id ?>&sort=newest">Terbaru</a>
                    <a href="?id=<?= $product_id ?>&sort=oldest">Terlama</a>
                    <a href="?id=<?= $product_id ?>&sort=a-z">A-Z</a>
                    <a href="?id=<?= $product_id ?>&sort=z-a">Z-A</a>
                </div>
            </div>
        </div>
        
        <!-- Comments Table -->
        <div class="comments-table">
            <?php foreach ($comments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-number"><?= $comment['id'] ?></div>
                    <div class="comment-user"><?= htmlspecialchars($comment['username']) ?></div>
                    <div class="comment-text"><?= htmlspecialchars($comment['comment']) ?></div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($comments)): ?>
                <div class="no-comments">Belum ada komentar untuk produk ini.</div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="comments-actions">
            <a href="product-detail.php?id=<?= $product_id ?>" class="back-btn">Back</a>
            <a href="add-comment.php?id=<?= $product_id ?>" class="add-comment-btn">Add Comment</a>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>