<?php
session_start();
require_once __DIR__ . '/../../website_php/database.php';
require_once __DIR__ . '/../../website_php/auth_check.php';

header('Content-Type: application/json; charset=utf-8');

if (!isAdmin()) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
// $reason = trim($_POST['reason'] ?? ''); // Pansamantalang tinago

$allowed_status = ['Pending', 'Accepted', 'Declined'];
if (!$id || !in_array($status, $allowed_status)) {
    echo json_encode(["status" => "error", "message" => "Invalid booking ID or status"]);
    exit;
}

try {
    // ✅ I-update lang ang status — hindi muna isasama ang dahilan
    $stmt = $pdo->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(["status" => "success", "message" => "Updated successfully"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
