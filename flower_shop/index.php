<?php include 'includes/header.php'; ?>
<head><meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<main>
    <div class="container-page">
        <!-- Slider Section -->
        <!-- Slider Section -->
<div class="slider">
    <div class="slides">
        <?php
        require 'config/database.php';
        try {
            $stmt = $pdo->query("SELECT * FROM sliders ORDER BY created_at DESC");
            $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($sliders) > 0): 
                foreach ($sliders as $index => $slider): ?>
                    <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($slider['image_path']) ?>" 
                             alt="Slider Image <?= $index + 1 ?>">
                    </div>
                <?php endforeach; 
            else: ?>
                <!-- Fallback jika tidak ada slider di database -->
                <div class="slide active">
                    <img src="assets/images/slide1.jpg" alt="Default Slide 1">
                </div>
                <div class="slide">
                    <img src="assets/images/slide2.jpg" alt="Default Slide 2">
                </div>
            <?php endif;
        } catch (PDOException $e) {
            // Jika ada error, tampilkan slider default
            echo '<div class="slide active">
                    <img src="assets/images/slide1.jpg" alt="Default Slide 1">
                  </div>
                  <div class="slide">
                    <img src="assets/images/slide2.jpg" alt="Default Slide 2">
                  </div>';
        }
        ?>
    </div>
    
    <!-- Slide Dots - akan dibuat dinamis oleh JavaScript -->
    <div class="slide-dots"></div>
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
            // Pastikan query mengambil field yang benar
$sql = "SELECT id, name, description, image_path, color FROM products";
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
                    <div class="product-card-page fade-in-up">
                        <div class="product-image-container">
    <img src="<?= htmlspecialchars($product['image_path']) ?>" 
         alt="<?= htmlspecialchars($product['name']) ?>">
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

<style>
  .container-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Compact Slider Styles */
        .slider {
            position: relative;
            width: 100%; /* Setengah lebar container */
            margin: 0 auto 30px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            height: 300px; /* Tinggi lebih compact */
        }

        .slides {
            display: flex;
            height: 100%;
            width: 100%;
            transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .slide {
            min-width: 100%;
            position: relative;
            flex-shrink: 0;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .slide-dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .slide-dots .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.6);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .slide-dots .dot.active {
            background: white;
            transform: scale(1.3);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .slider {
                width: 80%; /* Lebih lebar di mobile */
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .slider {
                width: 90%;
                height: 200px;
            }
        }

        /* Your existing product styles */
        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .product-card-page {
            width: 250px;
            border-radius: 10px;
            overflow: hidden;
            background-color: #FDFCE8;
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in-up.show {
            opacity: 1;
            transform: translateY(0);
        }

</style>
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
    }, 5000); // Ganti slide tiap 3 detik

    // Click on dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentIndex = index;
            showSlide(currentIndex);
        });
    });
    // --- SCROLL ANIMATION (looping version) ---
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
        } else {
            entry.target.classList.remove('show'); // Hapus saat keluar dari layar
        }
    });
}, {
    threshold: 0.1
});

document.querySelectorAll('.fade-in-up').forEach(el => {
    observer.observe(el);
});

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi slider
    let currentIndex = 0;
    const slides = document.querySelectorAll('.slides .slide');
    const dotsContainer = document.querySelector('.slide-dots');
    
    // Buat dots dinamis berdasarkan jumlah slide
    slides.forEach((_, index) => {
        const dot = document.createElement('span');
        dot.classList.add('dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => {
            goToSlide(index);
        });
        dotsContainer.appendChild(dot);
    });
    
    const dots = document.querySelectorAll('.slide-dots .dot');
    
    function goToSlide(index) {
        // Reset semua slide dan dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Set slide dan dot aktif
        currentIndex = index;
        slides[currentIndex].classList.add('active');
        dots[currentIndex].classList.add('active');
    }
    
    // Auto-slide
    const slideInterval = setInterval(() => {
        currentIndex = (currentIndex + 1) % slides.length;
        goToSlide(currentIndex);
    }, 5000);
    
    // Hentikan auto-slide saat hover
    
});


    // Inisialisasi awal
    showSlide(currentIndex);
</script>

<?php include 'includes/footer.php'; ?>