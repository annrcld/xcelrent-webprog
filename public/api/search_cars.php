<?php
// public/api/search_cars.php

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

if (!isset($_GET['pickupDate']) || !isset($_GET['returnDate'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Pickup and return dates are required.']);
    exit;
}

$pickupDate = $_GET['pickupDate'];
$returnDate = $_GET['returnDate'];

// Basic validation for date format (YYYY-MM-DD HH:MM)
if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $pickupDate) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $returnDate)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format. Please select dates again.']);
    exit;
}

try {
    // Find cars that are NOT booked during the selected period.
    // A car is booked if its rental period overlaps with the requested period.
    // Overlap condition: (StartDateA < EndDateB) and (StartDateB < EndDateA)
    $sql = "
        SELECT 
            c.id,
            CONCAT(c.brand, ' ', c.model) AS name,
            c.seating AS seats,
            c.fuel_type AS fuel,
            c.tier4_daily AS price,
            (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
        FROM cars c
        WHERE 
            c.status = 'live'
            AND c.id NOT IN (
                SELECT b.car_id 
                FROM bookings b
                WHERE b.status NOT IN ('cancelled', 'completed', 'rejected') 
                AND (b.pickup_date < ? AND b.return_date > ?)
            )
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $returnDate, $pickupDate);
    $stmt->execute();
    
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'A server error occurred while searching for cars.']);
}