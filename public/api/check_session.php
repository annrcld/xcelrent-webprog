<?php
// public/api/check_session.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/config.php';

// Check if user is logged in by checking session
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

if ($isLoggedIn) {
    echo json_encode([
        'isLoggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'first_name' => $_SESSION['user_first_name'],
            'last_name' => $_SESSION['user_last_name']
        ]
    ]);
} else {
    echo json_encode([
        'isLoggedIn' => false
    ]);
}
?>