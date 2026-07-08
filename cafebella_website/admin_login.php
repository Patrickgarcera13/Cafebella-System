<?php
// Handle login POST request
session_start();
require 'website_php/database.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Same checks as auth_check.php
        if ($user['is_banned'] == 1) {
            $error = "❌ This account has been banned.";
        } elseif ($user['is_approved'] == 0 || $user['status'] !== 'Approved') {
            $error = "⏳ Account pending admin approval.";
        } elseif ($user['role'] !== 'Admin') {
            // ONLY Admin can log in here — Staff are blocked
            $error = "❌ This login is for administrators only. Use the web app login for staff access.";
        } else {
            // All checks passed — set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect to your admin.php
            header("Location: admin.php");
            exit;
        }
    } else {
        $error = "❌ Incorrect email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cafe Bella - Admin Login</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f2f2f2;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }
  .login-card {
    background: white;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    width: 350px;
    text-align: center;
  }
  .login-card h2 {
    color: #114500;
    margin-bottom: 25px;
  }
  .input-group {
    margin-bottom: 20px;
    text-align: left;
  }
  .input-group label {
    display: block;
    font-size: 13px;
    margin-bottom: 5px;
    color: #666;
  }
  .input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    outline: none;
  }
  .input-group input:focus {
    border-color: #114500;
  }
  .btn-login {
    width: 100%;
    padding: 12px;
    background: #114500;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
  }
  .btn-login:hover {
    background: #0a2f00;
  }
  .error-msg {
    color: red;
    font-size: 13px;
    margin-top: 15px;
    min-height: 20px;
  }
</style>
</head>
<body>

<div class="login-card">
  <h2>Admin Login</h2>
  <form method="POST" action="admin_login.php">
    <div class="input-group">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" required autocomplete="off" placeholder="admin@cafebella.com">
    </div>
    <div class="input-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn-login">Login</button>
    <div class="error-msg"><?= $error ?></div>
  </form>
</div>

</body>
</html>
