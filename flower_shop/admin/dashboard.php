<?php
session_start();
require __DIR__.'/../config/database.php';

// Enhanced security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Inisialisasi variabel dengan array kosong sebagai default
$unverifiedUsers = [];

try {
    // Pastikan query ini menggunakan kondisi yang tepat
    $stmt = $pdo->prepare("
        SELECT id, username, created_at 
        FROM users 
        WHERE is_verified = FALSE OR is_verified = 0
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $unverifiedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debugging - tampilkan hasil query
    error_log("Unverified users: " . print_r($unverifiedUsers, true));
} catch (PDOException $e) {
    error_log("Error fetching unverified users: " . $e->getMessage());
    $unverifiedUsers = [];
}
try {
    // Hitung total bunga (ambil dari tabel products dengan kategori bunga)
   $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE category = 'bunga'");
$totalFlowers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM comments");
$totalComments = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_verified = 1");
$totalUsers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM sliders");
$totalSliders = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    error_log("Error fetching statistics: " . $e->getMessage());
    $totalFlowers = $totalComments = $totalUsers = $totalSliders = 0;
}

// Handle verifikasi user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_user'])) {
    $userId = $_POST['user_id'] ?? null;
    if ($userId && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE WHERE id = ?");
            $stmt->execute([$userId]);
            header("Location: dashboard.php?verified=1");
            exit;
        } catch (PDOException $e) {
            error_log("Error verifying user: " . $e->getMessage());
            $verificationError = "Gagal memverifikasi user. Silakan coba lagi.";
        }
    }
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- CSS Admin -->
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            
            <h2><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></h2>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-title">Data Master</div>
            
            <div class="menu-item" onclick="toggleSubmenu('data-master')">
                <i class="fas fa-database"></i> <span>Data Master</span>
            </div>
            <div class="submenu" id="data-master">
                <a href="flowers.php" class="submenu-item">
                    <i class="fas fa-flower"></i> <span>Data Bunga</span>
                </a>
                <a href="sliders.php" class="submenu-item">
                    <i class="fas fa-images"></i> <span>Data Slider</span>
                </a>
                <a href="comments.php" class="submenu-item">
                    <i class="fas fa-comments"></i> <span>Data Komentar</span>
                </a>
            </div>
            
<a href="users.php" class="menu-item">
    <i class="fas fa-users"></i> <span>Manajemen User</span>
</a>
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Admin</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
            </div>
        </div>
        
        <div class="welcome-message">
            <h2>Selamat datang di Dashboard Admin FLORAZZIU!</h2>
            <p>
                Anda login sebagai <strong>Administrator</strong>. Gunakan hak akses ini dengan bijak untuk mengelola sistem rekomendasi bunga FLORAZZIU.
            </p>
            <p>
                Dashboard ini memberikan Anda kontrol penuh atas konten, pengguna, dan preferensi sistem. Pastikan untuk selalu logout setelah selesai mengelola sistem.
            </p>
        </div>
        
        <div class="data-card-container">
            <div class="data-card">
                <h3><i class="fas fa-flower"></i> Total Bunga</h3>
                <div class="count"><?= htmlspecialchars($totalFlowers) ?></div>
                <a href="flowers.php">Lihat detail</a>
            </div>
            
            <div class="data-card">
                <h3><i class="fas fa-comments"></i> Komentar</h3>
                <div class="count"><?= htmlspecialchars($totalComments) ?></div>
                <a href="comments.php">Lihat detail</a>
            </div>
            
            <div class="data-card">
                <h3><i class="fas fa-users"></i> Pengguna</h3>
                <div class="count"><?= htmlspecialchars($totalUsers) ?></div>
                <a href="users.php">Lihat detail</a>
            </div>
            
            <div class="data-card">
                <h3><i class="fas fa-images"></i> Slider</h3>
                <div class="count"><?= htmlspecialchars($totalSliders) ?></div>
                <a href="sliders.php">Lihat detail</a>
            </div>
        </div>
        
        <div class="data-section-page">
            <div class="section-header"></div>
            <h2><i class="fas fa-bell"></i> Aktivitas Terkini</h2>
            <div class="sort-controls-container">
            <span>Urutkan: </span>
            <button id="sort-newest" class="sort-btn active" data-sort="newest">
                <i class="fas fa-arrow-down"></i> Terbaru
            </button>
            <button id="sort-oldest" class="sort-btn" data-sort="oldest">
                <i class="fas fa-arrow-up"></i> Terlama
            </button>
            </div>
            <ul class="data-list" id="recent-activities">
        <!-- Konten akan diisi oleh JavaScript -->
        <li>Memuat data aktivitas...</li>
                    <!-- <i class="fas fa-comment"></i> Komentar baru pada bunga "Mawar Merah" (12 menit lalu)
                </li>
                <li>
                    <i class="fas fa-edit"></i> Data bunga "Anggrek Bulan" diperbarui (30 menit lalu)
                </li>
                <li>
                    <i class="fas fa-sign-in-alt"></i> Admin login dari IP 192.168.1.1 (1 jam lalu)
                </li> -->
            </ul>
        </div>
        
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> FLORAZZIU - Sistem Rekomendasi Bunga. All rights reserved.
        </div>
        <?php if (!empty($unverifiedUsers)) : ?>

