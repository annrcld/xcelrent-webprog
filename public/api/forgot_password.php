<?php
// public/api/forgot_password.php
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

$email = trim($input['email'] ?? '');

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if user exists
    $stmt = $conn->prepare("SELECT id, first_name, email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        // Don't reveal if email exists or not for security
        echo json_encode([
            'success' => true,
            'message' => 'If your email exists in our system, you will receive a password reset link shortly.'
        ]);
        exit;
    }
    
    // Generate a password reset token
    $token = bin2hex(random_bytes(32)); // 64 character hex string
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
    
    // Check if password_reset_tokens table exists, if not create it
    $table_check = $conn->query("SHOW TABLES LIKE 'password_reset_tokens'");
    if ($table_check->num_rows == 0) {
        // Create the password_reset_tokens table
        $create_table_sql = "
            CREATE TABLE password_reset_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_token (token),
                INDEX idx_expires (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        if (!$conn->query($create_table_sql)) {
            throw new Exception("Failed to create password_reset_tokens table: " . $conn->error);
        }
    }
    
    // Store the token in the database
    $insert_token_stmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
    $insert_token_stmt->bind_param("iss", $user['id'], $token, $expires_at);
    
    if (!$insert_token_stmt->execute()) {
        throw new Exception("Failed to store reset token");
    }
    
    // In a real application, send email with reset link
    // For now, we'll just log the token for demonstration
    $reset_link = "http://localhost/project_xcelrent/public/reset_password.php?token=" . $token;
    error_log("Password reset link for {$email}: {$reset_link}");
    
    // Simulate sending email
    $subject = "Password Reset Request";
    $message = "Hi {$user['first_name']},\n\nYou have requested to reset your password. Click the link below to reset your password:\n\n{$reset_link}\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
    $headers = "From: noreply@xcelrent.com";
    
    // Note: In a real application, you would use a proper email service
    // mail($email, $subject, $message, $headers);
    
    echo json_encode([
        'success' => true,
        'message' => 'If your email exists in our system, you will receive a password reset link shortly.'
    ]);
    
} catch (Exception $e) {
    error_log("Error in forgot_password.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request']);
}

if (isset($stmt)) {
    $stmt->close();
}
if (isset($insert_token_stmt)) {
    $insert_token_stmt->close();
}
$conn->close();
?>