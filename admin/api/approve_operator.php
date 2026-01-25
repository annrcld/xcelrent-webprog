<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$operatorId = intval($_POST['operator_id'] ?? 0);
$carId = intval($_POST['car_id'] ?? 0);

if (!$operatorId || !$carId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Both Operator ID and Car ID are required']);
    exit;
}

try {
    // Get operator details for email notification
    $stmt = $conn->prepare("SELECT company_name, contact_name, email FROM operators WHERE id = ?");
    $stmt->bind_param("i", $operatorId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Operator not found']);
        exit;
    }

    $operator = $result->fetch_assoc();
    $stmt->close();

    // Update the car status from pending to live
    $updateStmt = $conn->prepare("UPDATE cars SET status = 'live' WHERE id = ? AND operator_id = ?");
    $updateStmt->bind_param("ii", $carId, $operatorId);
    $updateResult = $updateStmt->execute();
    $updateStmt->close();

    if (!$updateResult) {
        throw new Exception("Failed to update car status");
    }

    if ($conn->affected_rows === 0) {
        throw new Exception("No car was updated - car may not exist or belong to this operator");
    }

    // Send approval email notification
    $subject = "Car Application Approved - Xcelrent Car Rental";
    $message = "
    <html>
    <body>
        <h2>Car Application Approved</h2>
        <p>Dear {$operator['contact_name']},</p>
        <p>Congratulations! Your car application has been approved.</p>
        <p>Your car is now live and visible to customers on our platform.</p>
        <p>Thank you for partnering with Xcelrent Car Rental.</p>
        <br>
        <p>Best regards,<br>The Xcelrent Team</p>
    </body>
    </html>
    ";

    $emailSent = send_notification($operator['email'], $subject, $message);

    $response = [
        'success' => true,
        'message' => 'Car application approved successfully! Car is now live and visible to customers.'
    ];

    if (!$emailSent) {
        $response['warning'] = 'Application approved but email notification failed to send';
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error approving car application: ' . $e->getMessage()]);
}

$conn->close();
?>