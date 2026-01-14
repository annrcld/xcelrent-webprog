<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$verified = $_GET['verified'] ?? '';

$sql = "
    SELECT o.id, o.company_name, o.contact_name, o.email, o.phone, o.verified, o.created_at,
           COUNT(DISTINCT c.id) as total_cars,
           GROUP_CONCAT(DISTINCT d.doc_type SEPARATOR ', ') as documents
    FROM operators o
    LEFT JOIN cars c ON o.id = c.operator_id
    LEFT JOIN documents d ON d.user_id = o.id OR (o.id = 0 AND d.doc_type = 'operator_doc')
    WHERE 1=1
";

if ($verified === '0' || $verified === '1') {
    $verified = intval($verified);
    $sql .= " AND o.verified = $verified";
}

$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

$result = $conn->query($sql);
$operators = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $operators[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $operators]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>