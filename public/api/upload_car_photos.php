<?php
// public/api/upload_car_photos.php
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

// Check if car_id is provided
$carId = intval($_POST['car_id'] ?? 0);
if (!$carId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Car ID is required']);
    exit;
}

// Check if files are uploaded
if (!isset($_FILES['photos']) || !is_array($_FILES['photos']['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No photos uploaded']);
    exit;
}

$uploadedPhotos = [];
$errors = [];

// Create upload directory if it doesn't exist
$uploadDir = __DIR__ . '/../../admin/uploads/cars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Process each uploaded file
for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
    // Skip if no file was uploaded for this index
    if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_NO_FILE) {
        continue;
    }

    // Check for upload errors
    if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
        $errors[] = "File " . ($i + 1) . " upload error: " . $_FILES['photos']['error'][$i];
        continue;
    }

    // Validate file type
    $fileTmpPath = $_FILES['photos']['tmp_name'][$i];
    $fileName = $_FILES['photos']['name'][$i];
    $fileSize = $_FILES['photos']['size'][$i];
    $fileType = $_FILES['photos']['type'][$i];

    // Get file extension
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allowed file extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExtension, $allowedExtensions)) {
        $errors[] = "File " . ($i + 1) . " has invalid extension. Only JPG, JPEG, PNG, and GIF files are allowed.";
        continue;
    }

    // Limit file size (5MB)
    if ($fileSize > 5000000) {
        $errors[] = "File " . ($i + 1) . " exceeds 5MB size limit.";
        continue;
    }

    // Generate unique filename
    $newFileName = time() . '_' . uniqid() . '_' . $carId . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    // Move uploaded file to destination
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Determine if this is the primary photo (first one)
        $isPrimary = (count($uploadedPhotos) === 0) ? 1 : 0;

        // Save to database
        $stmt = $conn->prepare("INSERT INTO car_photos (car_id, file_path, is_primary) VALUES (?, ?, ?)");
        $filePath = 'uploads/cars/' . $newFileName;
        $stmt->bind_param("isi", $carId, $filePath, $isPrimary);

        if ($stmt->execute()) {
            $uploadedPhotos[] = [
                'id' => $conn->insert_id,
                'file_path' => $filePath,
                'is_primary' => $isPrimary
            ];
        } else {
            $errors[] = "Failed to save file " . ($i + 1) . " to database: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errors[] = "Failed to move file " . ($i + 1) . " to destination.";
    }
}

// Return response
if (count($uploadedPhotos) > 0) {
    // Update the car's main image to the primary photo
    $primaryPhoto = $uploadedPhotos[0];
    $updateStmt = $conn->prepare("UPDATE cars SET car_image = ? WHERE id = ?");
    $updateStmt->bind_param("si", $primaryPhoto['file_path'], $carId);
    $updateStmt->execute();
    $updateStmt->close();

    $response = [
        'success' => true,
        'message' => count($uploadedPhotos) . ' photo(s) uploaded successfully',
        'photos' => $uploadedPhotos
    ];

    if (!empty($errors)) {
        $response['errors'] = $errors;
    }

    echo json_encode($response);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No photos were uploaded successfully',
        'errors' => $errors
    ]);
}

$conn->close();
?> 
