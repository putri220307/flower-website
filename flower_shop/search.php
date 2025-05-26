<?php
include 'includes/header.php';
require 'config/database.php';

// Membersihkan dan memvalidasi input pencarian
$searchQuery = trim($_GET['query'] ?? '');

// Jika kosong, redirect
if (empty($searchQuery)) {
    header("Location: index.php");
    exit();
}

// Gunakan LIKE untuk kompatibilitas yang lebih baik
// Correct version - single parameter
$stmt = $pdo->prepare("SELECT * FROM products 
    WHERE name LIKE CONCAT('%', ?, '%') 
    ORDER BY name ASC");

$stmt->execute([$searchQuery]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <div class="container">
        <h2 class="search-results-title">Hasil Pencarian: "<?= htmlspecialchars($searchQuery) ?>"</h2>
        
        <?php if (!empty($products)): ?>
            <div class="products">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy"
                                 onerror="this.src='assets/images/default-product.jpg'">
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

<style>
    /* Responsive Search Results */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .search-results-title {
        text-align: center;
        margin: 30px 0;
        color: #584C43;
        font-size: 24px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        margin: 40px 0;
        padding: 0 10px;
    }
    
    .product-card {
        background: #FDFCE8;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .product-image-container {
        height: 250px;
        overflow: hidden;
        position: relative;
        background: #f8f8f8;
    }
    
    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .product-card:hover .product-image-container img {
        transform: scale(1.08);
    }
    
    .product-name {
        padding: 18px 15px 10px;
        font-size: 17px;
        margin: 0;
        text-align: center;
        color: #333;
        font-weight: 600;
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-actions {
        padding: 0 20px 20px;
    }
    
    .view-btn {
        display: block;
        padding: 12px;
        background: #E2CEB1;
        color: #584C43;
        text-align: center;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s;
        font-weight: 500;
        letter-spacing: 0.5px;
    }
    
    .view-btn:hover {
        background: #d4b98c;
        transform: translateY(-2px);
    }
    
    .no-products-found {
        text-align: center;
        padding: 60px 20px;
        background: #f9f9f9;
        border-radius: 12px;
        margin: 50px 0;
    }
    
    .no-products-found i {
        color: #584C43;
        margin-bottom: 20px;
    }
    
    .search-suggestions {
        margin-top: 25px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .search-suggestions p {
        margin-bottom: 15px;
        color: #666;
    }
    
    .btn-primary {
        display: inline-block;
        padding: 12px 25px;
        background: #584C43;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        margin-top: 15px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 15px;
    }
    
    .btn-primary:hover {
        background: #463c35;
        transform: translateY(-2px);
    }

    /* Responsive Adjustments */
    @media (max-width: 1024px) {
        .products {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 25px;
        }
    }
    
    @media (max-width: 768px) {
        .products {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 0;
        }
        
        .product-image-container {
            height: 200px;
        }
        
        .product-name {
            font-size: 16px;
            min-height: 50px;
            padding: 15px 10px 8px;
        }
    }
    
    @media (max-width: 480px) {
        .products {
            grid-template-columns: 1fr;
            gap: 25px;
        }
        
        .product-card {
            max-width: 320px;
            margin: 0 auto;
        }
        
        .search-results-title {
            font-size: 20px;
            margin: 20px 0;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>