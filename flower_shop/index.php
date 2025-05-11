<?php include 'includes/header.php'; ?>

<main>
    <div class="container">
        <!-- Slider Section -->
        <div class="slider">
            <!-- Slide Dots -->
            
            <div class="slides">
                <div class="slide">
                    <img src="assets/images/slide1.jpg" alt="Flower Slide 1">
                </div>
                <div class="slide">
                    <img src="assets/images/slide2.jpg" alt="Flower Slide 2">
                </div>
            </div>
            <div class="slide-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-dropdown">
                <button class="filter-btn">Filter by Color</button>
                <div class="dropdown-content">
                    <a href="?color=blue">Biru</a>
                    <a href="?color=purple">Ungu</a>
                    <a href="?color=pink">Pink</a>
                    <a href="?color=yellow">Kuning</a>
                    <a href="?color=green">Hijau</a>
                    <a href="?color=white">Putih</a>
                    <a href="?color=red">Merah</a>
                    <a href="?color=black">Hitam</a>
                </div>
            </div>
            
            <div class="filter-dropdown">
                <button class="filter-btn">Sort by</button>
                <div class="dropdown-content">
                    <a href="?sort=a-z">A-Z</a>
                    <a href="?sort=z-a">Z-A</a>
                </div>
            </div>
        </div>
        
        <!-- Products Section -->
        <div class="products">
            <?php
            require 'config/database.php';
            
            // Build query based on filters
            $sql = "SELECT * FROM products";
            $params = [];
            
            if (isset($_GET['color'])) {
                $sql .= " WHERE color = ?";
                $params[] = $_GET['color'];
            }
            
            if (isset($_GET['sort'])) {
                if ($_GET['sort'] == 'a-z') {
                    $sql .= " ORDER BY name ASC";
                } elseif ($_GET['sort'] == 'z-a') {
                    $sql .= " ORDER BY name DESC";
                }
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($products) > 0): 
                foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-actions">
                        <a href="products/product-detail.php?id=<?= $product['id'] ?>" class="view-btn">View</a>
                        </div>
                    </div>
                <?php endforeach; 
            else: ?>
                <!-- <div class="no-products">
                    <p>No products found. Try different filters.</p>
                </div> -->
            <?php endif; ?>
        </div>
    </div>
</main>
<script>
    let currentIndex = 0;
    const slides = document.querySelectorAll('.slides .slide');
    const dots = document.querySelectorAll('.slide-dots .dot');

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    // Auto-slide
    setInterval(() => {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }, 3900); // Ganti slide tiap 3 detik

    // Click on dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentIndex = index;
            showSlide(currentIndex);
        });
    });

    // Inisialisasi awal
    showSlide(currentIndex);
</script>

<?php include 'includes/footer.php'; ?>