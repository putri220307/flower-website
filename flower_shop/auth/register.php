<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $profile_picture = 'default.png';

    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi";
    } else {
        // Cek username sudah ada
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->rowCount() > 0) {
            $error = "Username sudah terdaftar.";
        } else {
            try {
                // Gunakan transaksi untuk memastikan data konsisten
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare("
                    INSERT INTO users 
                    (username, password, profile_picture, is_verified, role, created_at) 
                    VALUES (?, ?, ?, 0, 'user', NOW())
                ");
                $stmt->execute([$username, $password, $profile_picture]);
                
                $pdo->commit();
                
                $success = "Registrasi berhasil! Tunggu verifikasi admin.";
                $_SESSION['register_success'] = true;
                header("Location: login.php?registered=1");
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Terjadi kesalahan sistem. Silakan coba lagi.";
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}
?>

<main class="register-page">
    <div class="register-container">
        <div class="register-card">
            <h1>Registrasi</h1>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php elseif (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
                <a href="login.php" class="btn">Kembali ke Login</a>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="register-btn">Daftar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>
