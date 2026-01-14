<?php
// ============================================
// Admin Panel - Main Entry Point
// ============================================

// 1. Core Setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Load Configuration (Includes session_start() and DB connection)
require_once __DIR__ . '/includes/config.php';

// 3. AUTHENTICATION CHECK - DO NOT REMOVE
// This ensures only logged-in admins can see the dashboard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 4. Page Routing Logic
$page = isset($_GET['page']) ? basename($_GET['page']) : 'dashboard';
$allowed_pages = ['dashboard', 'add_car', 'manage_cars', 'bookings', 'renters', 'operators'];

// Validate page exists
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// 5. Build the UI
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

echo '<main class="main-content">';
    include __DIR__ . '/pages/' . $page . '.php';
echo '</main>';

include __DIR__ . '/includes/footer.php';
?>