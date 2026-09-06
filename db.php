<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'game_store';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Fetch web settings
$stmtSetting = $pdo->query("SELECT * FROM settings WHERE id = 1");
$webSetting = $stmtSetting->fetch();
if (!$webSetting) {
    $webSetting = [
        'site_name' => 'GameShop',
        'site_title' => 'ร้านค้าไอเทมเกมออนไลน์',
        'wallet_phone' => '0800000000',
        'contact_link' => '#'
    ];
}

// Sync user credit & session if logged in
if (isset($_SESSION['user_id'])) {
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $u = $stmtUser->fetch();
    if ($u) {
        $_SESSION['credit'] = $u['credit'];
        $_SESSION['role'] = $u['role'];
        $_SESSION['username'] = $u['username'];
        $_SESSION['user_img'] = $u['profile_img'];
    }
}
?>
