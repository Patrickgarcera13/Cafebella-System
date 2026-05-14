<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$db_path = __DIR__ . '/database.php';
if (!file_exists($db_path)) {
    die("Database file not found: " . $db_path);
}
require_once $db_path;

// Check kung naka-login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
if (!isset($pdo) || $pdo == null) {
    die("Database connection failed. Please check database.php");
}
$stmt = $pdo->prepare("SELECT is_approved, is_banned, role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$current_user || $current_user['is_approved'] == 0 || $current_user['is_banned'] == 1) {
    session_destroy();
    header("Location: login.html?error=access_denied");
    exit();
}
function isAdmin() {
    global $current_user;
    return isset($current_user['role']) && ($current_user['role'] === 'Admin');
}
?>