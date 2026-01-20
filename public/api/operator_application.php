<?php
// public/api/operator_application.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Turn off display_errors to prevent HTML output
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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

// Extract vehicle details
$vehicleName = trim($input['vehicleName'] ?? '');
$plateNumber = trim($input['plateNumber'] ?? '');
$category = trim($input['category'] ?? '');
$seater = intval($input['seater'] ?? 4);
$fuel = trim($input['fuel'] ?? '');
$transmission = trim($input['transmission'] ?? '');
$driverType = trim($input['driverType'] ?? '');

// Validation
if (empty($vehicleName) || empty($plateNumber) || empty($category) || empty($seater) || empty($fuel) || empty($transmission) || empty($driverType)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All vehicle details are required']);
    exit;
}

// Check if transmission column exists in cars table
$columnsResult = $conn->query("SHOW COLUMNS FROM cars LIKE 'transmission'");
$transmissionColumnExists = $columnsResult->num_rows > 0;

try {
    // Begin transaction
    $conn->begin_transaction();

    // Insert the car into the cars table
    if ($transmissionColumnExists) {
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, fuel_type, transmission, seating, driver_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'maintenance')");
        // Split vehicle name into brand and model
        $nameParts = explode(' ', $vehicleName, 2);
        $brand = $nameParts[0] ?? $vehicleName;
        $model = isset($nameParts[1]) ? $nameParts[1] : 'Model';
        $driverTypeValue = $driverType === 'Self-Drive' ? 'self_drive' : 'with_driver';
        $stmt->bind_param("ssssssis", $brand, $model, $plateNumber, $category, $fuel, $transmission, $seater, $driverTypeValue);
    } else {
        // Fallback if transmission column doesn't exist
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, fuel_type, seating, driver_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'maintenance')");
        // Split vehicle name into brand and model
        $nameParts = explode(' ', $vehicleName, 2);
        $brand = $nameParts[0] ?? $vehicleName;
        $model = isset($nameParts[1]) ? $nameParts[1] : 'Model';
        $driverTypeValue = $driverType === 'Self-Drive' ? 'self_drive' : 'with_driver';
        $stmt->bind_param("ssssssi", $brand, $model, $plateNumber, $category, $fuel, $seater, $driverTypeValue);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert car: " . $stmt->error);
    }

    $carId = $conn->insert_id;

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Vehicle application submitted successfully',
        'car_id' => $carId
    ]);

} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit application: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>