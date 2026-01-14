<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'msg' => 'Method not allowed']);
    exit;
}

// 1. Collect Data
$brand = $_POST['brand'] ?? '';
$model = $_POST['model'] ?? '';
$category = $_POST['category'] ?? '';
$seating = intval($_POST['seating'] ?? 0);
$plate = $_POST['plate_number'] ?? '';
$location = $_POST['location'] ?? '';

// 2. Start Transaction
$conn->begin_transaction();

try {
    // Insert Main Vehicle Info
    $stmt = $conn->prepare("INSERT INTO cars (brand, model, plate_number, category, seating, location, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'live', NOW())");
    $stmt->bind_param("sssis s", $brand, $model, $plate, $category, $seating, $location);
    $stmt->execute();
    $car_id = $conn->insert_id;

    // 3. Define the 5 separate file inputs
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
            $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
            $newName = time() . "_{$inputName}_{$car_id}.{$ext}";
            
            if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $newName)) {
                $path = 'uploads/cars/' . $newName;
                $docStmt = $conn->prepare("INSERT INTO documents (car_id, doc_type, file_path, verified, uploaded_at) VALUES (?, ?, ?, 0, NOW())");
                $docStmt->bind_param("iss", $car_id, $docLabel, $path);
                $docStmt->execute();
            }
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'msg' => 'Vehicle and all documents saved!']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
}
?>