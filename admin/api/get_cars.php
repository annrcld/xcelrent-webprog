<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$category = trim($_GET['category'] ?? '');
$status = trim($_GET['status'] ?? '');
$location = trim($_GET['location'] ?? '');
$driverType = trim($_GET['driver_type'] ?? '');

$allowedCategories = ['Sedan', 'SUV', 'Van'];
$allowedStatuses = ['live', 'hidden', 'maintenance'];
$allowedDriverTypes = ['self_drive', 'with_driver'];

if ($driverType && !in_array($driverType, $allowedDriverTypes)) {
    $driverType = '';
}

if ($category && !in_array($category, $allowedCategories)) {
    $category = '';
}
if ($status && !in_array($status, $allowedStatuses)) {
    $status = '';
}

$sql = "SELECT c.*, o.company_name as owner,
        (SELECT COUNT(*) FROM documents d WHERE d.car_id = c.id AND d.verified = 1) as verified_docs
        FROM cars c
        LEFT JOIN operators o ON c.operator_id = o.id
        WHERE 1=1";

$types = "";
$params = [];

if ($category !== '') {
    $sql .= " AND c.category = ?";
    $types .= "s";
    $params[] = $category;
}
if ($status !== '') {
    $sql .= " AND c.status = ?";
    $types .= "s";
    $params[] = $status;
}
if ($location !== '') {
    $sql .= " AND c.location = ?";
    $types .= "s";
    $params[] = $location;
}
if ($driverType !== '') {
    $sql .= " AND c.driver_type = ?";
    $types .= "s";
    $params[] = $driverType;
}

$sql .= " ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database query failed']);
    exit;
}

$result = $stmt->get_result();
$cars = [];

while ($row = $result->fetch_assoc()) {
    $docStmt = $conn->prepare("SELECT doc_type, verified FROM documents WHERE car_id = ?");
    $docStmt->bind_param("i", $row['id']);
    $docStmt->execute();
    $docResult = $docStmt->get_result();
    $row['docs'] = $docResult->fetch_all(MYSQLI_ASSOC);
    $cars[] = $row;
}

echo json_encode(['success' => true, 'data' => $cars]);
$conn->close();
?>