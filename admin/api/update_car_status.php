<?php
// admin/api/update_car_status.php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'Invalid JSON input']);
    exit;
}

// Extract and validate data
$carId = (int)($input['id'] ?? 0);
$newStatus = trim($input['status'] ?? '');

// Validate required fields
if (!$carId || !$newStatus) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'Missing required fields']);
    exit;
}

// Validate status value
$allowedStatuses = ['live', 'hidden', 'maintenance'];
if (!in_array($newStatus, $allowedStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'Invalid status value']);
    exit;
}

// Update the car status
$stmt = $conn->prepare("UPDATE cars SET status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $carId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true, 
            'msg' => 'Car status updated successfully',
            'car_id' => $carId,
            'new_status' => $newStatus
        ]);
    } else {
        // Car ID doesn't exist
        http_response_code(404);
        echo json_encode(['success' => false, 'msg' => 'Car not found']);
    }
}