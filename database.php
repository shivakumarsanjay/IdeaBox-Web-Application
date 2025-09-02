<?php
// Database configuration
$host = 'localhost';
$dbname = 'ideabox';
$username = 'root';
$password = '';

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging, 0 for production

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch(PDOException $e) {
    // Log error instead of displaying to user
    error_log("Database connection failed: " . $e->getMessage());
    
    // Display user-friendly error message
    die("We're experiencing technical difficulties. Please try again later.");
}
?>