<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Adjust as needed for your setup
define('DB_PASS', '');      // Adjust as needed for your setup
define('DB_NAME', 'xcelrent_car_rental');

// Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Don't die with HTML, just set a flag
    $db_error = $e->getMessage();
    $pdo = null; // Set pdo to null to indicate connection failure
}

// Site Configuration
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/project_xcelrent/public/');
define('SITE_NAME', 'Xcelrent Car Rental');

// Other configurations
define('DEFAULT_CURRENCY', 'PHP');
define('TIMEZONE', 'Asia/Manila');
date_default_timezone_set(TIMEZONE);
?>