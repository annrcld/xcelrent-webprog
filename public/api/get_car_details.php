<?php
// public/api/get_car_details.php

header('Content-Type: application/json');

// Include the database configuration
require_once __DIR__ . '/../includes/config.php';

// Check if the database connection is established
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Check if car ID is provided and is a number
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid car ID provided.']);
    exit;
}

$carId = intval($_GET['id']);

// Prepare and execute the query to prevent SQL injection
$sql = "SELECT c.*, 
               CONCAT(c.brand, ' ', c.model) AS name, 
               c.seating AS seats, 
               c.fuel_type AS fuel, 
               c.tier4_daily AS price,
               'Automatic' as transmission, 
               (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
        FROM cars c WHERE c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $carId);
$stmt->execute();
$result = $stmt->get_result();

if ($car = $result->fetch_assoc()) {
    echo json_encode($car);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Car not found.']);
}

$stmt->close();
$conn->close();