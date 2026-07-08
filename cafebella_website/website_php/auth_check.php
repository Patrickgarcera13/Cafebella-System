<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db_path = __DIR__ . '/database.php';
if (!file_exists($db_path)) {
    die("Database file not found: " . $db_path);
}
require_once $db_path;

// 🔒 Block unlogged users — CORRECT redirect to web app login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../web_app/Login.html");
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
    header("Location: ../web_app/Login.html?error=access_denied");
    exit();
}

// Function to check if the current user is an admin sa WEB APP 
function isAdmin() {
    global $current_user;
    return isset($current_user['role']) && ($current_user['role'] === 'Admin');
}

// 🛡️ NEW: Require Admin only (hard block)
function require_admin() {
    if (!isAdmin()) {
        http_response_code(403);
        die("<h1>403 Forbidden</h1><p>Only administrators can access this page.</p>");
    }
}

// 🛡️ NEW: Require Admin OR Staff (hard block)
function require_admin_or_staff() {
    global $current_user;
    if (!in_array($current_user['role'], ['Admin', 'Staff'])) {
        http_response_code(403);
        die("<h1>403 Forbidden</h1><p>Only authorized staff can access this page.</p>");
    }
}
?>
