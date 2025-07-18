<?php
session_start();
require 'includes/header.php';
require 'config/database.php';

// Ambil user ID jika sudah login
$user_id = $_SESSION['user_id'] ?? null;

// Fungsi untuk memeriksa status like & save
function is_liked($pdo, $user_id, $product_id) {
    if (!$user_id) return false;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetchColumn() > 0;
}

function is_saved($pdo, $user_id, $product_id) {
    if (!$user_id) return false;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM saved_products WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    return $stmt->fetchColumn() > 0;
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<main>
    <div class="container-page">
        <div class="slider">
            <div class="slides">
                <?php
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
                        <div class="slide active">
                            <img src="assets/images/slide1.jpg" alt="Default Slide 1">
                        </div>
                        <div class="slide">
                            <img src="assets/images/slide2.jpg" alt="Default Slide 2">
                        </div>
                    <?php endif;
                } catch (PDOException $e) {
                    echo '<div class="slide active">
                                <img src="assets/images/slide1.jpg" alt="Default Slide 1">
                            </div>
                            <div class="slide">
                                <img src="assets/images/slide2.jpg" alt="Default Slide 2">
                            </div>';
                }
                ?>
            </div>
            
            <div class="slide-dots"></div>
        </div>
        
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
        
        <div class="products">
            <?php
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
                foreach ($products as $product): 
                    $is_liked = $user_id ? is_liked($pdo, $user_id, $product['id']) : false;
                    $is_saved = $user_id ? is_saved($pdo, $user_id, $product['id']) : false;
                ?>
                    <div class="product-card-page fade-in-up" data-id="<?= htmlspecialchars($product['id']) ?>">
                        <div class="product-image-container">
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>
                        <div class="product-actions">
                            <button class="icon-btn like-btn <?= $is_liked ? 'active' : '' ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                            <a href="products/product-detail.php?id=<?= $product['id'] ?>" class="view-btn">View</a>
                            <button class="icon-btn save-btn <?= $is_saved ? 'active' : '' ?>">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; 
            else: ?>
               <p style="text-align: center; width: 100%; color: #777; margin-top: 50px;">
                    No products found in this category.
                </p>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
    /* ... Gaya CSS Anda yang sudah ada ... */
    .container-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        margin-top: 30px;
    }

    /* Compact Slider Styles */
    .slider {
        position: relative;
        max-width: 1400px;
        margin: 0 auto 30px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        height: 300px;
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
            width: 80%;
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
        max-width: 1400px;
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

    .product-image-container {
        height: 250px;
        overflow: hidden;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-actions {
        padding: 15px;
        text-align: center;
        display: flex; 
        justify-content: space-between; /* Mengatur jarak agar tombol di kiri, tengah, dan kanan */
        align-items: center; 
    }

    .view-btn {
        display: inline-block;
        padding: 10px 25px;
        background-color: #E2CEB1;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background-color 0.2s;
    }
    
    .view-btn:hover {
        background-color: #E2CEB1;
    }

    /* Gaya tambahan untuk tombol like/save */
    .icon-btn {
        background: none;
        border: none;
        font-size: 1.5em;
        cursor: pointer;
        color: #888;
        transition: all 0.2s ease;
    }

    .icon-btn:hover {
        color: #e74c3c;
    }

    .like-btn.active .fa-heart {
        color: #e74c3c;
    }
    
    .save-btn.active .fa-bookmark {
        color: #4E73DE;
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
    document.addEventListener('DOMContentLoaded', function() {
        // Logika untuk memeriksa status login dari PHP
        const isLoggedIn = <?= json_encode(isset($_SESSION['user_id'])) ?>;

        // --- Logika Slider yang sudah ada ---
        let currentIndex = 0;
        const slides = document.querySelectorAll('.slides .slide');
        const dotsContainer = document.querySelector('.slide-dots');
        
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
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            currentIndex = index;
            slides[currentIndex].classList.add('active');
            dots[currentIndex].classList.add('active');
        }
        
        const slideInterval = setInterval(() => {
            currentIndex = (currentIndex + 1) % slides.length;
            goToSlide(currentIndex);
        }, 5000);

        // --- SCROLL ANIMATION (looping version) ---
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                } else {
                    entry.target.classList.remove('show');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });

        // --- Logika Like dan Simpan (AJAX) ---
        document.addEventListener('click', function(e) {
            const likeBtn = e.target.closest('.like-btn');
            const saveBtn = e.target.closest('.save-btn');

            // Tangani tombol Like
            if (likeBtn) {
                // Jika user belum login, alihkan ke halaman login
                if (!isLoggedIn) {
                    window.location.href = 'auth/login.php';
                    return;
                }

                const productCard = likeBtn.closest('.product-card-page');
                const productId = productCard ? productCard.dataset.id : null;
                
                if (!productId) {
                    alert('Error: Product ID is missing.');
                    return;
                }

                fetch(`products/toggle_like.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || data.status === 'success') {
                            likeBtn.classList.toggle('active');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Tangani tombol Save
            if (saveBtn) {
                // Jika user belum login, alihkan ke halaman login
                if (!isLoggedIn) {
                    window.location.href = 'auth/login.php';
                    return;
                }
                
                const productCard = saveBtn.closest('.product-card-page');
                const productId = productCard ? productCard.dataset.id : null;
                
                if (!productId) {
                    alert('Error: Product ID is missing.');
                    return;
                }

                fetch(`products/toggle_save.php?id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || data.status === 'success') {
                            saveBtn.classList.toggle('active');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>