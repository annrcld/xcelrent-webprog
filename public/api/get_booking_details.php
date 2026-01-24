<?php
// public/api/get_booking_details.php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$booking_id = $_GET['id'] ?? null;

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

try {
    // First, check if the extended columns exist in the bookings table
    $columns_check = $conn->query("SHOW COLUMNS FROM bookings LIKE 'renter_first_name'");
    $has_extended_columns = $columns_check->num_rows > 0;

    if ($has_extended_columns) {
        // Use the extended query with additional columns
        $stmt = $conn->prepare("
            SELECT
                b.id,
                b.car_id,
                b.user_id,
                b.start_date,
                b.end_date,
                b.total_amount,
                b.status,
                b.created_at,
                COALESCE(b.renter_first_name, u.first_name) as renter_first_name,
                COALESCE(b.renter_last_name, u.last_name) as renter_last_name,
                COALESCE(b.renter_email, u.email) as renter_email,
                COALESCE(b.renter_phone, u.phone) as renter_phone,
                COALESCE(b.pickup_location, '') as pickup_location,
                COALESCE(b.return_location, '') as return_location,
                COALESCE(b.special_requests, '') as special_requests,
                c.brand,
                c.model,
                c.image
            FROM bookings b
            LEFT JOIN cars c ON b.car_id = c.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.id = ?
        ");
    } else {
        // Use the basic query with only original columns
        $stmt = $conn->prepare("
            SELECT
                b.id,
                b.car_id,
                b.user_id,
                b.start_date,
                b.end_date,
                b.total_amount,
                b.status,
                b.created_at,
                u.first_name as renter_first_name,
                u.last_name as renter_last_name,
                u.email as renter_email,
                u.phone as renter_phone,
                '' as pickup_location,
                '' as return_location,
                '' as special_requests,
                c.brand,
                c.model,
                c.image
            FROM bookings b
            LEFT JOIN cars c ON b.car_id = c.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.id = ?
        ");
    }

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    $booking = $result->fetch_assoc();

    // Format the response
    $response = [
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
    ];

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in get_booking_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error retrieving booking: ' . $e->getMessage()]);
}

if (isset($stmt) && $stmt) {
    $stmt->close();
}
$conn->close();
?>