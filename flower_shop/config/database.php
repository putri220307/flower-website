<?php
// Pastikan error reporting aktif untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi database
try {
    $host = 'localhost';
    $dbname = 'flower_shop';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set atribut PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Optimasi tambahan (jika diperlukan)
    // $pdo->exec("SET SESSION wait_timeout = 600");
    
} catch (PDOException $e) {
    // Tangani error koneksi dengan lebih baik
    error_log('Database connection failed: ' . $e->getMessage());
    die('Database connection error. Please try again later.');
}

// Pastikan variabel $pdo ada sebelum digunakan
if (!isset($pdo)) {
    die('Database connection not initialized');
}
?>