<?php
// public/pages/cars.php
$page_title = "Available Cars";

// Get parameters from URL
$pickup_date = $_GET['pickup'] ?? '';
$return_date = $_GET['return'] ?? '';
$category_filter = $_GET['category'] ?? '';
$location_filter = $_GET['location'] ?? '';

// Fetch cars from database
$cars = [];
$db_error = '';
if (isset($conn)) {
    // Build base query
    $sql = "SELECT c.*,
                   CONCAT(c.brand, ' ', c.model) AS name,
                   c.seating AS seats,
                   c.fuel_type AS fuel,
                   c.tier4_daily AS price,
                   (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
            FROM cars c
            WHERE c.status = 'live'";

    $params = [];
    $types = "";

    // Add date availability condition if dates are provided
    if (!empty($pickup_date) && !empty($return_date)) {
        $sql .= " AND c.id NOT IN (
                      SELECT DISTINCT b.car_id
                      FROM bookings b
                      WHERE b.status != 'cancelled'
                        AND (STR_TO_DATE(?, '%Y-%m-%d %H:%i') <= b.end_date AND STR_TO_DATE(?, '%Y-%m-%d %H:%i') >= b.start_date)
                  )";
        $params[] = $pickup_date;
        $params[] = $return_date;
        $types .= "ss";
    }

    // Add category filter if provided
    if (!empty($category_filter)) {
        $sql .= " AND c.category = ?";
        $params[] = $category_filter;
        $types .= "s";
    }

    // Add location filter if provided
    if (!empty($location_filter)) {
        $sql .= " AND c.location = ?";
        $params[] = $location_filter;
        $types .= "s";
    }

    $sql .= " ORDER BY c.id DESC";

    try {
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result === false) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        } else {
            $result = $conn->query($sql);
            if ($result === false) {
                throw new Exception("Query failed: " . $conn->error);
            }
        }

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $cars[] = $row;
            }
        }
    } catch (Exception $e) {
        $db_error = "Query failed: " . $e->getMessage();
    }
} else {
    $db_error = "Database connection failed. Check config.php";
}
?>


<div class="page-container" style="padding: 6rem 2rem; min-height: 60vh;">
    <!-- Search Box for Date Selection -->
    <div class="search-box" style="margin-bottom: 3rem;">
        <form class="search-form" onsubmit="searchCarsFromFleet(event)">
            <div class="form-group">
                <label>Pickup</label>
                <div class="date-trigger" onclick="openDateModal('pickup')">
                    <span id="pickupDisplay"><?php echo !empty($pickup_date) ? htmlspecialchars(date('M j, Y g:i A', strtotime($pickup_date))) : 'Date & Time'; ?></span>
                    <i class="fa-regular fa-calendar"></i>
                </div>
                <input type="hidden" id="pickupDateValue" value="<?php echo htmlspecialchars($pickup_date); ?>">
            </div>

            <div class="form-group">
                <label>Return</label>
                <div class="date-trigger" onclick="openDateModal('return')">
                    <span id="returnDisplay"><?php echo !empty($return_date) ? htmlspecialchars(date('M j, Y g:i A', strtotime($return_date))) : 'Date & Time'; ?></span>
                    <i class="fa-regular fa-calendar"></i>
                </div>
                <input type="hidden" id="returnDateValue" value="<?php echo htmlspecialchars($return_date); ?>">
            </div>

            <div class="form-group">
                <label>Type</label>
                <select class="form-control" id="categoryFilter">
                    <option value="">All Types</option>
                    <option value="Sedan" <?php echo (isset($_GET['category']) && $_GET['category'] === 'Sedan') ? 'selected' : ''; ?>>Sedan</option>
                    <option value="SUV" <?php echo (isset($_GET['category']) && $_GET['category'] === 'SUV') ? 'selected' : ''; ?>>SUV</option>
                    <option value="Van" <?php echo (isset($_GET['category']) && $_GET['category'] === 'Van') ? 'selected' : ''; ?>>Van</option>
                </select>
            </div>

            <div class="form-group">
                <label>Where To</label>
                <select class="form-control" id="locationFilter">
                    <option value="">All Areas</option>
                    <option value="Quezon City" <?php echo (isset($_GET['location']) && $_GET['location'] === 'Quezon City') ? 'selected' : ''; ?>>Quezon City</option>
                    <option value="Manila" <?php echo (isset($_GET['location']) && $_GET['location'] === 'Manila') ? 'selected' : ''; ?>>Manila</option>
                    <option value="Makati" <?php echo (isset($_GET['location']) && $_GET['location'] === 'Makati') ? 'selected' : ''; ?>>Makati</option>
                    <option value="Pasig" <?php echo (isset($_GET['location']) && $_GET['location'] === 'Pasig') ? 'selected' : ''; ?>>Pasig</option>
                    <option value="Bulacan" <?php echo (isset($_GET['location']) && $_GET['location'] === 'Bulacan') ? 'selected' : ''; ?>>Bulacan</option>
                </select>
            </div>

            <div class="form-group submit-group">
                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

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

<script>
// Function to handle search from the fleet page
function searchCarsFromFleet(e) {
    e.preventDefault();

    const pickupDate = document.getElementById('pickupDateValue').value;
    const returnDate = document.getElementById('returnDateValue').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const locationFilter = document.getElementById('locationFilter').value;

    if (!pickupDate || !returnDate) {
        const triggers = document.querySelectorAll('.date-trigger');
        triggers.forEach(trigger => {
            const inputId = trigger.nextElementSibling.id;
            if (!document.getElementById(inputId).value) {
                trigger.classList.add('input-error');
                setTimeout(() => trigger.classList.remove('input-error'), 500);
            }
        });
        return;
    }

    // Build URL with all search parameters
    let url = `?page=cars&pickup=${encodeURIComponent(pickupDate)}&return=${encodeURIComponent(returnDate)}`;

    if (categoryFilter) {
        url += `&category=${encodeURIComponent(categoryFilter)}`;
    }

    if (locationFilter) {
        url += `&location=${encodeURIComponent(locationFilter)}`;
    }

    window.location.href = url;
}
</script>
