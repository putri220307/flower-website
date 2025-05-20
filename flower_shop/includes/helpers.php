<?php
// includes/helpers.php

/**
 * Mendapatkan path foto profil user
 * @param int $userId ID User
 * @return string Path gambar
 */
function getUserProfileImage($userId) {
    $defaultImage = '/assets/images/default-profile.jpg';
    
    // Cari file di database atau filesystem
    $userImage = $_SERVER['DOCUMENT_ROOT'] . '/uploads/users/profile/user_' . $userId . '_*.{jpg,jpeg,png,gif}';
    $files = glob($userImage, GLOB_BRACE);
    
    return !empty($files) ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $files[0]) : $defaultImage;
}

/**
 * Mendapatkan path gambar bunga
 * @param int $flowerId ID Bunga
 * @return string Path gambar
 */
function getFlowerImage($flowerId) {
    $defaultImage = '/assets/images/default-flower.jpg';
    
    $flowerImage = $_SERVER['DOCUMENT_ROOT'] . '/uploads/admin/flowers/flower_' . $flowerId . '_*.{jpg,jpeg,png}';
    $files = glob($flowerImage, GLOB_BRACE);
    
    return !empty($files) ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $files[0]) : $defaultImage;
}