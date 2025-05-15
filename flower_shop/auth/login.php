<?php
session_start();
$hideHeader = true; // Supaya header tidak muncul
include '../includes/header.php';
require '../config/database.php';  // Pastikan koneksi ke database sudah benar

$productId = $_GET['product_id'] ?? '';

// Handle remember me cookie
if (isset($_COOKIE['remember_user'])) {
    $rememberData = json_decode($_COOKIE['remember_user'], true);
    if (isset($rememberData['username'])) {
        $rememberUsername = $rememberData['username'];
    }
}

// Jika sudah login, redirect ke halaman yang sesuai berdasarkan role
if (isset($_SESSION['loggedin'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../products/add-comment.php?id=".($_GET['product_id']??''));
    }
    exit;
}

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validasi login
    $stmt = $pdo->prepare("SELECT id, password, role, profile_picture FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek apakah username dan password cocok
    if ($user && $password === $user['password']) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['profile_pic'] = $user['profile_picture'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];  // Simpan role user di session
        
        // Set cookie jika remember me dicentang
        if ($remember) {
            $cookieValue = json_encode(['username' => $username]);
            setcookie('remember_user', $cookieValue, time() + (30 * 24 * 60 * 60), '/'); // 30 hari
        } else {
            // Hapus cookie jika ada
            if (isset($_COOKIE['remember_user'])) {
                setcookie('remember_user', '', time() - 3600, '/');
            }
        }
        
        // Redirect ke halaman yang sesuai berdasarkan role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../products/add-comment.php?id=" . ($_GET['product_id'] ?? ''));
        }
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
                    <input type="text" id="username" name="username" placeholder="Enter Username..." 
                           value="<?php echo isset($rememberUsername) ? htmlspecialchars($rememberUsername) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password..." required>
                </div>

                <div class="form-group remember-me">
                    <input type="checkbox" id="remember" name="remember" <?php echo isset($rememberUsername) ? 'checked' : ''; ?>>
                    <label for="remember">Remember Me</label>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>