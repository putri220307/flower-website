

<?php 
include '../includes/header.php';
require '../config/database.php';

// Ambil ID produk dari URL
$product_id = $_GET['id'] ?? 0;

// Query untuk mendapatkan detail produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: ../index.php");
    exit;
}

// Fungsi untuk mendapatkan path gambar yang benar
function getProductImagePath($imageName) {
    // Cek jika path sudah lengkap
    if (strpos($imageName, 'uploads/') === 0 || strpos($imageName, 'assets/') === 0) {
        return '../' . $imageName;
    }
    
    // Cek jika hanya nama file
    $possiblePaths = [
        '../uploads/profile/' . $imageName,
        '../assets/images/products/' . $imageName,
        '../' . $imageName
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // Default image jika tidak ditemukan
    return '../assets/images/default-product.jpg';
}

// Fungsi untuk format deskripsi produk
function formatProductDescription($description) {
    $paragraphs = explode("\n", $description);
    $formatted = '';
    foreach ($paragraphs as $para) {
        if (strpos($para, 'Keunggulan:') !== false) {
            $formatted .= '<p><strong>'.$para.'</strong></p>';
        } else {
            $formatted .= '<p>'.$para.'</p>';
        }
    }
    return $formatted;
}
?>

<main>
    <div class="container">
        <div class="product-detail-page-wrapper">
        <div class="product-detail-card-page">
            <h1 class="detail-title">Details</h1>
            
            <div class="product-detail-content">
                <div class="product-image-container">
                    <img src="<?php echo getProductImagePath($product['image_path'] ?? $product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='../assets/images/default-product.jpg'">
                </div>
                
                <div class="product-info">
                    <h2 class="product-name" style="color: <?php echo getColorCode($product['color']); ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h2>
                    
                    <div class="product-description">
                        <?php echo formatProductDescription(htmlspecialchars($product['description'])); ?>
                    </div>
                    
                    <div class="product-actions-container">
                        <div class="product-actions-page">
                            <a href="comments.php?id=<?php echo $product['id']; ?>" class="btn comment-btn">Comment</a>
                            <a href="../index.php" class="btn back-btn">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</main>