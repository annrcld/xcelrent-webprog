<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Method not allowed']);
    exit;
}

$car_id = intval($_POST['car_id'] ?? 0);
if (!$car_id) {
    echo json_encode(['success' => false, 'msg' => 'Missing car ID']);
    exit;
}

// Collect data
$brand = trim($_POST['brand'] ?? '');
$model = trim($_POST['model'] ?? '');
$category = trim($_POST['category'] ?? '');
$fuel_type = trim($_POST['fuel_type'] ?? '');
$transmission = trim($_POST['transmission'] ?? '');
$driver_type = trim($_POST['driver_type'] ?? '');
$seating = intval($_POST['seating'] ?? 0);
$plate = strtoupper(trim($_POST['plate_number'] ?? '')); // Auto-uppercase plate number
$location = trim($_POST['location'] ?? '');

// Pricing
$tier1_12hrs = floatval($_POST['tier1_12hrs'] ?? 0);
$tier1_24hrs = floatval($_POST['tier1_24hrs'] ?? 0);
$tier2_12hrs = floatval($_POST['tier2_12hrs'] ?? 0);
$tier2_24hrs = floatval($_POST['tier2_24hrs'] ?? 0);
$tier3_24hrs = floatval($_POST['tier3_24hrs'] ?? 0);
$tier4_daily = floatval($_POST['tier4_daily'] ?? 0);

// Validate
if (empty($brand) || empty($model) || empty($category) || empty($fuel_type) || empty($driver_type) ||
    empty($plate) || empty($location) || $seating <= 0) {
    echo json_encode(['success' => false, 'msg' => 'Please fill all required fields.']);
    exit;
}

if ($tier1_12hrs <= 0 || $tier1_24hrs <= 0 ||
    $tier2_12hrs <= 0 || $tier2_24hrs <= 0 ||
    $tier3_24hrs <= 0 || $tier4_daily <= 0) {
    echo json_encode(['success' => false, 'msg' => 'All prices must be greater than zero.']);
    exit;
}

