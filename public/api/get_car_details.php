<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$carId = intval($_GET['id'] ?? 0);

if (!$carId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Car ID is required']);
    exit;
}

try {
    // Get car details with operator information
    $sql = "
        SELECT c.*, o.company_name as operator_company, o.contact_name as operator_contact, o.email as operator_email, o.phone as operator_phone,
               (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS car_image
        FROM cars c
        LEFT JOIN operators o ON c.operator_id = o.id
        WHERE c.id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Car not found']);
        exit;
    }

    $car = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'car' => $car
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>