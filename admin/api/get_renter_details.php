<?php
// admin/api/get_renter_details.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

try {
    $userId = intval($_GET['user_id'] ?? 0);

    if (!$userId) {
        throw new Exception('User ID is required');
    }

    // Get user details
    $userStmt = $conn->prepare("SELECT id, first_name, last_name, email, phone, created_at FROM users WHERE id = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows === 0) {
        throw new Exception('User not found');
    }

    $user = $userResult->fetch_assoc();
    $userStmt->close();

    // Get user's booking history
    $bookingsStmt = $conn->prepare("
        SELECT b.id, b.start_date, b.end_date, b.status, b.total_amount,
               c.brand, c.model
        FROM bookings b
        JOIN cars c ON b.car_id = c.id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
        LIMIT 10
    ");
    $bookingsStmt->bind_param("i", $userId);
    $bookingsStmt->execute();
    $bookingsResult = $bookingsStmt->get_result();

    $bookings = [];
    while ($row = $bookingsResult->fetch_assoc()) {
        $bookings[] = $row;
    }
    $bookingsStmt->close();

    echo json_encode([
        'success' => true,
        'user' => $user,
        'bookings' => $bookings
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>