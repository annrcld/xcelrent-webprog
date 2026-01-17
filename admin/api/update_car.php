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
$seating = intval($_POST['seating'] ?? 0);
$plate = trim($_POST['plate_number'] ?? '');
$location = trim($_POST['location'] ?? '');

// Pricing
$tier1_12hrs = floatval($_POST['tier1_12hrs'] ?? 0);
$tier1_24hrs = floatval($_POST['tier1_24hrs'] ?? 0);
$tier2_12hrs = floatval($_POST['tier2_12hrs'] ?? 0);
$tier2_24hrs = floatval($_POST['tier2_24hrs'] ?? 0);
$tier3_24hrs = floatval($_POST['tier3_24hrs'] ?? 0);
$tier4_daily = floatval($_POST['tier4_daily'] ?? 0);

// Validate
if (empty($brand) || empty($model) || empty($category) || empty($fuel_type) || 
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
        brand = ?, model = ?, plate_number = ?, category = ?, fuel_type = ?, seating = ?, location = ?,
        tier1_12hrs = ?, tier1_24hrs = ?, tier2_12hrs = ?, tier2_24hrs = ?, tier3_24hrs = ?, tier4_daily = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssisddddddi",
        $brand, $model, $plate, $category, $fuel_type, $seating, $location,
        $tier1_12hrs, $tier1_24hrs, $tier2_12hrs, $tier2_24hrs, $tier3_24hrs, $tier4_daily,
        $car_id
    );

    $stmt->execute();

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