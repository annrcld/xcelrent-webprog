<?php
// public/api/submit_operator_application.php

header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. POST required.');
    }

    // Start session to get user info
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'User must be logged in to apply as operator']);
        exit;
    }

    // Helper function to handle file upload
    function uploadFile($fileInputName, $subFolder) {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$fileInputName];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('doc_') . '_' . time() . '.' . $ext;

        // Define paths
        // Relative path for Database (e.g., assets/uploads/documents/file.jpg)
        $relativePath = 'assets/uploads/' . $subFolder . '/' . $filename;

        // Absolute path for moving the file
        $targetPath = __DIR__ . '/../' . $relativePath;
        $targetDir = dirname($targetPath);

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $relativePath;
        }
        return null;
    }

    // Upload Files
    $or_cr_path = uploadFile('or_cr_file', 'documents');
    $deed_path = uploadFile('deed_of_sale_file', 'documents');
    $nbi_path = uploadFile('nbi_clearance_file', 'documents');
    $license_path = uploadFile('drivers_license_file', 'documents');

    // Car photos (assuming form has these names)
    $car_front = uploadFile('car_front_photo', 'cars');
    $car_side = uploadFile('car_side_photo', 'cars');
    $car_rear = uploadFile('car_rear_photo', 'cars');
    $car_interior = uploadFile('car_interior_photo', 'cars');

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
        throw new Exception('Failed to create operator record');
    }

    // Check if transmission column exists in cars table
    $columnsResult = $conn->query("SHOW COLUMNS FROM cars LIKE 'transmission'");
    $transmissionColumnExists = $columnsResult->num_rows > 0;

    // Begin transaction
    $conn->begin_transaction();

    // Insert the car into the cars table with operator_id and status 'pending'
    if ($transmissionColumnExists) {
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, fuel_type, transmission, seating, driver_type, location, operator_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        // Split vehicle name into brand and model
        $vehicleName = trim($_POST['brand'] ?? '') . ' ' . trim($_POST['model'] ?? '');
        $nameParts = explode(' ', $vehicleName, 2);
        $brand = $nameParts[0] ?? $vehicleName;
        $model = isset($nameParts[1]) ? $nameParts[1] : 'Model';
        $driverTypeValue = trim($_POST['driver_type'] ?? '') === 'Self-Drive' ? 'self_drive' : 'with_driver';
        $stmt->bind_param("sssssssis", $brand, $model, trim($_POST['plate_number'] ?? ''), trim($_POST['category'] ?? ''), trim($_POST['fuel_type'] ?? ''), trim($_POST['transmission'] ?? ''), intval($_POST['seating'] ?? 4), $driverTypeValue, trim($_POST['location'] ?? ''), $operatorId);
    } else {
        // Fallback if transmission column doesn't exist
        $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, fuel_type, seating, driver_type, location, operator_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        // Split vehicle name into brand and model
        $vehicleName = trim($_POST['brand'] ?? '') . ' ' . trim($_POST['model'] ?? '');
        $nameParts = explode(' ', $vehicleName, 2);
        $brand = $nameParts[0] ?? $vehicleName;
        $model = isset($nameParts[1]) ? $nameParts[1] : 'Model';
        $driverTypeValue = trim($_POST['driver_type'] ?? '') === 'Self-Drive' ? 'self_drive' : 'with_driver';
        $stmt->bind_param("ssssssis", $brand, $model, trim($_POST['plate_number'] ?? ''), trim($_POST['category'] ?? ''), trim($_POST['fuel_type'] ?? ''), intval($_POST['seating'] ?? 4), $driverTypeValue, trim($_POST['location'] ?? ''), $operatorId);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert car: " . $stmt->error);
    }

    $carId = $conn->insert_id;

    // Upload car photos to car_photos table
    $photos = [$car_front, $car_side, $car_rear, $car_interior];
    foreach ($photos as $index => $photoPath) {
        if ($photoPath) {
            $isPrimary = ($index === 0) ? 1 : 0; // Make first photo primary
            $photoStmt = $conn->prepare("INSERT INTO car_photos (car_id, file_path, is_primary) VALUES (?, ?, ?)");
            $photoStmt->bind_param("isi", $carId, $photoPath, $isPrimary);
            $photoStmt->execute();
            $photoStmt->close();
        }
    }

    // Upload documents to documents table
    $documents = [
        ['path' => $or_cr_path, 'type' => 'OR / CR'],
        ['path' => $deed_path, 'type' => 'Deed of Sale / Auth.'],
        ['path' => $nbi_path, 'type' => 'NBI Clearance'],
        ['path' => $license_path, 'type' => 'Driver\'s License']
    ];

    foreach ($documents as $doc) {
        if ($doc['path']) {
            $docStmt = $conn->prepare("INSERT INTO documents (car_id, doc_type, file_path, verified, uploaded_at) VALUES (?, ?, ?, 0, NOW())");
            $docStmt->bind_param("iss", $carId, $doc['type'], $doc['path']);
            $docStmt->execute();
            $docStmt->close();
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Application submitted successfully! It is now pending review.', 'car_id' => $carId]);

} catch (Throwable $e) {
    // Rollback transaction if connection exists
    if (isset($conn) && $conn) {
        $conn->rollback();
    }

    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}