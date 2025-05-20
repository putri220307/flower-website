<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get flower ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: flowers.php");
    exit;
}

// Get flower data
try {
    $stmt = $pdo->prepare("SELECT * FROM flowers WHERE id = ?");
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
    $currentImage = $flower['image_path'];
    
    // Handle image upload if new image provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists
        if ($currentImage && file_exists('../' . $currentImage)) {
            unlink('../' . $currentImage);
        }
        
        // Upload new image
        $uploadDir = '../uploads/flowers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $currentImage = 'uploads/flowers/' . $filename;
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE flowers SET name = ?, description = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$name, $description, $currentImage, $id]);
        header("Location: flowers.php?success=1");
        exit;
    } catch (PDOException $e) {
        $error = "Gagal memperbarui bunga: " . $e->getMessage();
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
        <!-- ... (same sidebar content) ... -->
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