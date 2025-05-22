<?php
session_start();
require __DIR__.'/../config/database.php';

// Enhanced security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get comment ID from URL
$commentId = $_GET['id'] ?? null;
if (!$commentId) {
    $_SESSION['error'] = "ID komentar tidak valid";
    header("Location: comments.php");
    exit;
}

// Fetch comment data
try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.comment, c.created_at, 
               u.username, 
               p.name AS product_name
        FROM comments c
        JOIN users u ON c.user_id = u.id
        JOIN products p ON c.product_id = p.id
        WHERE c.id = ?
    ");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment) {
        $_SESSION['error'] = "Komentar tidak ditemukan";
        header("Location: comments.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal mengambil data komentar: " . $e->getMessage();
    header("Location: comments.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF tidak valid";
        header("Location: comments.php");
        exit;
    }

    $newComment = $_POST['comment'] ?? '';
    if (empty($newComment)) {
        $_SESSION['error'] = "Komentar tidak boleh kosong";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE comments SET comment = ? WHERE id = ?");
            $stmt->execute([$newComment, $commentId]);
            $_SESSION['success'] = "Komentar berhasil diperbarui";
            header("Location: comments.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui komentar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Komentar - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Additional styles for edit comment page */
        .edit-comment-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-control[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .form-control-textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn-submit {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #3a5bc7;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        
        .button-group {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Sidebar (sama seperti sebelumnya) -->
    <div class="sidebar">
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
                <a href="sliders.php" class="submenu-item">
                    <i class="fas fa-images"></i> <span>Data Slider</span>
                </a>
                <a href="comments.php" class="submenu-item active">
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
            <h1>Edit Komentar</h1>
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
        
        <div class="edit-comment-container">
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="form-control" 
                           value="<?= htmlspecialchars($comment['username']) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="product_name">Bunga</label>
                    <input type="text" id="product_name" class="form-control" 
                           value="<?= htmlspecialchars($comment['product_name']) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="created_at">Tanggal Komentar</label>
                    <input type="text" id="created_at" class="form-control" 
                           value="<?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="comment">Komentar</label>
                    <textarea id="comment" name="comment" class="form-control form-control-textarea" 
                              required><?= htmlspecialchars($comment['comment']) ?></textarea>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="button-group">
                    <a href="comments.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
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

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);
        });
    </script>
</body>
</html>