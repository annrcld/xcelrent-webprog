<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Extract booking details
$carId = intval($input['car_id'] ?? 0);
$firstName = trim($input['first_name'] ?? '');
$lastName = trim($input['last_name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$pickupLocation = trim($input['pickup_location'] ?? '');
$returnLocation = trim($input['return_location'] ?? '');
$specialRequests = trim($input['special_requests'] ?? '');
$paymentMethod = trim($input['payment_method'] ?? '');
$proofOfPayment = $input['proof_of_payment'] ?? null;
$pickupDate = $input['pickup_date'] ?? '';
$returnDate = $input['return_date'] ?? '';

// Validation
if (!$carId || !$firstName || !$lastName || !$email || !$phone || !$pickupLocation || !$returnLocation || !$paymentMethod || !$proofOfPayment || !$pickupDate || !$returnDate) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate phone number format (Philippines format)
if (!preg_match('/^(09|\+639)\d{9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
    exit;
}

try {
    // Check if the car is available for the requested dates
    $availabilityCheck = $conn->prepare("
        SELECT id FROM bookings 
        WHERE car_id = ? 
        AND status IN ('pending', 'confirmed', 'ongoing') 
        AND (
            (STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s') <= end_date AND STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s') >= start_date)
        )
    ");
    $availabilityCheck->bind_param("isss", $carId, $pickupDate, $returnDate, $pickupDate);
    $availabilityCheck->execute();
    $availabilityResult = $availabilityCheck->get_result();
    
    if ($availabilityResult->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Selected car is not available for the requested dates']);
        exit;
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    // Insert booking record with 'pending' status
    $bookingStmt = $conn->prepare("
        INSERT INTO bookings (car_id, user_id, start_date, end_date, total_amount, status, created_at) 
        VALUES (?, ?, ?, ?, 0, 'pending', NOW())
    ");
    
    // For now, we'll use a placeholder user_id since we don't have the user logged in
    // In a real implementation, this would be $_SESSION['user_id']
    $userId = 0; // Placeholder - should be replaced with actual user ID
    
    $bookingStmt->bind_param("iiss", $carId, $userId, $pickupDate, $returnDate);
    $bookingResult = $bookingStmt->execute();
    
    if (!$bookingResult) {
        throw new Exception("Failed to create booking: " . $bookingStmt->error);
    }
    
    $bookingId = $conn->insert_id;
    $bookingStmt->close();
    
    // Save proof of payment
    $proofPath = saveProofOfPayment($proofOfPayment, $bookingId);
    if (!$proofPath) {
        throw new Exception("Failed to save proof of payment");
    }
    
    // Update booking with proof of payment path
    $updateProofStmt = $conn->prepare("UPDATE bookings SET proof_path = ? WHERE id = ?");
    $updateProofStmt->bind_param("si", $proofPath, $bookingId);
    $updateProofResult = $updateProofStmt->execute();
    $updateProofStmt->close();
    
    if (!$updateProofResult) {
        throw new Exception("Failed to update booking with proof of payment");
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking submitted successfully and is pending approval',
        'booking_id' => $bookingId
    ]);
    
} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error creating booking: ' . $e->getMessage()]);
}

$conn->close();

function saveProofOfPayment($proofData, $bookingId) {
    if (!isset($proofData['data']) || !isset($proofData['name'])) {
        return false;
    }
    
    // Decode base64 data
    $data = $proofData['data'];
    if (strpos($data, 'data:image') === 0 || strpos($data, 'data:application') === 0) {
        // Remove data URL prefix
        $parts = explode(',', $data, 2);
        $data = base64_decode($parts[1]);
    }
    
    if (!$data) {
        return false;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../assets/uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $ext = pathinfo($proofData['name'], PATHINFO_EXTENSION);
    $filename = 'proof_' . $bookingId . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    // Save file
    if (file_put_contents($filepath, $data)) {
        return 'assets/uploads/payments/' . $filename;
    }
    
    return false;
}
?>