<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF tidak valid";
        header("Location: users.php");
        exit;
    }

    $userId = $_POST['user_id'] ?? null;
    if (!$userId) {
        $_SESSION['error'] = "User ID tidak valid";
        header("Location: users.php");
        exit;
    }
    
    if (isset($_POST['toggle_status'])) {
        $userId = $_POST['user_id'];
        
        try {
            // Toggle verification status (is_verified)
            $stmt = $pdo->prepare("UPDATE users SET is_verified = NOT is_verified WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['success'] = "Status verifikasi user berhasil diubah";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal mengubah status: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $_SESSION['success'] = "User berhasil dihapus";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menghapus user: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['role'])) {
        $newRole = $_POST['role'];
        $userId = $_POST['user_id'];
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $userId]);
            $_SESSION['success'] = "Role user berhasil diubah";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal mengubah role: " . $e->getMessage();
        }
    }
    
    header("Location: users.php");
    exit;
}

// Get all users
try {
    $stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at, is_verified FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data user: " . $e->getMessage();
    $users = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Sidebar -->
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
                <a href="comments.php" class="submenu-item">
                    <i class="fas fa-comments"></i> <span>Data Komentar</span>
                </a>
            </div>
            
            <div class="menu-item active">
                <i class="fas fa-users"></i> <span>Manajemen User</span>
            </div>
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </div>


    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Manajemen User</h1>
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
            <h2><i class="fas fa-users"></i> Daftar User</h2>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Nama Lengkap</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['full_name'] ?? '-') ?></td>
                                <td>
                                    <form method="POST" class="role-form">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <select name="role" class="role-select" onchange="this.form.submit()">
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="toggle_status" class="status-btn <?= $user['is_verified'] ? 'active' : 'inactive' ?>">
                                            <?= $user['is_verified'] ? 'Terverifikasi' : 'Belum Verifikasi' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" name="delete_user" class="btn btn-delete">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // ... (script JavaScript tetap sama) ...
            
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