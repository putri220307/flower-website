<?php
include '../includes/header.php';
require '../config/database.php';

// [Kode PHP lainnya tetap sama...]
// Ambil ID produk dari URL
$product_id = $_GET['id'] ?? 0;

// Query untuk memeriksa keberadaan produk
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit;
}

// Handle delete comment
if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['user_id'] ?? 0;
    
    // Verify user owns the comment before deleting
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);
    
    // Refresh page
    header("Location: comments.php?id=" . $product_id);
    exit;
}

// Query untuk mendapatkan komentar
$sort = $_GET['sort'] ?? 'newest';
$orderBy = match($sort) {
    'a-z' => 'users.username ASC',
    'z-a' => 'users.username DESC',
    'oldest' => 'comments.created_at ASC',
    default => 'comments.created_at DESC'
};

$stmt = $pdo->prepare("
    SELECT comments.*, users.username 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.product_id = ? 
    ORDER BY $orderBy
");

$stmt->execute([$product_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Comment Actions Dropdown */
    .comment-actions {
        position: relative;
        margin: auto;
    }

    .dots-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        padding: 5px 10px;
        color: #666;
    }
    .dots-btn, .filter-btn {
        cursor: pointer !important;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }
    .dropdown, .filter-dropdown {
        position: relative !important;
        display: inline-block !important;
    }

    .dropdown-content {
        display: none !important;
        position: absolute;
        right: 0;
        background-color: #f9f9f9;
        min-width: 120px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1000; /* Dinaikkan */
        border-radius: 4px;
        /* Tambahkan ini untuk memastikan visibilitas awal jika ada opacity/visibility di tempat lain */
        visibility: hidden; /* Sembunyikan secara default */
        opacity: 0;       /* Sembunyikan secara default */
        transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out; /* Transisi halus */
    }

    .dropdown-content a, .dropdown-content button {
        color: black;
        padding: 8px 12px;
        text-decoration: none;
        display: block;
        text-align: left;
        width: 100%;
        border: none;
        background: none;
        cursor: pointer;
    }

    .dropdown-content a:hover, .dropdown-content button:hover {
        background-color: #f1f1f1;
    }

    /* Ditambahkan */
    .dropdown-content.show {
        display: block !important;
        /* Ini yang kita tambahkan! */
        visibility: visible !important; /* Pastikan terlihat */
        opacity: 1 !important;       /* Pastikan penuh */
    }

    .delete-btn {
        color: #ff4444 !important;
    }
</style>

<main>
    <div class="container">
        <h1 class="comments-title">Comment</h1> <div class="comments-filter">
            <div class="filter-dropdown">
                <button class="filter-btn">Sort by</button>
                <div class="dropdown-content">
                    <a href="?id=<?= $product_id ?>&sort=newest">Terbaru</a>
                    <a href="?id=<?= $product_id ?>&sort=oldest">Terlama</a>
                    <a href="?id=<?= $product_id ?>&sort=a-z">A-Z</a>
                    <a href="?id=<?= $product_id ?>&sort=z-a">Z-A</a>
                </div>
            </div>
        </div>
        
        <div class="comments-table">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $index => $comment): ?>
                    <div class="comment-item">
                        <div class="comment-number"><?= $index + 1 ?></div>
                        <div class="comment-user"><?= htmlspecialchars($comment['username']) ?></div>
                        <div class="comment-text"><?= htmlspecialchars($comment['comment']) ?></div>
                        <div class="comment-date"><?= date('d M Y', strtotime($comment['created_at'])) ?></div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                            <div class="comment-actions">
                                <div class="dropdown">
                                    <button class="dots-btn">⋮</button>
                                    <div class="dropdown-content">
                                        <a href="edit-comment.php?id=<?= $comment['id'] ?>">Edit</a>
                                        <form method="POST" onsubmit="return confirm('Yakin hapus komentar?')">
                                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                            <button type="submit" name="delete_comment" class="delete-btn">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-comments">Belum ada komentar.</div> <?php endif; ?>
        </div>
        
        <div class="comments-actions">
            <a href="product-detail.php?id=<?= $product_id ?>" class="back-btn">Back</a>
            <a href="add-comment.php?id=<?= $product_id ?>" class="add-comment-btn">Add Comment</a>
        </div>
    </div>
</main>

<script>
    // FUNGSI UTAMA YANG PASTI BERJALAN
    function initDropdowns() {
        // Fungsi untuk menutup semua dropdown yang terbuka
        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown-content').forEach(d => {
                d.classList.remove('show');
            });
        }

        // Event listener untuk tombol titik tiga (kebab menu)
        document.querySelectorAll('.dots-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah event click menyebar ke 'document'
                const menu = this.nextElementSibling; // Mendapatkan elemen dropdown-content yang terhubung
                
                // Tutup semua dropdown lain sebelum membuka yang ini
                closeAllDropdowns();

                // Toggle class 'show' pada dropdown yang diklik
                menu.classList.toggle('show');
            });
        });

        // Event listener untuk tombol sort
        const sortBtn = document.querySelector('.filter-btn');
        if (sortBtn) {
            sortBtn.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah event click menyebar ke 'document'
                const menu = this.nextElementSibling; // Mendapatkan elemen dropdown-content yang terhubung
                
                // Tutup semua dropdown lain sebelum membuka yang ini
                closeAllDropdowns();

                // Toggle class 'show' pada dropdown sort
                menu.classList.toggle('show');
            });
        }

        // Event listener untuk menutup semua dropdown saat mengklik di luar area dropdown
        document.addEventListener('click', function(e) {
            // Periksa jika klik terjadi di luar elemen .dropdown atau .filter-dropdown
            if (!e.target.closest('.dropdown') && !e.target.closest('.filter-dropdown')) {
                closeAllDropdowns();
            }
        });
    }

    // Jalankan fungsi initDropdowns saat dokumen selesai dimuat
    // Menggunakan addEventListener untuk DOMContentLoaded adalah metode yang paling andal
    document.addEventListener('DOMContentLoaded', initDropdowns);
</script>
    
<?php include '../includes/footer.php'; ?>