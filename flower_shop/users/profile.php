<?php
session_start();
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

                // Optional: Prevent form resubmission
                // header("Location: profile.php?success=1");
                // exit;
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
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
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
    </script>
</body>
</html>
