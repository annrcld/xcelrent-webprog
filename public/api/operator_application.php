<?php
// public/api/operator_application.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Turn off display_errors to prevent HTML output
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Initialize variables
$success = false;
$message = '';
$carId = 0;
$operatorId = 0;

// Start session to get user info
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // Use the public config which has MySQLi connection
    require_once '../includes/config.php';

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Method not allowed');
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        throw new Exception('User must be logged in to apply as operator');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        throw new Exception('Invalid JSON input');
    }

    // Extract vehicle details
    $vehicleName = trim($input['vehicleName'] ?? '');
    $plateNumber = trim($input['plateNumber'] ?? '');
    $category = trim($input['category'] ?? '');
    $seater = intval($input['seater'] ?? 4);
    $fuel = trim($input['fuel'] ?? '');
    $transmission = trim($input['transmission'] ?? '');
    $driverType = trim($input['driverType'] ?? '');
    $location = trim($input['location'] ?? 'Quezon City'); // Added location field

    // Validation
    if (empty($vehicleName) || empty($plateNumber) || empty($category) || empty($seater) || empty($fuel) || empty($transmission) || empty($driverType)) {
        http_response_code(400);
        throw new Exception('All vehicle details are required');
    }

    // Get user info for operator details
    $userId = $_SESSION['user_id'];
    $userEmail = $_SESSION['user_email'] ?? '';
    $userFirstName = $_SESSION['user_first_name'] ?? '';
    $userLastName = $_SESSION['user_last_name'] ?? '';
    $userPhone = $_SESSION['user_phone'] ?? '';

    // Check if user already has an operator record
    $checkOperatorSql = "SELECT id FROM operators WHERE contact_name = ? OR email = ?";
    $checkStmt = $conn->prepare($checkOperatorSql);
    $contactName = $userFirstName . ' ' . $userLastName;
    $checkStmt->bind_param("ss", $contactName, $userEmail);
    $checkStmt->execute();
    $existingOperator = $checkStmt->get_result();

    $operatorId = null;

    if ($existingOperator->num_rows > 0) {
        // Use existing operator record
        $existing = $existingOperator->fetch_assoc();
        $operatorId = $existing['id'];
    } else {
        // Create new operator record
        $insertOperatorSql = "INSERT INTO operators (company_name, contact_name, email, phone, verified) VALUES (?, ?, ?, ?, 0)";
        $operatorStmt = $conn->prepare($insertOperatorSql);
        $companyName = $userFirstName . "'s Vehicles"; // Default company name
        $operatorStmt->bind_param("ssss", $companyName, $contactName, $userEmail, $userPhone);
        $operatorStmt->execute();
        $operatorId = $conn->insert_id;
    }

    if (!$operatorId) {
        http_response_code(500);
        throw new Exception('Failed to create operator record');
    }

    // Check if transmission column exists in cars table
    $columnsResult = $conn->query("SHOW COLUMNS FROM cars LIKE 'transmission'");
    $transmissionColumnExists = $columnsResult->num_rows > 0;

    // Begin transaction
    $conn->begin_transaction();

    // Insert the car into the cars table with operator_id
    if ($transmissionColumnExists) {
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, fuel_type, transmission, seating, driver_type, location, operator_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        // Split vehicle name into brand and model
        $nameParts = explode(' ', $vehicleName, 2);
        $brand = $nameParts[0] ?? $vehicleName;
        $model = isset($nameParts[1]) ? $nameParts[1] : 'Model';
        $driverTypeValue = $driverType === 'Self-Drive' ? 'self_drive' : 'with_driver';
        $stmt->bind_param("sssssssssi", $brand, $model, $plateNumber, $category, $fuel, $transmission, $seater, $driverTypeValue, $location, $operatorId);
    } else {
        // Fallback if transmission column doesn't exist
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, fuel_type, seating, driver_type, location, operator_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        // Split vehicle name into brand and model
        $nameParts = explode(' ', $vehicleName, 2);
        $brand = $nameParts[0] ?? $vehicleName;
        $model = isset($nameParts[1]) ? $nameParts[1] : 'Model';
        $driverTypeValue = $driverType === 'Self-Drive' ? 'self_drive' : 'with_driver';
        $stmt->bind_param("sssssssssi", $brand, $model, $plateNumber, $category, $fuel, $seater, $driverTypeValue, $location, $operatorId);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert car: " . $stmt->error);
    }

    $carId = $conn->insert_id;

    // Commit transaction
    $conn->commit();

    $success = true;
    $message = 'Vehicle application submitted successfully';

} catch (Exception $e) {
    // Rollback transaction if connection exists
    if (isset($conn) && $conn) {
        $conn->rollback();
    }

    http_response_code(500);
    $success = false;
    $message = $e->getMessage();
}

// Close statement and connection if they exist
if (isset($stmt) && $stmt) {
    $stmt->close();
}
if (isset($conn) && $conn) {
    $conn->close();
}

// Ensure clean output
if (ob_get_level()) {
    ob_clean();
}

// Always return valid JSON
echo json_encode([
    'success' => $success,
    'message' => $message,
    'car_id' => $carId,
    'operator_id' => $operatorId
]);

// Flush output to ensure it's sent immediately
if (ob_get_level()) {
    ob_flush();
    flush();
}
?>