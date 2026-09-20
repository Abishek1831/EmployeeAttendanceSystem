<?php
// Disable error display (important for JSON responses)
error_reporting(0);
ini_set('display_errors', 0);

// Database Configuration
// NOTE: These are the default local XAMPP/MySQL credentials (root, no password).
// They are safe for local development only. Before deploying this project anywhere
// public, replace these with real credentials and load them from environment
// variables or a config file that is excluded via .gitignore (see SECURITY.md).
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'employee_attendance');

// Create connection
function getDBConnection() {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        return null;
    }
    
    return $conn;
}

// Start session only if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>