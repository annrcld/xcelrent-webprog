<?php
// public/api/get_car_details.php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

$car_id = $_GET['id'] ?? null;

if (!$car_id) {
    echo json_encode(['error' => 'Car ID is required']);
    exit;
}

// Fetch car details with primary image
$stmt = $conn->prepare("
    SELECT c.*, 
           CONCAT(c.brand, ' ', c.model) AS name,
           c.seating AS seats,
           c.fuel_type AS fuel,
           c.tier4_daily AS price,
           (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
    FROM cars c
    WHERE c.id = ?
");

$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $car = $result->fetch_assoc();
    echo json_encode($car);
} else {
    echo json_encode(['error' => 'Car not found']);
}

$stmt->close();
$conn->close();
?>