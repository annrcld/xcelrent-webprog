<?php
require_once __DIR__ . '/includes/config.php';
if ($conn) {
    echo "Connected to MySQL OK. DB: " . $conn->real_escape_string($dbname);
} else {
    echo "Connection failed.";
}
?>