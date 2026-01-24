<?php
// public/api/get_booking_details.php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila'); // Set timezone to Philippines (UTC+8)
require_once __DIR__ . '/../includes/config.php';

$booking_id = $_GET['id'] ?? null;

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

try {
    // Now that you've added the 'image' column to the 'cars' table, this query will succeed.
    $sql = "SELECT 
                b.*, 
                c.brand, 
                c.model, 
                c.image 
            FROM bookings b
            LEFT JOIN cars c ON b.car_id = c.id
            WHERE b.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    // Format the response for your confirmation page
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $booking['id'],
            'reference' => 'XCR-' . date('Ymd', strtotime($booking['created_at'])) . str_pad($booking['id'], 4, '0', STR_PAD_LEFT),
            'customer_name' => $booking['renter_first_name'] . ' ' . $booking['renter_last_name'],
            'vehicle' => $booking['brand'] . ' ' . $booking['model'],
            'pickup_date' => date('M j, Y \a\t g:i A', strtotime($booking['start_date'])),
            'return_date' => date('M j, Y \a\t g:i A', strtotime($booking['end_date'])),
            'total_amount' => '₱' . number_format(floatval($booking['total_amount']), 2),
            'status' => $booking['status'],
            'image' => $booking['image'] ? '/project_xcelrent/public/' . $booking['image'] : '/project_xcelrent/public/assets/img/default_car.jpg'
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

if (isset($stmt)) $stmt->close();
$conn->close();
?>