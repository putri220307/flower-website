<?php
session_start();
require __DIR__.'/../config/database.php';

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF tidak valid";
        header("Location: sliders.php");
        exit;
    }

    // Check if file was uploaded
    if (!isset($_FILES['slider_image']) || $_FILES['slider_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Silakan pilih file gambar";
        header("Location: sliders.php");
        exit;
    }

    // File validation
    $file = $_FILES['slider_image'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowedTypes)) {
        $_SESSION['error'] = "Hanya file JPG, PNG, atau GIF yang diperbolehkan";
        header("Location: sliders.php");
        exit;
    }

    if ($file['size'] > $maxSize) {
        $_SESSION['error'] = "Ukuran file terlalu besar (maksimal 2MB)";
        header("Location: sliders.php");
        exit;
    }

    // Create upload directory if not exists
    $uploadDir = '../assets/images/sliders/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'slider_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = $uploadDir . $filename;
    $relativePath = 'assets/images/sliders/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        try {
            // Save to database
            $stmt = $pdo->prepare("INSERT INTO sliders (image_path) VALUES (?)");
            $stmt->execute([$relativePath]);
            
            $_SESSION['success'] = "Slider berhasil diupload";
        } catch (PDOException $e) {
            // Delete file if database insert fails
            unlink($destination);
            $_SESSION['error'] = "Gagal menyimpan data slider: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Gagal mengupload file";
    }
    // Contoh di upload_slider.php
function resizeImage($file, $targetWidth) {
  list($width, $height) = getimagesize($file);
  $ratio = $height / $width;
  $targetHeight = $targetWidth * $ratio;
  
  $src = imagecreatefromjpeg($file);
  $dst = imagecreatetruecolor($targetWidth, $targetHeight);
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
  
  imagejpeg($dst, $file);
}

    header("Location: sliders.php");
    exit;
}