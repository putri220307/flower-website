dashboard.php
<?php
session_start();
require '../config/database.php';

// Enhanced security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
function logActivity($pdo, $userId, $activityType, $description) {
    $stmt = $pdo->prepare("
        INSERT INTO activity_log 
        (user_id, activity_type, description) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$userId, $activityType, $description]);
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
    <style>
        :root {
            --sidebar-bg: #E2CEB1;
            --main-bg: #FDFCE8;
            --text-dark: #584C43;
            --text-light: #FFFFFF;
            --accent-color: #4E73DE;
            --danger-color: #E74A3B;
            --success-color: #1CC88A;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
            background-color: var(--main-bg);
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            color: var(--text-dark);
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(88, 76, 67, 0.2);
        }
        
        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-menu {
            margin-top: 20px;
            position: relative;
            min-height: calc(100vh - 120px);
        }
        
        .menu-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .menu-item:hover {
            background-color: rgba(88, 76, 67, 0.1);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .menu-title {
            font-size: 20px;
            font-weight: 600;
            margin: 20px 20px 10px;
            color: var(--text-dark);
        }
        
        .submenu {
            padding-left: 20px;
            display: none;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .submenu.show {
            display: block;
        }
        
        .submenu-item {
            padding: 10px 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .submenu-item:hover {
            background-color: rgba(88, 76, 67, 0.1);
        }
        
        .submenu-item i {
            color: var(--accent-color);
        }
        
        .logout-btn {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            padding: 12px;
            background-color: var(--danger-color);
            color: var(--text-light);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
            transition: all 0.3s;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            color: var(--text-dark);
            font-size: 28px;
        }
        
        .user-info {
            background-color: white;
            padding: 8px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            font-weight: 500;
        }
        
        .welcome-message {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .welcome-message h2 {
            color: var(--text-dark);
            margin-bottom: 15px;
        }
        
        .welcome-message p {
            color: var(--text-dark);
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .data-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .data-section h2 {
            color: var(--text-dark);
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .data-section h2 i {
            color: var(--accent-color);
        }
        
        .data-list {
            list-style-type: none;
        }
        
        .data-list li {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .data-list li:hover {
            background-color: #f8f9fa;
        }
        
        .data-list li:last-child {
            border-bottom: none;
        }
        
        .data-list i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        
        .data-card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .data-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .data-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .data-card h3 {
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .data-card i {
            font-size: 20px;
        }
        
        .data-card .count {
            font-size: 28px;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .copyright {
            text-align: center;
            color: var(--text-dark);
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .sidebar-header h2 span,
            .menu-item span,
            .submenu-item span {
                display: none;
            }
            
            .menu-item, .submenu-item {
                justify-content: center;
            }
            
            .menu-item i, .submenu-item i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
    </style>
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
            
            <div class="menu-item">
                <i class="fas fa-users"></i> <span>Manajemen User</span>
            </div>
            
            <div class="menu-item">
                <i class="fas fa-cog"></i> <span>Pengaturan</span>
            </div>
            
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
                <div class="count">125</div>
                <a href="flowers.php">Lihat detail</a>
            </div>
            
            <div class="data-card">
                <h3><i class="fas fa-comments"></i> Komentar</h3>
                <div class="count">42</div>
                <a href="comments.php">Lihat detail</a>
            </div>
            
            <div class="data-card">
                <h3><i class="fas fa-users"></i> Pengguna</h3>
                <div class="count">78</div>
                <a href="users.php">Lihat detail</a>
            </div>
            
            <div class="data-card">
                <h3><i class="fas fa-images"></i> Slider</h3>
                <div class="count">5</div>
                <a href="sliders.php">Lihat detail</a>
            </div>
        </div>
        
        <div class="data-section">
            <h2><i class="fas fa-bell"></i> Aktivitas Terkini</h2>
            <ul class="data-list">
                <li>
                    <i class="fas fa-user-plus text-success"></i> User baru "johndoe" terdaftar (5 menit lalu)
                </li>
                <li>
                    <i class="fas fa-comment"></i> Komentar baru pada bunga "Mawar Merah" (12 menit lalu)
                </li>
                <li>
                    <i class="fas fa-edit"></i> Data bunga "Anggrek Bulan" diperbarui (30 menit lalu)
                </li>
                <li>
                    <i class="fas fa-sign-in-alt"></i> Admin login dari IP 192.168.1.1 (1 jam lalu)
                </li>
            </ul>
        </div>
        
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> FLORAZZIU - Sistem Rekomendasi Bunga. All rights reserved.
        </div>
    </div>
    
    <script>
        // Toggle submenu
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            submenu.classList.toggle('show');
        }
        // Auto-refresh aktivitas komentar setiap 15 detik
function refreshComments() {
    fetch('../products/comments.php?latest=5')
        .then(response => response.text())
        .then(data => {
            document.getElementById('activity-feed').innerHTML = data;
        });
}

setInterval(refreshComments, 15000);
        // Mobile responsive
        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.add('collapsed');
            } else {
                document.querySelector('.sidebar').classList.remove('collapsed');
            }
        }
        
        window.addEventListener('resize', checkScreenSize);
        checkScreenSize();
    </script>
</body>
</html>