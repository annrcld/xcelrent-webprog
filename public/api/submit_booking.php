<?php
// public/api/submit_booking.php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$car_id = $_POST['car_id'] ?? null;
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$pickup_location = $_POST['pickup_location'] ?? '';
$return_location = $_POST['return_location'] ?? '';
$pickup_date = $_POST['pickup_date'] ?? '';
$return_date = $_POST['return_date'] ?? '';
$special_requests = $_POST['special_requests'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// Validate required fields
if (!$car_id || !$first_name || !$last_name || !$email || !$phone || 
    !$pickup_location || !$return_location || !$pickup_date || !$return_date || !$payment_method) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

// Validate file upload
if (!isset($_FILES['proof_of_payment']) || $_FILES['proof_of_payment']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Proof of payment is required']);
    exit;
}

// Handle file upload
$file = $_FILES['proof_of_payment'];
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG and PNG images are allowed']);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = __DIR__ . '/../uploads/payment_proofs/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate unique filename
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = 'proof_' . time() . '_' . uniqid() . '.' . $file_extension;
$upload_path = $upload_dir . $new_filename;
$db_path = 'uploads/payment_proofs/' . $new_filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    exit;
}

// Calculate rental days
$start = new DateTime($pickup_date);
$end = new DateTime($return_date);
$rental_days = max(1, $start->diff($end)->days);

// Get car details for pricing
$stmt = $conn->prepare("SELECT tier4_daily FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    echo json_encode(['success' => false, 'message' => 'Car not found']);
    exit;
}

$daily_rate = $car['tier4_daily'];
$total_amount = $daily_rate * $rental_days;

// Insert booking into database
$stmt = $conn->prepare("INSERT INTO bookings (car_id, renter_first_name, renter_last_name, renter_email, renter_phone, 
                         pickup_location, return_location, start_date, end_date, rental_days, daily_rate, total_amount, 
                         payment_method, proof_of_payment, special_requests, status, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

$stmt->bind_param("issssssssiddsss", 
    $car_id, 
    $first_name, 
    $last_name, 
    $email, 
    $phone, 
    $pickup_location, 
    $return_location, 
    $pickup_date, 
    $return_date, 
    $rental_days, 
    $daily_rate, 
    $total_amount, 
    $payment_method, 
    $db_path, 
    $special_requests
);

if ($stmt->execute()) {
    $booking_id = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Booking submitted successfully',
        'booking_id' => $booking_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save booking: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>