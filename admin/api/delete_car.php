<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

// Only allow POST requests for safety
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Method not allowed. Use POST.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'msg' => 'Invalid car ID']);
    exit;
}

try {
    $conn->begin_transaction();

    // Delete associated documents first (to respect foreign key constraints)
    $stmt = $conn->prepare("DELETE FROM documents WHERE car_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete the car
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'msg' => 'Vehicle deleted successfully!']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Delete car error (ID: $id): " . $e->getMessage());
    echo json_encode(['success' => false, 'msg' => 'Failed to delete vehicle. Please try again.']);
}
?>