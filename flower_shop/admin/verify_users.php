<?php
session_start();
require '../config/database.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ..auth/login.php");
    exit;
}

// Handle verifikasi user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE WHERE id = ?");
    $stmt->execute([$userId]);
    $_SESSION['message'] = "User berhasil diverifikasi.";
header("Location: " . $_SERVER['PHP_SELF']);
exit;
}

// Handle penghapusan user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND is_verified = FALSE");
    $stmt->execute([$userId]);
     $_SESSION['message'] = "User berhasil dihapus.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ambil daftar user yang belum diverifikasi
$stmt = $pdo->prepare("SELECT id, username, created_at FROM users WHERE is_verified = FALSE ORDER BY created_at DESC");
$stmt->execute();
$unverifiedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<div class="container">
    <h1>Verifikasi User Baru</h1>
    
    <table class="table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Tanggal Registrasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unverifiedUsers as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="verify_user" class="btn btn-success">Verifikasi</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                        </form>
                    </td>

                </tr>
            <?php endforeach; ?>
            
            <?php if (empty($unverifiedUsers)): ?>
                <tr>
                    <td colspan="3">Tidak ada user yang perlu diverifikasi</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>