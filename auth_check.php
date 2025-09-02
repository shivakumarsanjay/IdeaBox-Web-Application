<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Store the current URL for redirecting after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    header("Location: login.php");
    exit();
}
?>