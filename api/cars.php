<?php
// api/cars.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Sample car data (this would typically come from a database)
$cars = [
    [
        'id' => 1,
        'name' => "Toyota Vios 2026",
        'type' => "Automatic",
        'seats' => 5,
        'fuel' => "Gasoline",
        'price' => 1500,
        'isPopular' => true,
        'img' => "https://imgcdn.zigwheels.ph/large/gallery/exterior/30/3013/toyota-vios-2022-front-side-view-695271.jpg"
    ],
    [
        'id' => 2,
        'name' => "Isuzu Sportivo X 2014",
        'type' => "Automatic",
        'seats' => 7,
        'fuel' => "Diesel",
        'price' => 1799,
        'isPopular' => true,
        'img' => "https://imgcdn.zigwheels.ph/medium/gallery/exterior/13/89/isuzu-crosswind-front-angle-low-view-949250.jpg"
    ],
    [
        'id' => 3,
        'name' => "Toyota Innova 2026",
        'type' => "Automatic",
        'seats' => 7,
        'fuel' => "Diesel",
        'price' => 3500,
        'isPopular' => false,
        'img' => "https://imgcdn.zigwheels.ph/medium/gallery/exterior/30/1108/toyota-innova-64464.jpg"
    ]
];

// Return the car data as JSON
echo json_encode($cars);
?>