<?php endif; ?>

    </div>
    <!-- Notifikasi User Baru -->
<!-- Di dashboard.php bagian HTML -->
<div class="data-section" id="unverified-users-container">
    <h2><i class="fas fa-user-clock"></i> User Baru Perlu Verifikasi</h2>
    
    <?php if (isset($_GET['verified'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> User berhasil diverifikasi!
        </div>
    <?php endif; ?>
    
    <?php if (!empty($verificationError)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($verificationError) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($unverifiedUsers)): ?>
        <div class="table-responsive">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Tanggal Registrasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unverifiedUsers as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'] ?? 'now')) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?? '' ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" name="verify_user" class="verify-btn">
                                        <i class="fas fa-check"></i> Verifikasi
                                    </button>
                                </form>
                                
                            </td>
                        </tr>
                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-check-circle"></i> Tidak ada user baru yang perlu diverifikasi
        </div>
    <?php endif; ?>
</div>
    
<script>
    // Toggle submenu
    function toggleSubmenu(id) {
        const submenu = document.getElementById(id);
        submenu.classList.toggle('show');
    }

    // Mobile responsive
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            document.querySelector('.sidebar').classList.add('collapsed');
        } else {
            document.querySelector('.sidebar').classList.remove('collapsed');
        }
    }

    // Format waktu lebih akurat
    function formatTimeAgo(date) {
        const now = new Date();
        const diff = (now - date) / 1000; // dalam detik
        
        if (diff < 60) return 'baru saja';
        if (diff < 3600) return `${Math.floor(diff/60)} menit yang lalu`;
        if (diff < 86400) return `${Math.floor(diff/3600)} jam yang lalu`;
        return `${Math.floor(diff/86400)} hari yang lalu`;
    }

    // Global variable for current sort
    let currentSort = 'newest'; // Default sort

    // Main function to update recent activities
    function updateRecentActivities() {
        console.log('Fetching activities with sort:', currentSort); // Debug log
        
        fetch('fetch_recent_activities.php?sort=' + currentSort + '&t=' + new Date().getTime())
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(activities => {
                console.log('Received activities:', activities); // Debug log
                const container = document.getElementById('recent-activities');
                
                if (!container) {
                    console.error('Element with ID "recent-activities" not found!');
                    return;
                }
                
                container.innerHTML = ''; // Clear existing content
                
                if (activities.error) {
                    container.innerHTML = `<li>${activities.error}</li>`;
                    return;
                }
                
                if (!activities || activities.length === 0) {
                    container.innerHTML = '<li>Tidak ada aktivitas terbaru</li>';
                    return;
                }
                
                // Process each activity
                activities.forEach(activity => {
                    const li = document.createElement('li');
                    let iconClass, activityText;
                    
                    // Set icon and text based on activity type
                    switch(activity.type) {
                        case 'user_baru':
                            iconClass = 'fas fa-user-plus';
                            activityText = `User <strong>${activity.username}</strong> terdaftar`;
                            break;
                        case 'admin_login':
                            iconClass = 'fas fa-sign-in-alt';
                            activityText = `Admin <strong>${activity.username}</strong> login`;
                            break;
                        case 'comment':
                            iconClass = 'fas fa-comment';
                            activityText = `Komentar baru pada bunga "${activity.flower_name}"`;
                            break;
                        default:
                            iconClass = 'fas fa-info-circle';
                            activityText = `Aktivitas sistem`;
                    }
                    
                    li.innerHTML = `
                        <i class="${iconClass}"></i> ${activityText}
                        <span class="time-ago">(${formatTimeAgo(new Date(activity.created_at))})</span>
                    `;
                    container.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Error fetching activities:', error);
                const container = document.getElementById('recent-activities');
                if (container) {
                    container.innerHTML = `<li>Gagal memuat aktivitas: ${error.message}</li>`;
                }
            });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded'); // Debug log
        
        // Mobile responsive check
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);
        
        // Set up sort buttons
        const sortNewest = document.getElementById('sort-newest');
        const sortOldest = document.getElementById('sort-oldest');
        
        if (sortNewest && sortOldest) {
            console.log('Sort buttons found'); // Debug log
            
            sortNewest.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Newest button clicked'); // Debug log
                
                if (currentSort !== 'newest') {
                    currentSort = 'newest';
                    sortNewest.classList.add('active');
                    sortOldest.classList.remove('active');
                    updateRecentActivities();
                }
            });
            
            sortOldest.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Oldest button clicked'); // Debug log
                
                if (currentSort !== 'oldest') {
                    currentSort = 'oldest';
                    sortOldest.classList.add('active');
                    sortNewest.classList.remove('active');
                    updateRecentActivities();
                }
            });
            
            // Set initial active button
            sortNewest.classList.add('active');
        } else {
            console.error('Sort buttons not found!'); // Debug log
        }
        
        // Initial load of activities
        updateRecentActivities();
        
        // Set up auto-refresh every 5 seconds
        const refreshInterval = setInterval(updateRecentActivities, 5000);
        
        // Cleanup interval when page unloads
        window.addEventListener('beforeunload', function() {
            clearInterval(refreshInterval);
        });
    });

    // Function to handle user verification (if needed)
    function verifyUser(userId) {
        console.log('Verifying user:', userId);
        // You would typically make a fetch request here
    }
</script>

</body>
</html>