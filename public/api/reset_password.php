<?php
// public/api/reset_password.php
header('Content-Type: application/json');
require_once '../includes/config.php';

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

$token = $input['token'] ?? '';
$new_password = $input['new_password'] ?? '';

if (empty($token) || empty($new_password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token and new password are required']);
    exit;
}

if (strlen($new_password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long']);
    exit;
}

try {
    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_reset_tokens WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $token_data = $result->fetch_assoc();
    
    if (!$token_data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired reset token']);
        exit;
    }
    
    if (strtotime($token_data['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Reset token has expired']);
        exit;
    }
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update user's password
    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update_stmt->bind_param("si", $hashed_password, $token_data['user_id']);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update password");
    }
    
    // Delete the used token
    $delete_stmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
    $delete_stmt->bind_param("s", $token);
    $delete_stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Password has been reset successfully. You can now log in with your new password.'
    ]);
    
} catch (Exception $e) {
    error_log("Error in reset_password.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while resetting password']);
}

if (isset($stmt)) {
    $stmt->close();
}
if (isset($update_stmt)) {
    $update_stmt->close();
}
if (isset($delete_stmt)) {
    $delete_stmt->close();
}
$conn->close();
?>