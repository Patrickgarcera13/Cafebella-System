<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
// Sa login.php at signup.php (na nasa web_app folder)
include __DIR__ . '/../../website_php/database.php';
$data = json_decode(file_get_contents("php://input"), true);
if (
    empty($data['full_name']) ||
    empty($data['email']) ||
    empty($data['password'])
) {
    echo json_encode(["success"=>false,"message"=>"All fields required"]);
    exit;
}
$full_name = $data['full_name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$is_approved = 0;
$is_banned = 0;
$status = 'Pending';
$role = 'Staff';
try {
    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        echo json_encode(["success"=>false,"message"=>"Email already exists"]);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, is_approved, is_banned, status, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $password, $is_approved, $is_banned, $status, $role]);
    echo json_encode(["success"=>true,"message"=>"Signup successful! Waiting for owner approval."]);
} catch(PDOException $e) {
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
?>