<?php
require_once __DIR__ . '/admin/includes/config.php';
echo "Checking if transmission column exists...\n";

// Check if the transmission column exists
$checkColumnSql = "SHOW COLUMNS FROM cars LIKE 'transmission'";
$result = $conn->query($checkColumnSql);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $alterSql = "ALTER TABLE cars ADD COLUMN transmission VARCHAR(20) AFTER fuel_type";

    if ($conn->query($alterSql) === TRUE) {
        echo "Transmission column added successfully.\n";

        // Update existing records to have a default value
        $updateSql = "UPDATE cars SET transmission = 'Automatic' WHERE transmission IS NULL";
        if ($conn->query($updateSql) === TRUE) {
            echo "Existing records updated with default transmission value.\n";
        } else {
            echo "Error updating existing records: " . $conn->error . "\n";
        }
    } else {
        echo "Error adding transmission column: " . $conn->error . "\n";
    }
} else {
    echo "Transmission column already exists.\n";
}

$conn->close();
?> 
