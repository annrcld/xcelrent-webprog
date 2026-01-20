<?php
// Common functions for Xcelrent Car Rental

/**
 * Sanitize user input
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate a unique ID
 */
function generate_unique_id() {
    return uniqid(date('ymdHis') . '_', true);
}

/**
 * Format currency
 */
function format_currency($amount, $currency = DEFAULT_CURRENCY) {
    switch ($currency) {
        case 'PHP':
            return '₱' . number_format($amount, 2);
        case 'USD':
            return '$' . number_format($amount, 2);
        default:
            return $amount;
    }
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (Philippines format)
 */
function validate_phone($phone) {
    // Basic validation for Philippine phone numbers
    return preg_match('/^(09|\+639)\d{9}$/', $phone);
}

/**
 * Send email notification
 */
function send_notification($to, $subject, $message) {
    // TODO: Implement proper email sending mechanism
    // This is a placeholder function
    $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Hash password
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect user to specific page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Get current user info
 */
function get_current_user() {
    if (isset($_SESSION['user_id'])) {
        return $_SESSION;
    }
    return null;
}

/**
 * Log activity
 */
function log_activity($activity, $user_id = null) {
    // TODO: Implement logging mechanism
    // This is a placeholder function
    $timestamp = date('Y-m-d H:i:s');
    $user = $user_id ?: ($_SESSION['user_id'] ?? 'Guest');
    error_log("[$timestamp] User: $user | Activity: $activity\n", 3, "logs/activity.log");
}
?>