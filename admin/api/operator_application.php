<?php
// public/api/operator_application.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to apply as an operator']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Extract vehicle details
$vehicleName = trim($input['vehicleName'] ?? '');
$plateNumber = trim($input['plateNumber'] ?? '');
$category = trim($input['category'] ?? '');
$seater = intval($input['seater'] ?? 0);
$fuel = trim($input['fuel'] ?? '');
$transmission = trim($input['transmission'] ?? '');
$driverType = trim($input['driverType'] ?? '');
$location = trim($input['location'] ?? '');

// Validate required fields
if (!$vehicleName || !$plateNumber || !$category || !$seater || !$fuel || !$transmission || !$driverType || !$location) {
    echo json_encode(['success' => false, 'message' => 'All vehicle details are required']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Get user details from users table (including phone)
    $userStmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
    $userStmt->bind_param("i", $user_id);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows === 0) {
        throw new Exception("User not found");
    }
    
    $user = $userResult->fetch_assoc();
    $userStmt->close();
    
    // Check if operator already exists for this user
    $checkStmt = $conn->prepare("SELECT id FROM operators WHERE email = ?");
    $checkStmt->bind_param("s", $user['email']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Operator exists, get their ID
        $operator = $checkResult->fetch_assoc();
        $operator_id = $operator['id'];
        $checkStmt->close();
    } else {
        // Create new operator entry with user's info including phone
        $company_name = $user['first_name'] . ' ' . $user['last_name'];
        $contact_name = $user['first_name'] . ' ' . $user['last_name'];
        
        $insertOpStmt = $conn->prepare("INSERT INTO operators (company_name, contact_name, email, phone, verified, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
        $insertOpStmt->bind_param("ssss", $company_name, $contact_name, $user['email'], $user['phone']);
        
        if (!$insertOpStmt->execute()) {
            throw new Exception("Failed to create operator: " . $insertOpStmt->error);
        }
        
        $operator_id = $conn->insert_id;
        $insertOpStmt->close();
    }
    
    // Parse vehicle name into brand and model
    $nameParts = explode(' ', $vehicleName, 2);
    $brand = $nameParts[0];
    $model = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // Insert car with status 'pending' for admin approval
    $insertCarStmt = $conn->prepare("
        INSERT INTO cars (operator_id, brand, model, plate_number, category, seating, fuel_type, transmission, driver_type, location, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $insertCarStmt->bind_param(
        "isssssisss",
        $operator_id,
        $brand,
        $model,
        $plateNumber,
        $category,
        $seater,
        $fuel,
        $transmission,
        $driverType,
        $location
    );
    
    if (!$insertCarStmt->execute()) {
        throw new Exception("Failed to create car: " . $insertCarStmt->error);
    }
    
    $car_id = $conn->insert_id;
    $insertCarStmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Car application submitted successfully! Pending admin approval.',
        'car_id' => $car_id,
        'operator_id' => $operator_id
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Operator application error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>