try {
    $sql = "UPDATE cars SET
        brand = ?, model = ?, plate_number = ?, category = ?, fuel_type = ?, transmission = ?, driver_type = ?, seating = ?, location = ?,
        tier1_12hrs = ?, tier1_24hrs = ?, tier2_12hrs = ?, tier2_24hrs = ?, tier3_24hrs = ?, tier4_daily = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssisddddddi",
        $brand, $model, $plate, $category, $fuel_type, $transmission, $driver_type, $seating, $location,
        $tier1_12hrs, $tier1_24hrs, $tier2_12hrs, $tier2_24hrs, $tier3_24hrs, $tier4_daily,
        $car_id
    );

    $stmt->execute();

    // Handle car image upload if provided
    if (!empty($_FILES['car_image']['name'])) {
        $imageFile = $_FILES['car_image'];

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($imageFile['type'], $allowedTypes)) {
            throw new Exception("Invalid image type. Only JPG, PNG, and GIF files are allowed.");
        }

        // Validate file size (5MB max)
        if ($imageFile['size'] > 5 * 1024 * 1024) {
            throw new Exception("Image file size exceeds 5MB limit.");
        }

        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/cars/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception("Failed to create upload directory.");
            }
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $newFileName = 'car_' . $car_id . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;
        $relativePath = 'uploads/cars/' . $newFileName;

        // Move uploaded file
        if (!move_uploaded_file($imageFile['tmp_name'], $destPath)) {
            throw new Exception("Failed to upload image file.");
        }

        // Update the car's image in the database
        $updateImageStmt = $conn->prepare("UPDATE cars SET image = ? WHERE id = ?");
        $updateImageStmt->bind_param("si", $relativePath, $car_id);
        $updateImageStmt->execute();
        $updateImageStmt->close();

        // Update the car_photos table to set this as the primary image
        // First, set all photos for this car as non-primary
        $resetPrimaryStmt = $conn->prepare("UPDATE car_photos SET is_primary = 0 WHERE car_id = ?");
        $resetPrimaryStmt->bind_param("i", $car_id);
        $resetPrimaryStmt->execute();
        $resetPrimaryStmt->close();

        // Check if a photo record already exists for this car with this image path
        $checkPhotoStmt = $conn->prepare("SELECT id FROM car_photos WHERE car_id = ? AND file_path = ?");
        $checkPhotoStmt->bind_param("is", $car_id, $relativePath);
        $checkPhotoStmt->execute();
        $checkResult = $checkPhotoStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Update existing photo record to be primary
            $updatePhotoStmt = $conn->prepare("UPDATE car_photos SET is_primary = 1 WHERE car_id = ? AND file_path = ?");
            $updatePhotoStmt->bind_param("is", $car_id, $relativePath);
            $updatePhotoStmt->execute();
            $updatePhotoStmt->close();
        } else {
            // Insert new photo record as primary
            $insertPhotoStmt = $conn->prepare("INSERT INTO car_photos (car_id, file_path, is_primary) VALUES (?, ?, 1)");
            $insertPhotoStmt->bind_param("is", $car_id, $relativePath);
            $insertPhotoStmt->execute();
            $insertPhotoStmt->close();
        }
        $checkPhotoStmt->close();
    } else {
        // If no new image uploaded, ensure the existing image is properly set as primary
        // Get the current primary image for this car
        $getImageStmt = $conn->prepare("SELECT image FROM cars WHERE id = ?");
        $getImageStmt->bind_param("i", $car_id);
        $getImageStmt->execute();
        $imageResult = $getImageStmt->get_result();
        $carData = $imageResult->fetch_assoc();
        $currentImage = $carData['image'] ?? null;

        if ($currentImage) {
            // Set all photos for this car as non-primary
            $resetPrimaryStmt = $conn->prepare("UPDATE car_photos SET is_primary = 0 WHERE car_id = ?");
            $resetPrimaryStmt->bind_param("i", $car_id);
            $resetPrimaryStmt->execute();
            $resetPrimaryStmt->close();

            // Find the photo record that matches the current image and set it as primary
            $setPrimaryStmt = $conn->prepare("UPDATE car_photos SET is_primary = 1 WHERE car_id = ? AND file_path = ?");
            $setPrimaryStmt->bind_param("is", $car_id, $currentImage);
            $setPrimaryStmt->execute();
            $setPrimaryStmt->close();
        }
    }

    // Handle document uploads (same as add_car.php)
    $docFiles = [
        'or_file'       => 'Official Receipt (OR)',
        'cr_file'       => 'Certificate of Registration (CR)',
        'nbi_clearance' => 'NBI Clearance',
        'deed_of_sale'  => 'Deed of Sale',
        'pro_license'   => 'Professional License'
    ];

    $uploadDir = __DIR__ . '/../uploads/cars/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    foreach ($docFiles as $inputName => $docLabel) {
        if (!empty($_FILES[$inputName]['name'])) {
            $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                throw new Exception("Invalid file type for {$docLabel}.");
            }
            $newName = time() . "_{$inputName}_{$car_id}.{$ext}";
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath)) {
                $path = 'uploads/cars/' . $newName;
                // First, delete old doc if exists
                $delStmt = $conn->prepare("DELETE FROM documents WHERE car_id = ? AND doc_type = ?");
                $delStmt->bind_param("is", $car_id, $docLabel);
                $delStmt->execute();

                // Insert new doc
                $docStmt = $conn->prepare("INSERT INTO documents (car_id, doc_type, file_path, verified, uploaded_at) VALUES (?, ?, ?, 0, NOW())");
                $docStmt->bind_param("iss", $car_id, $docLabel, $path);
                $docStmt->execute();
            } else {
                throw new Exception("Failed to upload {$docLabel}.");
            }
        }
    }

    echo json_encode(['success' => true, 'msg' => 'Vehicle updated successfully!', 'car_id' => $car_id]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Failed to update vehicle: ' . $e->getMessage()]);
}
?>