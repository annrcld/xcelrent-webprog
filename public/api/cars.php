<?php
// API endpoint for car rental operations
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection with error handling
try {
    require_once '../includes/config.php';
    require_once '../includes/functions.php';

    // Check if database connection failed
    if (isset($db_error) && $db_error) {
        throw new Exception("Database connection failed: " . $db_error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration error: ' . $e->getMessage()]);
    exit;
}

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Since this is cars.php, we're specifically handling cars
handleCars($method);

function handleCars($method) {
    switch ($method) {
        case 'GET':
            // Return available cars
            $cars = [
                [
                    'id' => 1,
                    'name' => 'Toyota Vios 2026',
                    'type' => 'Automatic',
                    'seats' => 5,
                    'fuel' => 'Gasoline',
                    'price' => 1500,
                    'isPopular' => true,
                    'img' => 'https://imgcdn.zigwheels.ph/large/gallery/exterior/30/3013/toyota-vios-2022-front-side-view-695271.jpg'
                ],
                [
                    'id' => 2,
                    'name' => 'Isuzu Sportivo X 2014',
                    'type' => 'Automatic',
                    'seats' => 7,
                    'fuel' => 'Diesel',
                    'price' => '1,799',
                    'isPopular' => true,
                    'img' => 'https://imgcdn.zigwheels.ph/medium/gallery/exterior/13/89/isuzu-crosswind-front-angle-low-view-949250.jpg'
                ],
                [
                    'id' => 3,
                    'name' => 'Toyota Innova 2026',
                    'type' => 'Automatic',
                    'seats' => 7,
                    'fuel' => 'Diesel',
                    'price' => 3500,
                    'isPopular' => false,
                    'img' => 'https://imgcdn.zigwheels.ph/medium/gallery/exterior/30/1108/toyota-innova-64464.jpg'
                ]
            ];

            echo json_encode($cars);
            break;
        case 'POST':
            // Add a new car (requires authentication)
            $input = json_decode(file_get_contents('php://input'), true);

            // TODO: Implement car creation logic
            http_response_code(201);
            echo json_encode(['message' => 'Car added successfully']);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

?>