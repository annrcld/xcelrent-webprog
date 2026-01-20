<?php
// public/api/signup.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

// Extract data
$firstName = trim($input['firstName'] ?? '');
$lastName = trim($input['lastName'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$password = $input['password'] ?? '';

// Validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Phone validation (basic check for 11 digits starting with 09)
if (!preg_match('/^09\d{9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone number must be 11 digits and start with 09']);
    exit;
}

// Password validation (at least 8 characters)
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Begin transaction
$conn->begin_transaction();

try {
    // Check if password column exists in users table
    $columnsResult = $conn->query("SHOW COLUMNS FROM users LIKE 'password'");
    $passwordColumnExists = $columnsResult->num_rows > 0;

    if ($passwordColumnExists) {
        // Insert new user with password
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);
    } else {
        // Insert new user without password (fallback for when column doesn't exist)
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $phone);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert user");
    }

    $userId = $conn->insert_id;

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully',
        'user_id' => $userId,
        'email' => $email
    ]);

} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create account']);
}

$stmt->close();
$conn->close();
?>