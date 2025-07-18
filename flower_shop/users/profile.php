<?php
session_start();
require_once __DIR__ . '/../includes/helpers.php';
require '../config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Get current user data (before upload)
$stmt = $pdo->prepare("SELECT username, profile_picture, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "../assets/uploads/";

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
    $targetFile = $targetDir . $fileName;

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['profile_picture']['tmp_name']);
    finfo_close($finfo);

    if (in_array($mimeType, $allowedTypes)) {
        if ($_FILES['profile_picture']['size'] > 5000000) {
            $error = "File is too large. Maximum size is 5MB.";
        } else {
            $oldProfilePic = $user['profile_picture'] ?? null;

            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->execute([$fileName, $_SESSION['user_id']]);

                $_SESSION['profile_pic'] = $fileName;
                $success = "Profile picture updated successfully!";

                if (!empty($oldProfilePic) && $oldProfilePic !== 'default.jpg' && $oldProfilePic !== $fileName) {
                    $oldFile = $targetDir . $oldProfilePic;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            } else {
                $error = "Sorry, there was an error uploading your file.";
                error_log("Upload error: Failed to move file to " . $targetFile);
            }
        }
    } else {
        $error = "Please upload a valid image file (JPG, JPEG, PNG, GIF).";
    }
}

// Refresh user data
$stmt = $pdo->prepare("SELECT username, profile_picture, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Set session variables if not set
if (!isset($_SESSION['email']) && isset($user['email'])) {
    $_SESSION['email'] = $user['email'];
}
if (!isset($_SESSION['created_at']) && isset($user['created_at'])) {
    $_SESSION['created_at'] = $user['created_at'];
}

$user_id = $_SESSION['user_id'] ?? 0;

// --- Query to fetch user comments ---
$stmt_comments = $pdo->prepare("
    SELECT c.comment, c.created_at, p.name as product_name, p.id as product_id
    FROM comments c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt_comments->execute([$user_id]);
$user_comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

// --- Query to fetch user liked products ---
$stmt_likes = $pdo->prepare("
    SELECT p.id as product_id, p.name as product_name, p.image_path as product_image
    FROM likes l
    JOIN products p ON l.product_id = p.id
    WHERE l.user_id = ?
    ORDER BY l.created_at DESC
");
$stmt_likes->execute([$user_id]);
$user_likes = $stmt_likes->fetchAll(PDO::FETCH_ASSOC);

// --- Query to fetch user saved products ---
$stmt_saves = $pdo->prepare("
    SELECT p.id as product_id, p.name as product_name, p.image_path as product_image
    FROM saved_products s
    JOIN products p ON s.product_id = p.id
    WHERE s.user_id = ?
    ORDER BY s.created_at DESC
");
$stmt_saves->execute([$user_id]);
$user_saves = $stmt_saves->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Flower Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h1 {
            color: #584C43;
            font-size: 28px;
        }

        .profile-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 30px;
        }

        .profile-picture {
            flex: 0 0 200px;
            text-align: center;
        }

        .profile-picture img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #E2CEB1;
        }

        .profile-form {
            flex: 1;
            min-width: 250px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #584C43;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #E2CEB1;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            margin-top: 10px;
            background-color: #4E73DE;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* --- Styles for Tab Navigation --- */
        .profile-tabs {
            display: flex;
            justify-content: space-around;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tab-button {
            flex: 1;
            min-width: 120px;
            background: none;
            border: none;
            padding: 15px 10px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 500;
            color: #777;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            text-align: center;
        }

        .tab-button:hover {
            color: #584C43;
            border-bottom-color: #E2CEB1;
        }

        .tab-button.active {
            color: #584C43;
            border-bottom-color: #4E73DE;
            font-weight: 600;
        }

        /* --- Tab Content --- */
        .tab-content {
            display: none;
            padding-top: 20px;
        }

        .tab-content.active {
            display: block;
        }

        /* --- Styles for History Lists (Comments, Likes, Saved) --- */
        .tab-content h2 {
            color: #584C43;
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        .comment-item-profile, .product-item-profile {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-item-profile:hover, .comment-item-profile:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .comment-item-profile .comment-product-link {
            font-size: 0.9em;
            color: #007bff;
            text-decoration: none;
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .comment-item-profile .comment-product-link:hover {
            text-decoration: underline;
        }

        .comment-item-profile .comment-date {
            font-size: 0.85em;
            color: #777;
            text-align: right;
        }

        /* --- New Styles for Likes and Saved Lists --- */
        .product-item-profile .product-info {
            flex-grow: 1;
        }
        
        .product-item-profile .product-info h3 {
            margin: 0;
            font-size: 1.1em;
            color: #333;
            line-height: 1.4;
        }

        .product-item-profile .product-info a {
            text-decoration: none;
            color: inherit;
        }

        .product-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-left: 20px;
            border: 1px solid #ddd;
        }

        .no-content-message {
            text-align: center;
            color: #777;
            font-style: italic;
            padding: 20px;
            border: 1px dashed #e0e0e0;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h1>My Profile</h1>
            </div>

            <?php if(isset($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="profile-content">
                <div class="profile-picture">
                    <img src="../assets/uploads/<?= htmlspecialchars($user['profile_picture'] ?? 'default.jpg') ?>"
                                alt="Profile Picture"
                                onerror="this.src='../assets/uploads/default.jpg'">

                    <form method="POST" enctype="multipart/form-data" class="mt-3">
                        <input type="file" name="profile_picture" id="profile_picture"
                                accept="image/jpeg,image/png,image/gif"
                                style="display: none;" required>
                        <label for="profile_picture" class="btn btn-primary">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>

                <div class="profile-form">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Member Since</label>
                        <input type="text" value="<?= date('F Y', strtotime($_SESSION['created_at'] ?? 'now')) ?>" readonly>
                    </div>

                    <div class="form-actions">
                        <a href="../index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="../auth/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            <div class="profile-tabs">
                <button class="tab-button active" data-tab="comments"><i class="fas fa-comments"></i> Comments</button>
                <button class="tab-button" data-tab="likes"><i class="fas fa-heart"></i> Likes</button>
                <button class="tab-button" data-tab="saved"><i class="fas fa-bookmark"></i> Saved</button>
            </div>

            <div id="comments" class="tab-content active">
                <h2>Your Comments History</h2>
                <?php if (!empty($user_comments)): ?>
                    <?php foreach ($user_comments as $comment): ?>
                        <div class="comment-item-profile">
                            <div class="comment-info">
                                <a href="../products/product-detail.php?id=<?= $comment['product_id'] ?>" class="comment-product-link">
                                    On Product: <?= htmlspecialchars($comment['product_name']) ?>
                                </a>
                                <p><strong>Comment:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
                            </div>
                            <div class="comment-date"><?= date('d M Y H:i', strtotime($comment['created_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content-message">
                        You haven't made any comments yet.
                    </div>
                <?php endif; ?>
            </div>

            <div id="likes" class="tab-content">
                <h2>Your Liked Products</h2>
                <?php if (!empty($user_likes)): ?>
                    <?php foreach ($user_likes as $liked_product): ?>
                        <a href="../products/product-detail.php?id=<?= $liked_product['product_id'] ?>" class="product-item-profile">
                            <div class="product-info">
                                <h3><?= htmlspecialchars($liked_product['product_name']) ?></h3>
                            </div>
                            <?php 
                                $image_src = htmlspecialchars($liked_product['product_image'] ?? '');
                                $final_src = (strpos($image_src, 'http') === 0 || strpos($image_src, '/') === 0) 
                                             ? $image_src 
                                             : '/' . $image_src;
                            ?>
                            <img src="<?= $final_src ?>"
                                 alt="<?= htmlspecialchars($liked_product['product_name']) ?>"
                                 class="product-item-image"
                                 onerror="this.onerror=null; this.src='/assets/images/products/default_product.jpg'">
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content-message">
                        You haven't liked any products yet.
                    </div>
                <?php endif; ?>
            </div>

            <div id="saved" class="tab-content">
                <h2>Your Saved Products</h2>
                <?php if (!empty($user_saves)): ?>
                    <?php foreach ($user_saves as $saved_product): ?>
                        <a href="../products/product-detail.php?id=<?= $saved_product['product_id'] ?>" class="product-item-profile">
                            <div class="product-info">
                                <h3><?= htmlspecialchars($saved_product['product_name']) ?></h3>
                            </div>
                            <?php 
                                $image_src = htmlspecialchars($saved_product['product_image'] ?? '');
                                $final_src = (strpos($image_src, 'http') === 0 || strpos($image_src, '/') === 0) 
                                             ? $image_src 
                                             : '/' . $image_src;
                            ?>
                            <img src="<?= $final_src ?>"
                                 alt="<?= htmlspecialchars($saved_product['product_name']) ?>"
                                 class="product-item-image"
                                 onerror="this.onerror=null; this.src='/assets/images/products/default_product.jpg'">
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content-message">
                        You haven't saved any products yet.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // For profile picture preview
            document.getElementById('profile_picture').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.querySelector('.profile-picture img').src = event.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // --- Tab Navigation Logic ---
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.dataset.tab;
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });

            // Set 'comments' tab as default active when the page loads
            document.querySelector('.tab-button[data-tab="comments"]').click();
        });
    </script>
</body>
</html>