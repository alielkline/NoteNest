<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/database.php';

$pdo = Database::getConnection();
$userModel = new User($pdo);

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $user = $userModel->findByRememberToken($token);
    
    if ($user && strtotime($user['remember_token_expiry']) > time()) {
        // Valid token, log user in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['profile_image'] = $user['profile_image'];
        
        // Optionally refresh token expiration to extend login
        $newExpiry = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60);
        $userModel->updateRememberToken($user['id'], $token, $newExpiry);
        setcookie('remember_token', $token, time() + 30*24*60*60, "/", "", false, true);
    } else {
        // Invalid or expired token, clear cookie
        setcookie('remember_token', '', time() - 3600, "/");
    }
}
