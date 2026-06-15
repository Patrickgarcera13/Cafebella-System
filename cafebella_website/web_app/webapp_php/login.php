<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");
// Sa login.php at signup.php (na nasa web_app folder)
include __DIR__ . '/../../website_php/database.php';
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(["success"=>false,"message"=>"Email and password required"]);
    exit;
}
$email = $data['email'];
$password = $data['password'];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(["success"=>false,"message"=>"User not found"]);
        exit;
    }
    if (!password_verify($password, $user['password'])) {
        echo json_encode(["success"=>false,"message"=>"Incorrect password"]);
        exit;
    }
    if ($user['is_banned'] == 1) {
        echo json_encode(["success"=>false,"message"=>"Your account has been banned."]);
        exit;
    }
    if ($user['is_approved'] == 0) {
        echo json_encode(["success"=>false,"message"=>"Waiting for owner approval."]);
        exit;
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role'];

    echo json_encode(["success"=>true,"message"=>"Login successful"]);

} catch(PDOException $e) {
    echo json_encode(["success"=>false,"message"=>$e->getMessage()]);
}
?>
