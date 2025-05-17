<?php
session_start();
$hideHeader = true; // Supaya header tidak muncul
include '../includes/header.php';
require '../config/database.php';

$productId = $_GET['product_id'] ?? '';
$redirectFrom = $_GET['from'] ?? ''; // Tambahkan parameter untuk mengetahui asal halaman

// Handle remember me cookie
if (isset($_COOKIE['remember_user'])) {
    $rememberData = json_decode($_COOKIE['remember_user'], true);
    if (isset($rememberData['username'])) {
        $rememberUsername = $rememberData['username'];
    }
}

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION['loggedin'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        // Logika redirect baru berdasarkan asal halaman
        if ($redirectFrom === 'comment') {
            header("Location: ../products/add-comment.php?id=".$productId);
        } else {
            header("Location: ../index.php");
        }
    }
    exit;
}

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Validasi login
    $stmt = $pdo->prepare("SELECT id, password, role, profile_picture, is_verified, created_at 
                       FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Periksa apakah akun sudah diverifikasi atau merupakan akun yang sudah ada sebelumnya
        $isExistingAccount = !isset($user['created_at']) || 
                        (isset($user['created_at']) && strtotime($user['created_at']) < strtotime('-1 day')); // Anggap akun yang dibuat >1 hari lalu adalah akun existing
        
        if ($user['is_verified'] || $isExistingAccount) {
            if ($password === $user['password']) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['profile_pic'] = $user['profile_picture'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                
                // Set cookie jika remember me dicentang
                if ($remember) {
                    $cookieValue = json_encode(['username' => $username]);
                    setcookie('remember_user', $cookieValue, time() + (30 * 24 * 60 * 60), '/');
                } else {
                    if (isset($_COOKIE['remember_user'])) {
                        setcookie('remember_user', '', time() - 3600, '/');
                    }
                }
                
                // Redirect berdasarkan role dan asal halaman
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    // Logika redirect baru
                    if (isset($_POST['redirect_from']) && $_POST['redirect_from'] === 'comment') {
                        header("Location: ../products/add-comment.php?id=".$productId);
                    } else {
                        header("Location: ../index.php");
                    }
                }
                exit;
            } else {
                $loginError = "Username atau password salah";
            }
        } else {
            $loginError = "Akun Anda belum diverifikasi oleh admin. Silakan tunggu verifikasi.";
        }
    } else {
        $loginError = "Username atau password salah";
    }
}

// Handle registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['reg_username'] ?? '';
    $password = $_POST['reg_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validasi registrasi
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $regError = "Semua field harus diisi";
    } elseif ($password !== $confirmPassword) {
        $regError = "Password dan konfirmasi password tidak cocok";
    } else {
        // Cek apakah username sudah ada
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $regError = "Username sudah digunakan";
        } else {
            // Default role adalah 'user' dan is_verified false
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, is_verified) VALUES (?, ?, 'user', FALSE)");
            if ($stmt->execute([$username, $password])) {
                $regSuccess = "Registrasi berhasil! Silakan tunggu verifikasi dari admin.";
            } else {
                $regError = "Gagal melakukan registrasi. Silakan coba lagi.";
            }
        }
    }
}
?>

<main class="login-page">
    <div class="login-container">
        <div class="login-card">
            <h1 class="welcome-text">Welcome!</h1>

            <!-- Tab untuk login/register -->
            <div class="auth-tabs">
                <button class="tab-btn active" data-tab="login">Login</button>
                <button class="tab-btn" data-tab="register">Register</button>
            </div>

            <!-- Login Form -->
            <div id="login-tab" class="auth-form active">
                <?php if (isset($loginError)): ?>
                    <div class="error-message"><?php echo $loginError; ?></div>
                <?php endif; ?>

                <form method="POST" class="login-form">
                    <input type="hidden" name="login" value="1">
                    <input type="hidden" name="redirect_from" value="<?php echo $redirectFrom === 'comment' ? 'comment' : 'index'; ?>">
                    
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

            <!-- Register Form -->
            <div id="register-tab" class="auth-form">
                <?php if (isset($regError)): ?>
                    <div class="error-message"><?php echo $regError; ?></div>
                <?php endif; ?>
                
                <?php if (isset($regSuccess)): ?>
                    <div class="success-message"><?php echo $regSuccess; ?></div>
                <?php else: ?>
                    <form method="POST" class="register-form">
                        <input type="hidden" name="register" value="1">
                        
                        <div class="form-group">
                            <label for="reg_username">Username</label>
                            <input type="text" id="reg_username" name="reg_username" placeholder="Choose a username" required>
                        </div>

                        <div class="form-group">
                            <label for="reg_password">Password</label>
                            <input type="password" id="reg_password" name="reg_password" placeholder="Create a password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                        </div>

                        <button type="submit" class="register-btn">Register</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
// Tab switching functionality
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Show the corresponding form
        const tabName = btn.getAttribute('data-tab');
        document.querySelectorAll('.auth-form').forEach(form => form.classList.remove('active'));
        document.getElementById(`${tabName}-tab`).classList.add('active');
    });
});
</script>

<?php include '../includes/footer.php'; ?>