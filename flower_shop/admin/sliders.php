<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_slider'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF tidak valid";
        header("Location: sliders.php");
        exit;
    }

    $sliderId = $_POST['slider_id'] ?? null;
    if ($sliderId) {
        try {
            // Get image path first
            $stmt = $pdo->prepare("SELECT image_path FROM sliders WHERE id = ?");
            $stmt->execute([$sliderId]);
            $slider = $stmt->fetch();
            
            if ($slider) {
                // Delete file
                if (file_exists('../' . $slider['image_path'])) {
                    unlink('../' . $slider['image_path']);
                }
                
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM sliders WHERE id = ?");
                $stmt->execute([$sliderId]);
                $_SESSION['success'] = "Slider berhasil dihapus";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menghapus slider: " . $e->getMessage();
        }
    }
    header("Location: sliders.php");
    exit;
}

// Get all sliders
try {
    $stmt = $pdo->query("SELECT * FROM sliders ORDER BY created_at DESC");
    $sliders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data slider: " . $e->getMessage();
    $sliders = [];
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
    <title>Manajemen Slider - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .slider-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .slider-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .slider-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .slider-actions {
            padding: 15px;
            display: flex;
            justify-content: space-between;
            background: #f9f9f9;
        }
        
        .btn-edit {
            background: #4e73df;
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .btn-delete {
            background: #e74a3b;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .upload-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        upload-slider-container {
        max-width: 800px;
        margin: 20px auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .upload-header {
        text-align: center;
        margin-bottom: 25px;
    }

    .upload-header h2 {
        color: #2c3e50;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .upload-header p {
        color: #7f8c8d;
        font-size: 14px;
    }

    .upload-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .file-upload-wrapper {
        position: relative;
        margin-bottom: 20px;
    }

    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        border: 2px dashed #bdc3c7;
        border-radius: 8px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-upload-label:hover {
        border-color: #3498db;
        background-color: #f1f8fe;
    }

    .file-upload-label i {
        font-size: 48px;
        color: #3498db;
        margin-bottom: 15px;
    }

    .file-upload-label .upload-text {
        font-size: 16px;
        color: #2c3e50;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .file-upload-label .upload-hint {
        font-size: 13px;
        color: #7f8c8d;
    }

    #file-name-display {
        margin-top: 10px;
        font-size: 14px;
        color: #27ae60;
        font-weight: 500;
        text-align: center;
    }

    #slider_image {
        display: none;
    }

    .btn-upload {
        background: linear-gradient(135deg, #3498db, #2c3e50);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-upload:hover {
        background: linear-gradient(135deg, #2980b9, #1a252f);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(41, 128, 185, 0.2);
    }

    .btn-upload:active {
        transform: translateY(0);
    }

    .preview-container {
        margin-top: 20px;
        text-align: center;
    }

    .preview-title {
        font-size: 14px;
        color: #7f8c8d;
        margin-bottom: 10px;
    }

    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 6px;
        border: 1px solid #eee;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .upload-slider-container {
            padding: 20px;
        }
        
        .file-upload-label {
            padding: 30px 15px;
        }
    }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <    <div class="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="dashboard-link">
            <h2><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></h2>
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
            <h1>Manajemen Slider</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="upload-slider-container">
    <div class="upload-header">
        <h2><i class="fas fa-cloud-upload-alt"></i> Upload Slider Baru</h2>
        <p>Tambahkan gambar slider baru untuk ditampilkan di halaman utama</p>
    </div>

    <form action="upload_slider.php" method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="file-upload-wrapper">
            <label for="slider_image" class="file-upload-label" id="file-upload-label">
                <i class="fas fa-image"></i>
                <span class="upload-text">Pilih atau tarik gambar slider</span>
                <span class="upload-hint">Format: JPG, PNG (Maksimal 2MB)</span>
            </label>
            <input type="file" id="slider_image" name="slider_image" accept="image/*" required>
            <div id="file-name-display"></div>
        </div>

        <div class="preview-container">
            <div class="preview-title">Pratinjau Gambar</div>
            <img id="image-preview" class="image-preview" src="#" alt="Pratinjau Gambar">
        </div>

        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <button type="submit" class="btn-upload">
            <i class="fas fa-upload"></i> Upload Slider
        </button>
    </form>
</div>
            
            <h2><i class="fas fa-list"></i> Daftar Slider</h2>
            
            <?php if (empty($sliders)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada slider yang diupload
                </div>
            <?php else: ?>
                <div class="slider-grid">
                    <?php foreach ($sliders as $slider): ?>
                        <div class="slider-card">
                            <img src="../<?= htmlspecialchars($slider['image_path']) ?>" alt="Slider Image" class="slider-image">
                            <div class="slider-actions">
                                <span><?= date('d/m/Y H:i', strtotime($slider['created_at'])) ?></span>
                                <div>
                                    <a href="edit_slider.php?id=<?= $slider['id'] ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus slider ini?')">
                                        <input type="hidden" name="slider_id" value="<?= $slider['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" name="delete_slider" class="btn-delete">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
        document.getElementById('slider_image').addEventListener('change', function(e) {
        const fileNameDisplay = document.getElementById('file-name-display');
        const previewImage = document.getElementById('image-preview');
        const fileLabel = document.getElementById('file-upload-label');
        
        if (this.files.length > 0) {
            const fileName = this.files[0].name;
            fileNameDisplay.textContent = `File dipilih: ${fileName}`;
            
            // Menampilkan preview gambar
            if (this.files[0].type.match('image.*')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
            
            // Ubah style label saat file dipilih
            fileLabel.style.borderColor = '#27ae60';
            fileLabel.style.backgroundColor = '#e8f8f0';
        } else {
            fileNameDisplay.textContent = '';
            previewImage.style.display = 'none';
            fileLabel.style.borderColor = '#bdc3c7';
            fileLabel.style.backgroundColor = '#f8f9fa';
        }
    });

    // Drag and drop functionality
    const fileLabel = document.getElementById('file-upload-label');
    const fileInput = document.getElementById('slider_image');
    
    fileLabel.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileLabel.style.borderColor = '#3498db';
        fileLabel.style.backgroundColor = '#e1f0fa';
    });
    
    fileLabel.addEventListener('dragleave', () => {
        fileLabel.style.borderColor = fileInput.files.length ? '#27ae60' : '#bdc3c7';
        fileLabel.style.backgroundColor = fileInput.files.length ? '#e8f8f0' : '#f8f9fa';
    });
    
    fileLabel.addEventListener('drop', (e) => {
        e.preventDefault();
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    });
    </script>
</body>
</html>