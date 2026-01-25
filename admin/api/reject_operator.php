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
$rejectionReason = trim($_POST['reason'] ?? '');

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

    // Delete the specific car application
    $deleteStmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND operator_id = ?");
    $deleteStmt->bind_param("ii", $carId, $operatorId);
    $deleteResult = $deleteStmt->execute();
    $deleteStmt->close();

    if (!$deleteResult) {
        throw new Exception("Failed to delete car application");
    }

    // Send rejection email notification
    $subject = "Car Application Rejected - Xcelrent Car Rental";
    $message = "
    <html>
    <body>
        <h2>Car Application Rejected</h2>
        <p>Dear {$operator['contact_name']},</p>
        <p>We regret to inform you that your car application has been rejected.</p>
        <p><strong>Reason for rejection:</strong></p>
        <p>" . (!empty($rejectionReason) ? htmlspecialchars($rejectionReason) : "Application did not meet our requirements.") . "</p>
        <p>If you believe this decision was made in error, please contact us to discuss your application further.</p>
        <p>Thank you for your interest in partnering with Xcelrent Car Rental.</p>
        <br>
        <p>Best regards,<br>The Xcelrent Team</p>
    </body>
    </html>
    ";

    $emailSent = send_notification($operator['email'], $subject, $message);

    $response = [
        'success' => true,
        'message' => 'Car application rejected successfully and removed'
    ];

    if (!$emailSent) {
        $response['warning'] = 'Application rejected but email notification failed to send';
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error rejecting car application: ' . $e->getMessage()]);
}

$conn->close();
?>