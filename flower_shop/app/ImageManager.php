<?php
// app/ImageManager.php

class ImageManager {
    const DEFAULT_PROFILE = '/assets/images/default-profile.jpg';
    const DEFAULT_FLOWER = '/assets/images/default-flower.jpg';
    
    public static function getUserProfile($userId) {
        return self::findImage(
            '/uploads/users/profile/user_' . $userId,
            self::DEFAULT_PROFILE
        );
    }
    
    public static function getFlowerImage($flowerId) {
        return self::findImage(
            '/uploads/admin/flowers/flower_' . $flowerId,
            self::DEFAULT_FLOWER
        );
    }
    
    private static function findImage($pattern, $default) {
        $fullPattern = $_SERVER['DOCUMENT_ROOT'] . $pattern . '_*.{jpg,jpeg,png,gif}';
        $files = glob($fullPattern, GLOB_BRACE);
        
        return !empty($files) 
            ? str_replace($_SERVER['DOCUMENT_ROOT'], '', $files[0])
            : $default;
    }
}