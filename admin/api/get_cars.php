<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$location = $_GET['location'] ?? '';

// Added car_image and pricing tiers to the SELECT
$sql = "SELECT c.*, o.company_name as owner,
        (SELECT COUNT(*) FROM documents d WHERE d.car_id = c.id AND d.verified = 1) as verified_docs
        FROM cars c
        LEFT JOIN operators o ON c.operator_id = o.id
        WHERE 1=1";

if (!empty($category)) $sql .= " AND c.category = '" . $conn->real_escape_string($category) . "'";
if (!empty($status)) $sql .= " AND c.status = '" . $conn->real_escape_string($status) . "'";
if (!empty($location)) $sql .= " AND c.location = '" . $conn->real_escape_string($location) . "'";

$sql .= " ORDER BY c.created_at DESC";

$result = $conn->query($sql);
$cars = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Fetch document status for this specific car
        $docSql = "SELECT doc_type, verified FROM documents WHERE car_id = " . $row['id'];
        $docRes = $conn->query($docSql);
        $row['docs'] = $docRes->fetch_all(MYSQLI_ASSOC);
        $cars[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $cars]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
$conn->close();