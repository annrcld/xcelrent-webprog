<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$search = $_GET['search'] ?? '';

$sql = "
    SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status, u.created_at,
           COUNT(DISTINCT b.id) as total_rentals,
           MAX(b.end_date) as last_rental_date
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    WHERE 1=1
";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR u.email LIKE '%$search%')";
}

$sql .= " GROUP BY u.id ORDER BY u.created_at DESC";

$result = $conn->query($sql);
$renters = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $renters[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $renters]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>