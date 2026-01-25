<?php
// admin/api/approve_booking.php
ob_start();

ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../includes/config.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $bookingId = intval($_POST['booking_id'] ?? 0);

    if (!$bookingId) {
        throw new Exception('Booking ID is required');
    }

    // Get booking details
    $stmt = $conn->prepare("SELECT b.*, u.email, u.first_name, u.last_name FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Booking not found');
    }

    $booking = $result->fetch_assoc();
    $stmt->close();

    // Update booking status to confirmed
    $updateStmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
    $updateStmt->bind_param("i", $bookingId);
    
    if (!$updateStmt->execute()) {
        throw new Exception("Failed to update booking status");
    }
    
    $updateStmt->close();

    // Try to send email notification
    try {
        $subject = "Booking Confirmed - Xcelrent Car Rental";
        $message = "<html><body>
            <h2>Booking Confirmed!</h2>
            <p>Dear {$booking['first_name']},</p>
            <p>Your booking has been confirmed!</p>
            <p><strong>Booking Details:</strong></p>
            <p>Rental Period: " . date('M j, Y', strtotime($booking['start_date'])) . " to " . date('M j, Y', strtotime($booking['end_date'])) . "</p>
            <p>Total Amount: ₱" . number_format($booking['total_amount'], 2) . "</p>
            <p>We look forward to serving you!</p>
            <br>
            <p>Best regards,<br>The Xcelrent Team</p>
        </body></html>";
        
        $headers = "From: noreply@xcelrent.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        @mail($booking['email'], $subject, $message, $headers);
    } catch (Exception $e) {
        // Email failed, but that's okay
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Booking approved and confirmed successfully!'
    ]);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?>