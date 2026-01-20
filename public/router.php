<?php
// public/router.php
session_start();
require_once 'includes/config.php';

// Define available pages
$available_pages = [
    'home' => 'pages/home.php',
    'about' => 'pages/about.php',
    'contact' => 'pages/contact.php',
    'cars' => 'pages/cars.php',
    'booking' => 'pages/booking.php',
    'payment' => 'pages/payment.php',
    'confirmation' => 'pages/confirmation.php'
];

// Get requested page
$page = $_GET['page'] ?? 'home';

// Validate page
if (!array_key_exists($page, $available_pages) || !file_exists($available_pages[$page])) {
    $page = 'home';
}

// Set page title based on requested page
$page_titles = [
    'home' => 'Home',
    'about' => 'About Us',
    'contact' => 'Contact',
    'cars' => 'Available Cars',
    'booking' => 'Book a Car',
    'payment' => 'Payment',
    'confirmation' => 'Booking Confirmation'
];

$page_title = $page_titles[$page] ?? 'Xcelrent Car Rental';

include 'includes/header.php';
include $available_pages[$page];
include 'includes/footer.php';
?>