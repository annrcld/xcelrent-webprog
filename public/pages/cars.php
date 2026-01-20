<?php
// public/pages/cars.php
$page_title = "Available Cars";
include __DIR__ . '/../includes/header.php'; // Uses absolute path

// Fetch cars from database
$cars = [];
$db_error = '';
if (isset($conn)) {
    $sql = "SELECT c.*, 
                   CONCAT(c.brand, ' ', c.model) AS name, 
                   c.seating AS seats, 
                   c.fuel_type AS fuel, 
                   c.tier4_daily AS price,
                   (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
            FROM cars c 
            WHERE c.status = 'live' 
            ORDER BY c.id DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
    } else {
        $db_error = "Query failed: " . $conn->error;
    }
} else {
    $db_error = "Database connection failed. Check config.php";
}
?>

<nav>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='home.php'">Xcelrent<span class="dot">.</span></div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="cars.php" class="active">Book</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
             <div class="divider-vertical"></div>
            <div class="nav-auth-group">
                <button class="btn btn-text" onclick="openModal('operatorModal')">Be an Operator</button>
                <button class="btn btn-primary" onclick="openModal('loginModal')">Sign In</button>
            </div>
        </div>
        <div class="mobile-menu-btn"><i class="fa-solid fa-bars"></i></div>
    </div>
</nav>

<div class="page-container" style="padding: 6rem 2rem; min-height: 60vh;">
    <div class="results-header" style="text-align: center; margin-bottom: 3rem;">
        <h2 class="section-heading">Our Fleet</h2>
        <p class="section-subheading">Select the perfect vehicle for your trip</p>
    </div>

    <div class="cars-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto;">
        <?php if (!empty($db_error)): ?>
            <div style="grid-column: 1/-1; text-align: center; color: red; padding: 2rem; background: #fff0f0; border-radius: 8px;">
                <p><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></p>
            </div>
        <?php elseif (empty($cars)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 2rem;">
                <p>No vehicles found at the moment. Please check back later.</p>
            </div>
        <?php else: ?>
            <?php foreach ($cars as $car): ?>
                <div class="car-card" style="border: 1px solid #eee; border-radius: 12px; overflow: hidden; transition: transform 0.2s; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="car-image-wrapper" style="height: 200px; overflow: hidden;">
                        <img src="/project_xcelrent/public/<?php echo htmlspecialchars($car['image'] ?? 'assets/img/default_car.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($car['name'] ?? 'Car'); ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="car-details" style="padding: 1.5rem;">
                        <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem;"><?php echo htmlspecialchars($car['name'] ?? 'Unknown Model'); ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                            <i class="fa-solid fa-user" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($car['seats'] ?? '4'); ?> Seats &nbsp;|&nbsp; 
                            <i class="fa-solid fa-gas-pump" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($car['fuel'] ?? 'Gas'); ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 1.2rem; color: #22c55e;">₱<?php echo number_format($car['price'] ?? 0); ?>/day</span>
                            <button class="btn btn-primary" onclick="viewCar(<?php echo $car['id']; ?>)">View</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
include __DIR__ . '/../includes/footer.php';
?>