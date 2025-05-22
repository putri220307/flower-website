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

// Handle delete comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF tidak valid";
        header("Location: comments.php");
        exit;
    }

    $commentId = $_POST['comment_id'] ?? null;
    if ($commentId) {
        try {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$commentId]);
            $_SESSION['success'] = "Komentar berhasil dihapus";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menghapus komentar: " . $e->getMessage();
        }
    }
    header("Location: comments.php");
    exit;
}

// Get all comments with user and product info
try {
    $stmt = $pdo->query("
        SELECT c.id, c.comment, c.created_at, 
               u.username, 
               p.name AS product_name
        FROM comments c
        JOIN users u ON c.user_id = u.id
        JOIN products p ON c.product_id = p.id
        ORDER BY c.created_at DESC
    ");
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data komentar: " . $e->getMessage();
    $comments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Komentar - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Additional styles for comments page */
        .comment-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .comment-user {
            font-weight: 600;
            color: #4e73df;
        }
        
        .comment-product {
            font-style: italic;
            color: #666;
        }
        
        .comment-date {
            color: #999;
            font-size: 0.9em;
        }
        
        .comment-content {
            margin: 15px 0;
            line-height: 1.6;
        }
        
        .comment-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #f6c23e;
            color: #fff;
            border: none;
        }
        
        .btn-edit:hover {
            background: #dda20a;
        }
        
        .btn-delete {
            background: #e74a3b;
            color: #fff;
            border: none;
        }
        
        .btn-delete:hover {
            background: #be2617;
        }
        
        .no-comments {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Sidebar (sama seperti di dashboard.php) -->
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
            <h1>Manajemen Komentar</h1>
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
        
        <div class="data-section">
            <h2><i class="fas fa-comments"></i> Daftar Komentar</h2>
            
            <?php if (empty($comments)): ?>
                <div class="no-comments">
                    <i class="fas fa-comment-slash fa-2x"></i>
                    <p>Tidak ada komentar yang ditemukan</p>
                </div>
            <?php else: ?>
                <div class="comments-container">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-card">
                            <div class="comment-header">
                                <div>
                                    <span class="comment-user"><?= htmlspecialchars($comment['username']) ?></span>
                                    <span> pada </span>
                                    <span class="comment-product"><?= htmlspecialchars($comment['product_name']) ?></span>
                                </div>
                                <div class="comment-date">
                                    <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                </div>
                            </div>
                            <div class="comment-content">
                                <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                            </div>
                            <div class="comment-actions">
                                <a href="edit_comments.php?id=<?= $comment['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">
                                    <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" name="delete_comment" class="btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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