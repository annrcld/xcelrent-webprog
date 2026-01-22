<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Method not allowed']);
    exit;
}

// 1. Collect Main Vehicle Data (including fuel_type)
$brand = trim($_POST['brand'] ?? '');
$model = trim($_POST['model'] ?? '');
$category = trim($_POST['category'] ?? '');
$fuel_type = trim($_POST['fuel_type'] ?? '');
$driver_type = trim($_POST['driver_type'] ?? '');
$transmission = trim($_POST['transmission'] ?? '');
$seating = intval($_POST['seating'] ?? 0);
$plate = strtoupper(trim($_POST['plate_number'] ?? '')); // Auto-uppercase plate number
$location = trim($_POST['location'] ?? '');

// 2. Collect Pricing Tiers
$tier1_12hrs = floatval($_POST['tier1_12hrs'] ?? 0);
$tier1_24hrs = floatval($_POST['tier1_24hrs'] ?? 0);
$tier2_12hrs = floatval($_POST['tier2_12hrs'] ?? 0);
$tier2_24hrs = floatval($_POST['tier2_24hrs'] ?? 0);
$tier3_24hrs = floatval($_POST['tier3_24hrs'] ?? 0);
$tier4_daily = floatval($_POST['tier4_daily'] ?? 0);

// 3. Basic Validation
if (empty($brand) || empty($model) || empty($category) || empty($fuel_type) || empty($driver_type) ||
    empty($plate) || empty($location) || $seating <= 0) {
    echo json_encode(['success' => false, 'msg' => 'Please fill in all required vehicle fields.']);
    exit;
}

// Validate pricing > 0
if ($tier1_12hrs <= 0 || $tier1_24hrs <= 0 ||
    $tier2_12hrs <= 0 || $tier2_24hrs <= 0 ||
    $tier3_24hrs <= 0 || $tier4_daily <= 0) {
    echo json_encode(['success' => false, 'msg' => 'All pricing fields must be greater than zero.']);
    exit;
}

// 4. Start Transaction
$conn->begin_transaction();

try {
    // Insert Main Vehicle Info + Pricing Tiers
    $sql = "INSERT INTO cars (
        brand, model, plate_number, category, fuel_type, transmission, driver_type, seating, location, status,
        tier1_12hrs, tier1_24hrs, tier2_12hrs, tier2_24hrs, tier3_24hrs, tier4_daily,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'live', ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);

    // Bind: 8 strings (ssssssis) + 1 integer (i) + 6 doubles (dddddd) = 15 total
    $stmt->bind_param(
        "sssssssisdddddd",
        $brand, $model, $plate, $category, $fuel_type, $transmission, $driver_type, $seating, $location,
        $tier1_12hrs, $tier1_24hrs, $tier2_12hrs, $tier2_24hrs, $tier3_24hrs, $tier4_daily
    );

    $stmt->execute();
    $car_id = $conn->insert_id;

    // 5. Handle Document Uploads
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
            // Allow only safe extensions
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                throw new Exception("Invalid file type for {$docLabel}.");
            }
            $newName = time() . "_{$inputName}_{$car_id}.{$ext}";
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath)) {
                $path = 'uploads/cars/' . $newName;
                $docStmt = $conn->prepare("INSERT INTO documents (car_id, doc_type, file_path, verified, uploaded_at) VALUES (?, ?, ?, 0, NOW())");
                $docStmt->bind_param("iss", $car_id, $docLabel, $path);
                $docStmt->execute();
            } else {
                throw new Exception("Failed to upload {$docLabel}.");
            }
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'msg' => 'Vehicle and all documents saved successfully!', 'car_id' => $car_id]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Add Car Error: " . $e->getMessage()); // Optional: log to server
    echo json_encode(['success' => false, 'msg' => 'Failed to save vehicle: ' . $e->getMessage()]);
}
?>