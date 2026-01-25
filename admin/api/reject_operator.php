<?php
// admin/api/reject_operator.php
ob_start(); // Start output buffering to catch any errors

ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../includes/config.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $operatorId = intval($_POST['operator_id'] ?? 0);
    $carId = intval($_POST['car_id'] ?? 0);
    $rejectionReason = trim($_POST['reason'] ?? '');

    if (!$operatorId || !$carId) {
        throw new Exception('Both Operator ID and Car ID are required');
    }

    // Get operator details
    $stmt = $conn->prepare("SELECT company_name, contact_name, email FROM operators WHERE id = ?");
    $stmt->bind_param("i", $operatorId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Operator not found');
    }

    $operator = $result->fetch_assoc();
    $stmt->close();

    // Delete the specific car application
    $deleteStmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND operator_id = ?");
    $deleteStmt->bind_param("ii", $carId, $operatorId);
    $deleteResult = $deleteStmt->execute();
    $affectedRows = $conn->affected_rows;
    $deleteStmt->close();

    if (!$deleteResult) {
        throw new Exception("Failed to delete car application");
    }

    if ($affectedRows === 0) {
        throw new Exception("No car was deleted - car may not exist or doesn't belong to this operator");
    }

    // Try to send email (don't let it break the response)
    $emailSent = false;
    try {
        $subject = "Car Application Rejected - Xcelrent Car Rental";
        $reasonText = !empty($rejectionReason) ? htmlspecialchars($rejectionReason) : "Application did not meet our requirements.";
        
        $message = "<html><body>
            <h2>Car Application Rejected</h2>
            <p>Dear {$operator['contact_name']},</p>
            <p>We regret to inform you that your car application has been rejected.</p>
            <p><strong>Reason for rejection:</strong></p>
            <p>{$reasonText}</p>
            <p>If you believe this decision was made in error, please contact us to discuss your application further.</p>
            <p>Thank you for your interest in partnering with Xcelrent Car Rental.</p>
            <br>
            <p>Best regards,<br>The Xcelrent Team</p>
        </body></html>";
        
        $headers = "From: noreply@xcelrent.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $emailSent = @mail($operator['email'], $subject, $message, $headers);
    } catch (Exception $e) {
        // Email failed, but that's okay
    }

    $response = [
        'success' => true,
        'message' => 'Car application rejected successfully and removed'
    ];

    if (!$emailSent) {
        $response['warning'] = 'Application rejected but email notification failed to send';
    }

    ob_end_clean(); // Clear any buffered output
    echo json_encode($response);

} catch (Exception $e) {
    ob_end_clean(); // Clear any buffered output
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?>