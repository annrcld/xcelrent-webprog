
<?php
// admin/api/approve_operator.php
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

    // Try to send email (don't let it break the response)
    $emailSent = false;
    try {
        $subject = "Car Application Approved - Xcelrent Car Rental";
        $message = "<html><body>
            <h2>Car Application Approved</h2>
            <p>Dear {$operator['contact_name']},</p>
            <p>Congratulations! Your car application has been approved.</p>
            <p>Your car is now live and visible to customers on our platform.</p>
            <p>Thank you for partnering with Xcelrent Car Rental.</p>
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
        'message' => 'Car application approved successfully! Car is now live and visible to customers.'
    ];

    if (!$emailSent) {
        $response['warning'] = 'Application approved but email notification failed to send';
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