<?php
// public/includes/config.php

// Database Configuration
define('DB_HOST', 'localhost:3307');
define('DB_USER', 'root');  // Adjust as needed for your setup
define('DB_PASS', '');      // Adjust as needed for your setup
define('DB_NAME', 'xcelrent_car_rental');

// Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage() . " <br><strong>Hint:</strong> Ensure MySQL is started in XAMPP Control Panel.");
}
?>