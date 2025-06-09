<?php
include '../includes/header.php';
require '../config/database.php';

$comment_id = $_GET['id'] ?? 0;

// Ambil data komentar dari database.
// Ini juga akan mengambil product_id yang terkait dengan komentar tersebut.
$stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ? AND user_id = ?");
$stmt->execute([$comment_id, $_SESSION['user_id']]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$comment) {
    header("Location: index.php"); 
    exit;
}
$product_id = $comment['product_id'];

// Tangani pengiriman form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_comment = $_POST['comment'] ?? '';
    
    if (!empty($new_comment)) {
        // Lakukan sanitasi input untuk keamanan
        $sanitized_comment = htmlspecialchars(strip_tags($new_comment));

        // Update komentar di database
        $stmt = $pdo->prepare("UPDATE comments SET comment = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$sanitized_comment, $comment_id]);
        
        // *** BAGIAN PENTING UNTUK PENGALIHAN SETELAH SAVE ***
        // Redirect kembali ke halaman komentar setelah berhasil disimpan
        header("Location: comments.php?id=" . $product_id);
        exit; // Sangat penting untuk menghentikan eksekusi script setelah header
    }
}
?>

<main>
    <div class="edit-comment-container">
        <h1>Edit Comment</h1>
        
        <form method="POST" class="comment-form">
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