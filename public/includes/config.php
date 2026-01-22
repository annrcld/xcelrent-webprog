<?php
// public/includes/config.php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');  // Adjust as needed for your setup
define('DB_PASS', '');      // Adjust as needed for your setup
define('DB_NAME', 'xcelrent_car_rental');
define('DB_PORT', 3307);

// Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage() . " <br><strong>Hint:</strong> Ensure MySQL is started in XAMPP Control Panel on port " . DB_PORT . ".");
}
?>