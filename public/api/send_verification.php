<?php
// public/api/send_verification.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$type = $input['type'] ?? '';
$value = $input['value'] ?? '';

// Validation
if (empty($type) || empty($value)) {
    echo json_encode(['success' => false, 'message' => 'Type and value are required']);
    exit;
}

if (!in_array($type, ['email', 'phone'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid verification type']);
    exit;
}

// Generate a 6-digit verification code
$verification_code = sprintf("%06d", mt_rand(1, 999999));

// Store the verification code in session with expiry (5 minutes)
$_SESSION['verification_code'] = $verification_code;
$_SESSION['verification_expiry'] = time() + (5 * 60); // 5 minutes
$_SESSION['verification_type'] = $type;
$_SESSION['verification_value'] = $value;

try {
    if ($type === 'email') {
        // In a real application, you would send an email with the verification code
        // For now, we'll just simulate it and log the code for testing purposes
        
        // For demo purposes, we'll just log the code (in production, send via email)
        error_log("Verification code for email {$value}: {$verification_code}");
        
        // Simulate sending email
        $subject = "Email Verification Code";
        $message = "Your verification code is: {$verification_code}\n\nThis code will expire in 5 minutes.";
        $headers = "From: noreply@xcelrent.com";
        
        // Note: In a real application, you would use a proper email service
        // mail($value, $subject, $message, $headers);
        
        echo json_encode([
            'success' => true,
            'message' => 'Verification code sent to your email'
        ]);
    } else if ($type === 'phone') {
        // In a real application, you would send an SMS with the verification code
        // For now, we'll just simulate it and log the code for testing purposes
        
        error_log("Verification code for phone {$value}: {$verification_code}");
        
        // Note: In a real application, you would use an SMS service like Twilio
        echo json_encode([
            'success' => true,
            'message' => 'Verification code sent to your phone'
        ]);
    }
} catch (Exception $e) {
    error_log("Error sending verification: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to send verification code']);
}

$conn->close();
?>