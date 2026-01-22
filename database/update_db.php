<?php
// Script to update database schema and add password field to users table
require_once '../admin/includes/config.php';

// Check if password column exists in users table
$checkSql = "SHOW COLUMNS FROM users LIKE 'password'";
$result = $conn->query($checkSql);

if ($result->num_rows == 0) {
    // Add password column to users table
    $alterSql = "ALTER TABLE users ADD COLUMN password VARCHAR(255) AFTER phone";
    if ($conn->query($alterSql)) {
        echo "Password column added to users table successfully\n";
    } else {
        echo "Error adding password column: " . $conn->error . "\n";
    }
} else {
    echo "Password column already exists in users table\n";
}

$conn->close();
?>