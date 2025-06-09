<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Daftar warna yang tersedia
$availableColors = [
    'blue', 'purple', 'pink', 'white', 
    'red', 'yellow', 'green', 'black'
];

// Inisialisasi variabel error
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_flower'])) {
        // Handle add flower
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category = 'bunga';
        $color = $_POST['color'] ?? ''; // Ambil warna dari form
        
        // Validasi warna
        if (!in_array($color, $availableColors)) {
            $error = "Warna yang dipilih tidak valid";
        }
        
        // Handle image upload
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/images/products/'; // Ubah path upload
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$destination = $uploadDir . $filename;

if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
    $imagePath = 'assets/images/products/' . $filename; 
            } else {
                $error = "Gagal mengupload gambar";
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, image_path, category, color) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $imagePath, $category, $color]);
                header("Location: flowers.php?success=1");
                exit;
            } catch (PDOException $e) {
                $error = "Gagal menambahkan bunga: ".$e->getMessage();
            }
        }
    } elseif (isset($_POST['delete_flower'])) {
        // Handle delete flower (tetap sama)
        $id = $_POST['id'];
        
        try {
            // First get image path to delete file
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE id = ? AND category = 'bunga'");
            $stmt->execute([$id]);
            $flower = $stmt->fetch();
            
            if ($flower && !empty($flower['image_path'])) {
                $filePath = '../' . $flower['image_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Then delete from database
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND category = 'bunga'");
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
    $stmt = $pdo->query("SELECT * FROM products WHERE category = 'bunga' ORDER BY id DESC");
    $flowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Gagal mengambil data bunga: ".$e->getMessage();
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
    <style>
       
    </style>
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
            
            <div class="menu-item active" onclick="toggleSubmenu('data-master')">
                <i class="fas fa-database"></i> <span>Data Master</span>
            </div>
            <div class="submenu show" id="data-master">
                <a href="flowers.php" class="submenu-item active">
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
            <!-- Tombol untuk membuka modal -->
            <button class="add-btn" onclick="openModal()">
                <i class="fas fa-plus"></i> Tambah Bunga
            </button>
            
            <!-- Daftar Bunga -->
            <h2><i class="fas fa-flower"></i> Daftar Bunga</h2>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bunga</th>
                            <th>Warna</th>
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
                                <td>
                                    <span class="color-badge" style="background-color: <?= htmlspecialchars($flower['color']) ?>">
                                        <?= ucfirst(htmlspecialchars($flower['color'])) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($flower['description']) ?></td>
                                <td>
                                    <?php if ($flower['image_path']): ?>
                                        <img src="../<?= htmlspecialchars($flower['image_path']) ?>" alt="<?= htmlspecialchars($flower['name']) ?>" width="80">
                                    <?php else: ?>
                                        <span class="no-image">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_flowers.php?id=<?= $flower['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> 
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $flower['id'] ?>">
                                        <button type="submit" name="delete_flower" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus bunga ini?')">
                                            <i class="fas fa-trash"></i> 
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
    
    <!-- Modal Form Tambah Bunga -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h2><i class="fas fa-plus"></i> Tambah Data Bunga</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Bunga</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Warna Bunga</label>
                        <select id="color" name="color" required>
                            <option value="">Pilih Warna</option>
                            <option value="blue">blue</option>
                            <option value="purple">purple</option>
                            <option value="pink">pink</option>
                            <option value="white">white</option>
                            <option value="red">red</option>
                            <option value="yellow">yellow</option>
                            <option value="green">green</option>
                            <option value="back">black</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="image">Gambar Bunga</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="add_flower" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Data
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

        // Fungsi untuk modal
        function openModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        // Tutup modal jika klik di luar area modal
        window.onclick = function(event) {
            const modal = document.getElementById('addModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile responsive check
            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);
        });
    </script>
</body>
</html>