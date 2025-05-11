<?php
session_start();
$hideHeader = true; // Supaya header tidak muncul
include '../includes/header.php';
require '../config/database.php';  // Pastikan koneksi ke database sudah benar

$productId = $_GET['product_id'] ?? '';
// Jika sudah login, redirect ke add comment
if (isset($_SESSION['loggedin'])) {
    header("Location: ../products/add-comment.php?id=".($_GET['product_id']??''));
    exit;
}



// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi sederhana (dalam produksi, gunakan sistem auth yang lebih aman)
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah username dan password cocok
    if ($user && $password === $user['password']) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['profile_pic'] = $user['profile_picture'];
        $_SESSION['user_id'] = $user['id'];  // Simpan user_id di session
        
        // Redirect ke halaman add-comment.php setelah login
        header("Location: ../products/add-comment.php?id=" . ($_GET['product_id'] ?? ''));

        exit;
    } else {
        $error = "Username atau password salah";
    }
}
?>

<main class="login-page">
    <div class="login-container">
        <div class="login-card">
            <h1 class="welcome-text">Welcome Back!</h1>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter Username..." required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password..." required>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
