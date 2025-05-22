<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get slider ID
$sliderId = $_GET['id'] ?? null;
if (!$sliderId) {
    $_SESSION['error'] = "ID slider tidak valid";
    header("Location: sliders.php");
    exit;
}

// Fetch slider data
try {
    $stmt = $pdo->prepare("SELECT * FROM sliders WHERE id = ?");
    $stmt->execute([$sliderId]);
    $slider = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$slider) {
        $_SESSION['error'] = "Slider tidak ditemukan";
        header("Location: sliders.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengambil data slider: " . $e->getMessage();
    header("Location: sliders.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF tidak valid";
        header("Location: sliders.php");
        exit;
    }

    // Check if new image was uploaded
    if (isset($_FILES['slider_image']) && $_FILES['slider_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['slider_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validate new image
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = "Hanya file JPG, PNG, atau GIF yang diperbolehkan";
            header("Location: edit_slider.php?id=" . $sliderId);
            exit;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "Ukuran file terlalu besar (maksimal 2MB)";
            header("Location: edit_slider.php?id=" . $sliderId);
            exit;
        }

        // Generate new filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'slider_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = '../assets/images/sliders/' . $filename;
        $relativePath = 'assets/images/sliders/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Delete old file
            if (file_exists('../' . $slider['image_path'])) {
                unlink('../' . $slider['image_path']);
            }
            
            // Update database with new path
            try {
                $stmt = $pdo->prepare("UPDATE sliders SET image_path = ? WHERE id = ?");
                $stmt->execute([$relativePath, $sliderId]);
                $_SESSION['success'] = "Gambar slider berhasil diperbarui";
                header("Location: sliders.php");
                exit;
            } catch (PDOException $e) {
                // Delete new file if update fails
                unlink($destination);
                $_SESSION['error'] = "Gagal memperbarui data slider: " . $e->getMessage();
                header("Location: edit_slider.php?id=" . $sliderId);
                exit;
            }
        } else {
            $_SESSION['error'] = "Gagal mengupload file";
            header("Location: edit_slider.php?id=" . $sliderId);
            exit;
        }
    } else {
        $_SESSION['error'] = "Tidak ada perubahan yang dilakukan";
        header("Location: sliders.php");
        exit;
    }
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Slider - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .current-image {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-submit {
            background: #4e73df;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <h2><span>Dashboard</span></h2>
            </a>
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
                <a href="sliders.php" class="submenu-item active">
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
            <h1>Edit Slider</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="edit-container">
            <h2><i class="fas fa-image"></i> Gambar Saat Ini</h2>
            <img src="../<?= htmlspecialchars($slider['image_path']) ?>" alt="Current Slider" class="current-image">
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="slider_image">Upload Gambar Baru</label>
                    <input type="file" id="slider_image" name="slider_image" accept="image/*">
                    <small>Biarkan kosong jika tidak ingin mengubah gambar</small>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="button-group">
                    <a href="sliders.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            submenu.classList.toggle('show');
        }

        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.add('collapsed');
            } else {
                document.querySelector('.sidebar').classList.remove('collapsed');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);
        });
    </script>
</body>
</html>