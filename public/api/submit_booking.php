<?php
// public/api/submit_booking.php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila'); // Set to Philippines timezone (UTC+8)
session_start();
require_once __DIR__ . '/../includes/config.php';

// Check if user is logged in (Crucial for the user_id foreign key)
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not logged in. Please log in to complete booking.']);
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
$pickup_date_raw = $_POST['pickup_date'] ?? '';
$return_date_raw = $_POST['return_date'] ?? '';
$special_requests = $_POST['special_requests'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// 1. Convert Date strings to MySQL format (YYYY-MM-DD HH:MM:SS) with timezone handling
// Handle different date formats that might come from JavaScript
// If the date string ends with 'Z', it's in UTC; otherwise, treat as local time
if (substr($pickup_date_raw, -1) === 'Z') {
    // Date is in UTC, convert to Philippines time
    $pickup_datetime = new DateTime($pickup_date_raw, new DateTimeZone('UTC'));
    $pickup_datetime->setTimezone(new DateTimeZone('Asia/Manila'));
} else {
    // Date is in local time, interpret as Philippines time
    $pickup_datetime = new DateTime($pickup_date_raw, new DateTimeZone('Asia/Manila'));
}

if (substr($return_date_raw, -1) === 'Z') {
    // Date is in UTC, convert to Philippines time
    $return_datetime = new DateTime($return_date_raw, new DateTimeZone('UTC'));
    $return_datetime->setTimezone(new DateTimeZone('Asia/Manila'));
} else {
    // Date is in local time, interpret as Philippines time
    $return_datetime = new DateTime($return_date_raw, new DateTimeZone('Asia/Manila'));
}

$start_date = $pickup_datetime->format('Y-m-d H:i:s');
$end_date = $return_datetime->format('Y-m-d H:i:s');

// Validate required fields
if (!$car_id || !$first_name || !$last_name || !$email || !$phone || 
    !$pickup_location || !$return_location || !$pickup_date_raw || !$return_date_raw || !$payment_method) {
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

// Calculate rental days with timezone awareness
$start_dt = new DateTime($start_date, new DateTimeZone('Asia/Manila'));
$end_dt = new DateTime($end_date, new DateTimeZone('Asia/Manila'));
$rental_days = max(1, $start_dt->diff($end_dt)->days);

// Get car details for pricing
$stmt_car = $conn->prepare("SELECT tier4_daily FROM cars WHERE id = ?");
$stmt_car->bind_param("i", $car_id);
$stmt_car->execute();
$car_result = $stmt_car->get_result();
$car = $car_result->fetch_assoc();

if (!$car) {
    echo json_encode(['success' => false, 'message' => 'Car not found']);
    exit;
}

$daily_rate = $car['tier4_daily'];
$reservation_fee = 500; 
$total_amount = ($daily_rate * $rental_days) - $reservation_fee;

// 2. Insert booking into database matching your EXACT schema
// Types: i (int), s (string), d (double/decimal)
$sql = "INSERT INTO bookings (
    car_id, user_id, start_date, end_date, total_amount,
    renter_first_name, renter_last_name, renter_email, renter_phone,
    pickup_location, return_location, rental_days, daily_rate,
    payment_method, proof_of_payment, special_requests, status, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($sql);

// Bind mapping: iissd sssss s i d s s s
$stmt->bind_param("iissdsssssssidss",
    $car_id,
    $user_id,
    $start_date,
    $end_date,
    $total_amount,
    $first_name,
    $last_name,
    $email,
    $phone,
    $pickup_location,
    $return_location,
    $rental_days,
    $daily_rate,
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