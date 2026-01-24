<?php
// public/api/check_verification.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['verified' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['verified' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$type = $input['type'] ?? '';
$value = $input['value'] ?? '';

// Validation
if (empty($type) || empty($value)) {
    echo json_encode(['verified' => false, 'message' => 'Type and value are required']);
    exit;
}

if (!in_array($type, ['email', 'phone'])) {
    echo json_encode(['verified' => false, 'message' => 'Invalid verification type']);
    exit;
}

// Check if the value has been verified
$verified_key = 'verified_' . $type;

if (isset($_SESSION[$verified_key]) && $_SESSION[$verified_key] === $value) {
    echo json_encode([
        'verified' => true,
        'message' => 'Verification confirmed'
    ]);
} else {
    echo json_encode([
        'verified' => false,
        'message' => 'Value not verified'
    ]);
}

?>