<?php
// admin/api/get_bookings.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

$status = $_GET['status'] ?? '';

$sql = "
    SELECT b.id, b.start_date, b.end_date, b.total_amount, b.status, b.created_at,
           b.proof_of_payment,
           u.id as user_id, u.first_name, u.last_name, u.email, u.phone,
           c.id as car_id, c.brand, c.model, c.plate_number, c.location
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN cars c ON b.car_id = c.id
    WHERE 1=1
";

if (!empty($status)) {
    $status = $conn->real_escape_string($status);
    $sql .= " AND b.status = '$status'";
}

$sql .= " ORDER BY b.created_at DESC";

$result = $conn->query($sql);
$bookings = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $bookings]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>