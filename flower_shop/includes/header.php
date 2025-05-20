
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/helpers.php';
include __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<?php if (!isset($hideHeader) || $hideHeader !== true): ?>
<header class="main-header">
    <div class="container header-flex">
        <div class="logo">
            <img src="/assets/images/logo.png" alt="Flower Shop Logo" width="155" height="83">
        </div>

        <div class="search-box">
            <form action="../search.php" method="get">
                <input type="text" name="query" placeholder="Search.." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="user-area">
            <a href="../about-us.php" class="icon-btn">
                <i class="fas fa-exclamation-circle"></i>
            </a>
            <div class="divider"></div>
            <span class="username">User</span>
            <div class="user-actions">
    <?php if(isset($_SESSION['loggedin'])): ?>
        <div class="profile-dropdown">
            <div class="profile-toggle">
                <img src="../assets/uploads/<?= $_SESSION['profile_pic'] ?? 'default.jpg' ?>" alt="Profile">
                <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <ul class="dropdown-menu">
                <li><a href="../users/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    <?php else: ?>
        <a href="../auth/login.php" class="login-btn">Login</a>
    <?php endif; ?>
</div>
        </div>
    </div>
</header>
<?php endif; ?>