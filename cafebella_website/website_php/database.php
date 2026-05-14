<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "cafebella_db";

try {
    $pdo = new PDO("mysql:host=".$host.";dbname=".$database, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection error: " . $e->getMessage()]);
    exit();
}
?>