<?php
// Quick DB test script — adjust port if needed
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "xcelrent_car_rental";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";

$result = $conn->query("SELECT * FROM cars");
echo "<br><br>";
echo "<h3>Cars in Database:</h3>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo htmlspecialchars($row['brand']) . " " . htmlspecialchars($row['model']) . " - " . htmlspecialchars($row['plate_number']) . "<br>";
    }
} else {
    echo "No cars found in database.";
}

$conn->close();
?>