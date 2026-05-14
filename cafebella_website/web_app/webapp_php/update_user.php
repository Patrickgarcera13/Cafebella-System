<?php
session_start();
header("Content-Type: application/json");

// ✅ TAMA ITONG PATH — HINDI NA PAPALITAN
include __DIR__ . '/../../website_php/database.php';
require_once __DIR__ . '/../../website_php/auth_check.php';

// SIGURADUHIN ADMIN LANG ANG MAKAGAGAWA
if (!isAdmin()) {
    echo json_encode(["success" => false, "message" => "❌ Hindi ka pinapayagan!"]);
    exit;
}

// Kunin ang data na galing sa Settings.php
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'])) {
    echo json_encode(["success" => false, "message" => "❌ Walang User ID!"]);
    exit;
}

$user_id = $data['user_id'];

// --- PROTEKSIYON: BAWAL I-BAN ANG HULING ADMIN ---
if (isset($data['is_banned']) && $data['is_banned'] == 1) {
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'Admin' AND is_banned = 0 AND status = 'Approved'");
    $activeAdmins = $countStmt->fetch();

    $roleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $roleStmt->execute([$user_id]);
    $userRole = $roleStmt->fetchColumn();

    if ($activeAdmins['total'] <= 1 && $userRole === 'Admin') {
        echo json_encode(["success" => false, "message" => "❌ Hindi pwedeng i-ban ang huling Admin!"]);
        exit;
    }
}

// Simulan ang pag-update
try {
    // ✅ APPROVE USER
    if (isset($data['is_approved'])) {
        $stmt = $pdo->prepare("UPDATE users SET status = 'Approved', is_approved = 1 WHERE id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(["success" => true, "message" => "✅ User na-approve na!"]);
        exit;
    }

    // ✅ BAN USER
    if (isset($data['is_banned'])) {
        $stmt = $pdo->prepare("UPDATE users SET is_banned = ? WHERE id = ?");
        $stmt->execute([$data['is_banned'], $user_id]);
        echo json_encode(["success" => true, "message" => "✅ Matagumpay na na-update!"]);
        exit;
    }

    // ✅ PALITAN ANG ROLE
    if (isset($data['role'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$data['role'], $user_id]);
        echo json_encode(["success" => true, "message" => "✅ Role napalitan na!"]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "⚠️ Walang ginawang pagbabago."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "❌ Error: " . $e->getMessage()]);
}
?>