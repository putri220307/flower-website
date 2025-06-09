    <?php
    session_start();
    require __DIR__.'/../config/database.php';

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Security check
    if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit;
    }

    // Available colors
    $availableColors = [
        'blue', 'purple', 'pink', 'white', 
        'red', 'yellow', 'green', 'black'
    ];

    // Get flower ID
    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: flowers.php");
        exit;
    }

    // Get flower data - USING THE CORRECT TABLE (products with category filter)
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND category = 'bunga'");
        $stmt->execute([$id]);
        $flower = $stmt->fetch();
        
        if (!$flower) {
            header("Location: flowers.php");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: flowers.php?error=1");
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $color = $_POST['color'] ?? $flower['color'];
        $currentImage = $flower['image_path'];
        
        // Validate color
        if (!in_array($color, $availableColors)) {
            $error = "Warna yang dipilih tidak valid";
        }
        
        // Handle image upload if new image provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if ($currentImage && file_exists('../' . $currentImage)) {
                unlink('../' . $currentImage);
            }
            
            // Upload new image - consistent path with flowers.php
            $uploadDir = '../uploads/admin/flowers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $currentImage = '/uploads/admin/flowers/' . $filename;
            } else {
                $error = "Gagal mengupload gambar";
            }
        }
        
        if (empty($error)) {
            try {
                // Update all fields including color
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, image_path = ?, color = ? WHERE id = ?");
                $stmt->execute([$name, $description, $currentImage, $color, $id]);
                header("Location: flowers.php?success=1");
                exit;
            } catch (PDOException $e) {
                $error = "Gagal memperbarui bunga: " . $e->getMessage();
            }
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Bunga - FLORAZZIU</title>
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
                <h1>Edit Bunga</h1>
                <div class="user-info">
                    <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                </div>
            </div>
            
            <div class="data-section">
                <a href="flowers.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Bunga
                </a>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="edit-form">
                    <div class="form-group">
                        <label for="name">Nama Bunga</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($flower['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="3" required><?= htmlspecialchars($flower['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Warna Bunga</label>
                        <select id="color" name="color" required>
                            <option value="">Pilih Warna</option>
                            <option value="blue" <?= $flower['color'] === 'blue' ? 'selected' : '' ?>>blue</option>
                            <option value="purple" <?= $flower['color'] === 'purple' ? 'selected' : '' ?>>purple</option>
                            <option value="pink" <?= $flower['color'] === 'pink' ? 'selected' : '' ?>>pink</option>
                            <option value="white" <?= $flower['color'] === 'white' ? 'selected' : '' ?>>white</option>
                            <option value="red" <?= $flower['color'] === 'red' ? 'selected' : '' ?>>red</option>
                            <option value="yellow" <?= $flower['color'] === 'yellow' ? 'selected' : '' ?>>yellow</option>
                            <option value="green" <?= $flower['color'] === 'green' ? 'selected' : '' ?>>green</option>
                            <option value="black" <?= $flower['color'] === 'black' ? 'selected' : '' ?>>black</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Gambar Bunga</label>
                        <?php if ($flower['image_path']): ?>
                            <div class="current-image">
                                <img src="../<?= htmlspecialchars($flower['image_path']) ?>" alt="Current Image" width="150">
                                <p>Gambar saat ini</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*">
                        <small>Biarkan kosong jika tidak ingin mengubah gambar</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </body>
    </html>