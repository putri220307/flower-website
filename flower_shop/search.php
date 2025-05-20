<?php
include 'includes/header.php';
require 'config/database.php';

// Membersihkan dan memvalidasi input pencarian
$searchQuery = trim($_GET['query'] ?? '');

// Query pencarian produk hanya jika ada kata kunci
if (!empty($searchQuery)) {
    $stmt = $pdo->prepare("SELECT * FROM products 
                      WHERE name LIKE ? OR description LIKE ?
                      ORDER BY name ASC");
$stmt->execute(["$searchQuery%", "$searchQuery%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Jika search kosong, arahkan ke halaman utama
    header("Location: index.php");
    exit();
}
?>

<main>
    <div class="container">
        <h2 class="search-results-title">Hasil Pencarian: "<?= htmlspecialchars($searchQuery) ?>"</h2>
        
        <?php if (!empty($products)): ?>
            <div class="products">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        
                        <div class="product-image-container">
                            <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy">
                        </div>
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-actions">
                            <a href="products/product-detail.php?id=<?= $product['id'] ?>" 
                               class="view-btn"
                               aria-label="View <?= htmlspecialchars($product['name']) ?>">
                                View
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-products-found">
                <i class="fas fa-search fa-3x"></i>
                <p>Produk "<?= htmlspecialchars($searchQuery) ?>" tidak ditemukan</p>
                <div class="search-suggestions">
                    <p>Coba cari dengan kata kunci lain atau lihat produk kami:</p>
                    <a href="index.php" class="btn-primary">Semua Produk</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>