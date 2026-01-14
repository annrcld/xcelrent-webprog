<?php
// admin/includes/config.php

// 1. Start the session first thing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Database Configuration
$servername = "localhost"; // Usually just localhost when port is specified separately
$username = "root";
$password = "";
$dbname = "xcelrent_car_rental";
$port = 3307;

// 3. Create connection using specific port variable
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// 4. Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 5. Set charset
mysqli_set_charset($conn, "utf8");

// 6. Define base URLs
define('BASE_URL', '/project_xcelrent/admin/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('API_URL', BASE_URL . 'api/');
?>