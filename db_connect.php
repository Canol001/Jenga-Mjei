<?php
// db_connect.php

// Database configuration
define('DB_HOST', 'localhost'); // Replace with your database host
define('DB_USER', 'root'); // Replace with your database username
define('DB_PASS', ''); // Replace with your database password
define('DB_NAME', 'inventory_db'); // Replace with your database name

// Error reporting (enable for development, disable for production)
ini_set('display_errors', '1'); // Set to '0' in production
ini_set('display_startup_errors', '1'); // Set to '0' in production
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Set character set to UTF-8
$conn->set_charset('utf8mb4');

// Optional: Set timezone for consistency
date_default_timezone_set('UTC');

// Function to check if the user is an admin
function checkAdmin($conn) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $userId = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT title FROM members WHERE id = ?");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    $isAdmin = $user && $user['title'] === 'Admin';
    $_SESSION['is_admin'] = $isAdmin; // Update session variable
    return $isAdmin;
}

// Check admin privileges for API access
if (basename($_SERVER['PHP_SELF']) !== 'login.php' && !checkAdmin($conn)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Access denied: Administrator privileges required'
    ]);
    exit;
}
?>