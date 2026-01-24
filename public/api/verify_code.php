<?php
// public/api/verify_code.php
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

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$type = $input['type'] ?? '';
$code = $input['code'] ?? '';
$target = $input['target'] ?? '';

// Validation
if (empty($type) || empty($code) || empty($target)) {
    echo json_encode(['success' => false, 'message' => 'Type, code, and target are required']);
    exit;
}

if (!in_array($type, ['email', 'phone'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid verification type']);
    exit;
}

// Check if verification code exists and is not expired
if (!isset($_SESSION['verification_code']) || !isset($_SESSION['verification_expiry'])) {
    echo json_encode(['success' => false, 'message' => 'No verification code found. Please request a new one.']);
    exit;
}

if (time() > $_SESSION['verification_expiry']) {
    // Clear the expired code
    unset($_SESSION['verification_code']);
    unset($_SESSION['verification_expiry']);
    unset($_SESSION['verification_type']);
    unset($_SESSION['verification_value']);
    
    echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please request a new one.']);
    exit;
}

// Check if the code matches
if ($_SESSION['verification_code'] !== $code) {
    echo json_encode(['success' => false, 'message' => 'Invalid verification code']);
    exit;
}

// Check if the type and target match
if ($_SESSION['verification_type'] !== $type || $_SESSION['verification_value'] !== $target) {
    echo json_encode(['success' => false, 'message' => 'Verification mismatch']);
    exit;
}

// Verification successful
// Store the verified value in session for use in profile update
$_SESSION['verified_' . $type] = $target;

// Clear the verification data
unset($_SESSION['verification_code']);
unset($_SESSION['verification_expiry']);
unset($_SESSION['verification_type']);
unset($_SESSION['verification_value']);

echo json_encode([
    'success' => true,
    'message' => 'Verification successful'
]);

?>