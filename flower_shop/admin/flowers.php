<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_flower'])) {
        // Handle add flower
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        // Handle image upload
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/flowers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = 'uploads/flowers/' . $filename;
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO flowers (name, description, image_path) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $imagePath]);
            header("Location: flowers.php?success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menambahkan bunga: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_flower'])) {
        // Handle delete flower
        $id = $_POST['id'];
        
        try {
            // First get image path to delete file
            $stmt = $pdo->prepare("SELECT image_path FROM flowers WHERE id = ?");
            $stmt->execute([$id]);
            $flower = $stmt->fetch();
            
            if ($flower && $flower['image_path']) {
                $filePath = '../' . $flower['image_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Then delete from database
            $stmt = $pdo->prepare("DELETE FROM flowers WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: flowers.php?success=1");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menghapus bunga: " . $e->getMessage();
        }
    }
}

// Get all flowers
try {
    $stmt = $pdo->query("SELECT * FROM flowers ORDER BY id DESC");
    $flowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data bunga: " . $e->getMessage();
    $flowers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Bunga - FLORAZZIU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
          <div class="sidebar-header">
    <h2><a href="dashboard.php" style="color: inherit; text-decoration: none;">
        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
    </a></h2>
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
            <button class="logout-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Data Bunga</h1>
            <div class="user-info">
                <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Operasi berhasil dilakukan!
            </div>
        <?php endif; ?>
        
        <div class="data-section">
            <h2><i class="fas fa-flower"></i> Daftar Bunga</h2>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bunga</th>
                            <th>Deskripsi</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flowers as $index => $flower): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($flower['name']) ?></td>
                                <td><?= htmlspecialchars($flower['description']) ?></td>
                                <td>
                                    <?php if ($flower['image_path']): ?>
                                        <img src="../<?= htmlspecialchars($flower['image_path']) ?>" alt="<?= htmlspecialchars($flower['name']) ?>" width="80">
                                    <?php else: ?>
                                        <span class="no-image">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_flower.php?id=<?= $flower['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $flower['id'] ?>">
                                        <button type="submit" name="delete_flower" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus bunga ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="add-form">
                <h3><i class="fas fa-plus"></i> Tambah Data Bunga</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nama Bunga</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Gambar Bunga</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    
                    <button type="submit" name="add_flower" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle submenu (same as dashboard)
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            submenu.classList.toggle('show');
        }
    </script>
</body>
</html>