<?php
// includes/helpers.php

/**
 * Mendapatkan path foto profil user
 * @param int $userId ID User
 * @return string Path gambar
 */
function getUserProfileImage($userId) {
    // Sesuaikan ini dengan path default jika gambar profil tidak ditemukan
    $defaultImage = '/assets/images/default-profile.jpg'; 
    
    // Asumsi: Anda menyimpan gambar profil di folder 'uploads/users/profile/'
    // dan nama filenya mengikuti pola 'user_[ID_USER]_*.{ekstensi}'
    // Atau jika Anda menyimpan nama file di database, Anda akan melakukan query di sini.
    // Contoh ini mengasumsikan pencarian file di filesystem.
    
    // Jika Anda menyimpan nama file profil di database users, lebih baik ambil dari sana.
    // Contoh:
    // global $pdo; // Pastikan $pdo tersedia atau lewatkan sebagai argumen
    // $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    // $stmt->execute([$userId]);
    // $user = $stmt->fetch();
    // if ($user && !empty($user['profile_picture'])) {
    //     return '/assets/uploads/' . $user['profile_picture']; // Sesuaikan path
    // }
    
    // Jika Anda masih menggunakan metode glob:
    $userImage = $_SERVER['DOCUMENT_ROOT'] . '/uploads/users/profile/user_' . $userId . '_*.{jpg,jpeg,png,gif}';
    $files = glob($userImage, GLOB_BRACE);
    
    // Mengembalikan path relatif jika ditemukan, jika tidak, default
    return !empty($files) ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $files[0]) : $defaultImage;
}

/**
 * Mendapatkan path gambar bunga
 * @param int $flowerId ID Bunga
 * @return string Path gambar
 */
function getFlowerImage($flowerId) {
    // Sesuaikan ini dengan path default jika gambar bunga tidak ditemukan
    $defaultImage = '/assets/images/default-flower.jpg'; 
    
    // Asumsi: Anda menyimpan gambar bunga di folder 'uploads/admin/flowers/'
    // dan nama filenya mengikuti pola 'flower_[ID_BUNGA]_*.{ekstensi}'
    // Atau jika Anda menyimpan nama file di database, Anda akan melakukan query di sini.
    
    // Jika Anda menyimpan nama file bunga di database flowers, lebih baik ambil dari sana.
    // Contoh:
    // global $pdo; // Pastikan $pdo tersedia atau lewatkan sebagai argumen
    // $stmt = $pdo->prepare("SELECT image_url FROM flowers WHERE id = ?");
    // $stmt->execute([$flowerId]);
    // $flower = $stmt->fetch();
    // if ($flower && !empty($flower['image_url'])) {
    //     return '/assets/uploads/' . $flower['image_url']; // Sesuaikan path
    // }

    // Jika Anda masih menggunakan metode glob:
    $flowerImage = $_SERVER['DOCUMENT_ROOT'] . '/uploads/admin/flowers/flower_' . $flowerId . '_*.{jpg,jpeg,png}';
    $files = glob($flowerImage, GLOB_BRACE);
    
    // Mengembalikan path relatif jika ditemukan, jika tidak, default
    return !empty($files) ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $files[0]) : $defaultImage;
}

/**
 * Converts a datetime string to a human-readable "time ago" format.
 *
 * @param string $datetime The datetime string (e.g., '2023-01-15 10:30:00').
 * @param bool $full If true, returns all differences (e.g., '1 year, 2 months'), otherwise just the largest.
 * @return string The human-readable time difference.
 */
if (!function_exists('time_ago')) {
    function time_ago($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        // Calculate weeks from days, store in a local variable
        $diff_weeks = floor($diff->d / 7);
        $diff->d -= ($diff_weeks * 7); // Subtract weeks from days in the original diff object

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week', // Still keep 'w' in the string array for display
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        // Map the calculated weeks to the 'w' key in the diff object for consistent iteration
        // We'll create a temporary object or array to hold the values including 'w'
        $diff_values = (array) $diff; // Cast to array to easily add custom properties
        $diff_values['w'] = $diff_weeks; // Add the calculated weeks

        foreach ($string as $k => &$v) {
            // Use the combined values for checking and displaying
            if (isset($diff_values[$k]) && $diff_values[$k]) {
                $v = $diff_values[$k] . ' ' . $v . ($diff_values[$k] > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}


// Anda dapat menambahkan fungsi helper lainnya di sini di masa mendatang
// function format_currency($amount) { ... }
// function validate_email($email) { ... }

?>