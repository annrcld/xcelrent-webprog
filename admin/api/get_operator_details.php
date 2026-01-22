<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$operatorId = intval($_GET['operator_id'] ?? 0);
$carId = intval($_GET['car_id'] ?? 0);

if (!$operatorId || !$carId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Both Operator ID and Car ID are required']);
    exit;
}

// Get operator details
$operatorSql = "
    SELECT o.*
    FROM operators o
    WHERE o.id = ?
";

$operatorStmt = $conn->prepare($operatorSql);
$operatorStmt->bind_param("i", $operatorId);
$operatorStmt->execute();
$operatorResult = $operatorStmt->get_result();

if ($operatorResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Operator not found']);
    exit;
}

$operator = $operatorResult->fetch_assoc();

// Get specific car details
$carSql = "
    SELECT c.*,
           COUNT(cp.id) as total_photos
    FROM cars c
    LEFT JOIN car_photos cp ON c.id = cp.car_id
    WHERE c.id = ? AND c.operator_id = ?
    GROUP BY c.id
";

$carStmt = $conn->prepare($carSql);
$carStmt->bind_param("ii", $carId, $operatorId);
$carStmt->execute();
$carResult = $carStmt->get_result();

if ($carResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Car not found']);
    exit;
}

$car = $carResult->fetch_assoc();

// Get all photos for this car
$photosSql = "SELECT id, file_path, is_primary FROM car_photos WHERE car_id = ? ORDER BY is_primary DESC, id ASC";
$photosStmt = $conn->prepare($photosSql);
$photosStmt->bind_param("i", $carId);
$photosStmt->execute();
$photosResult = $photosStmt->get_result();

$photos = [];
while ($photo = $photosResult->fetch_assoc()) {
    $photos[] = $photo;
}

$car['photos'] = $photos;

// Get documents for this operator
$docsSql = "SELECT * FROM documents WHERE user_id = ? ORDER BY uploaded_at DESC";
$docsStmt = $conn->prepare($docsSql);
$docsStmt->bind_param("i", $operatorId);
$docsStmt->execute();
$docsResult = $docsStmt->get_result();

$documents = [];
while ($doc = $docsResult->fetch_assoc()) {
    $documents[] = $doc;
}

$response = [
    'success' => true,
    'operator' => $operator,
    'car' => $car,
    'documents' => $documents
];

echo json_encode($response);

$conn->close();
?>