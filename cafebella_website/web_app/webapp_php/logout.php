<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: /cafebella_website/web_app/Login.html"); // Adjust path as needed
exit();
?>