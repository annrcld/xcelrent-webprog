<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'Invalid car ID']);
    exit;
}

// Fetch single car
$stmt = $conn->prepare("
    SELECT c.*, o.company_name as owner
    FROM cars c
    LEFT JOIN operators o ON c.operator_id = o.id
    WHERE c.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'msg' => 'Car not found']);
    exit;
}

$car = $result->fetch_assoc();

// Get verified doc count
$docStmt = $conn->prepare("SELECT COUNT(*) as verified_docs FROM documents WHERE car_id = ? AND verified = 1");
$docStmt->bind_param("i", $id);
$docStmt->execute();
$docRow = $docStmt->get_result()->fetch_assoc();
$car['verified_docs'] = (int)($docRow['verified_docs'] ?? 0);

// Return single car object (not array)
echo json_encode(['success' => true, 'data' => $car]);
$conn->close();
?>