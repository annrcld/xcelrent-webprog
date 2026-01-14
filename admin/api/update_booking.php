<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Method not allowed']);
    exit;
}

$booking_id = intval($_POST['booking_id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowed_statuses = ['pending', 'confirmed', 'ongoing', 'completed', 'cancelled'];

if ($booking_id <= 0 || !in_array($status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'msg' => 'Invalid booking_id or status']);
    exit;
}

$stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $booking_id);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success' => true, 'msg' => 'Booking updated']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'msg' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>