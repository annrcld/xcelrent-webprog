<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

// Get all pending car applications with operator details
$sql = "
    SELECT c.id as car_id, c.brand, c.model, c.plate_number, c.category, c.seating, c.fuel_type, c.transmission, c.driver_type, c.location, c.created_at as car_created_at,
           o.id as operator_id, o.company_name, o.contact_name, o.email, COALESCE(o.phone, u.phone) as phone
    FROM cars c
    INNER JOIN operators o ON c.operator_id = o.id
    LEFT JOIN users u ON o.email = u.email  -- Join with users table to get phone if not in operators
    WHERE c.status = 'pending'  -- Only show cars pending approval
    ORDER BY c.created_at DESC
";

$result = $conn->query($sql);
$applications = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $applications]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>